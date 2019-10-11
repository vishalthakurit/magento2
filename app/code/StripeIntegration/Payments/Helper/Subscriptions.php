<?php

namespace StripeIntegration\Payments\Helper;

use StripeIntegration\Payments\Helper\Logger;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use StripeIntegration\Payments\Exception\SCANeededException;
use Magento\Framework\Exception\LocalizedException;

class Subscriptions
{
    public $couponCodes = [];
    public $subscriptions = [];
    public $invoices = [];
    public $paymentIntents = [];

    // If you have a shipping module that can calculate the shipping cost of an individual item instead of the entire order,
    // you can overwrite this plugin and set $allowMixedCarts = true
    // You will also need to overwrite the getShippingExclTax() method in Helper/Subscriptions.php
    public $allowMixedCarts = false;

    public function __construct(
        \StripeIntegration\Payments\Model\Rollback $rollback,
        \StripeIntegration\Payments\Helper\Generic $paymentsHelper,
        \StripeIntegration\Payments\Model\Config $config,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \StripeIntegration\Payments\Model\StripeCustomer $customer,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Tax\Model\Sales\Order\TaxManagement $taxManagement
    ) {
        $this->rollback = $rollback;
        $this->paymentsHelper = $paymentsHelper;
        $this->config = $config;
        $this->priceCurrency = $priceCurrency;
        $this->eventManager = $eventManager;
        $this->customer = $customer;
        $this->cache = $cache;
        $this->taxManagement = $taxManagement;
    }

    public function createSubscriptions($order, $isDryRun = false, $trialEnd = null)
    {
        $this->_subscriptionsTotal = 0;
        $this->_isDryRun = $isDryRun;
        $this->_piSecrets = [];
        $this->_createdSubscriptions = [];
        $this->_error = null;

        // Get all the products on the order
        $items = $order->getAllItems();
        foreach ($items as $item)
        {
            $product = $this->paymentsHelper->loadProductById($item->getProductId());
            if ($product->getStripeSubEnabled())
            {
                try
                {
                    $this->createSubscriptionForProduct($product, $order, $item, $isDryRun, $trialEnd);
                }
                catch (\Stripe\Error\Card $e)
                {
                    $this->rollback->run($e->getMessage(), $e);
                }
                catch (\Stripe\Error $e)
                {
                    $this->rollback->run($e->getMessage(), $e);
                }
                catch (\Exception $e)
                {
                    // We get a \Stripe\Error\InvalidRequest if the customer is purchasing a subscription with a currency
                    // that is different from the currency they used for previous subscription purposes
                    $message = $e->getMessage();
                    if (preg_match('/with currency (\w+)$/', $message, $matches))
                    {
                        $currency = strtoupper($matches[1]);
                        $this->rollback->run("Your account has been configured to use a different currency. Please complete the purchase in the currency: $currency", $e);
                    }
                    else
                        $this->rollback->run("Sorry, we could not create the subscription for " . $product->getName() . ". Please contact us for more help.", $e);
                }
            }
        }

        return [
            "subscriptionsTotal" => $this->_subscriptionsTotal,
            "piSecrets" => $this->_piSecrets,
            "createdSubscriptions" => $this->_createdSubscriptions,
            "error" => $this->_error
        ];
    }

    // If you have a shipping module that can calculate the shipping cost of an individual item instead of the entire order,
    // you can overwrite this method to return the individual subscription's shipping cost.
    // You will also need to overwrite the StripeIntegration\Payments\Plugin\Cart\Before plugin
    public function getShippingExclTax($order, $item, $isDryRun)
    {
        if ($isDryRun)
            $shippingAmount = $order->getShippingAddress()->getShippingAmount();
        else
            $shippingAmount = $order->getShippingAmount();
        // $shippingTax = $order->getShippingTaxAmount();
        // $shippingCostIncludingTax = $order->getShippingInclTax();

        return $shippingAmount;
    }

    public function getBaseShippingExclTax($order, $item, $isDryRun)
    {
        if ($isDryRun)
            $shippingAmount = $order->getShippingAddress()->getBaseShippingAmount();
        else
            $shippingAmount = $order->getBaseShippingAmount();
        // $shippingBaseTax = $order->getBaseShippingTaxAmount();
        // $shippingBaseCostIncludingTax = $order->getBaseShippingInclTax();

        return $shippingAmount;
    }

    public function getQuote()
    {
        $quote = $this->paymentsHelper->getQuote();
        $createdAt = $quote->getCreatedAt();
        if (empty($createdAt)) // case of admin orders
        {
            $quoteId = $quote->getQuoteId();
            $quote = $this->paymentsHelper->loadQuoteById($quoteId);
        }
        return $quote;
    }

    public function getShippingTax($paramName = "percent")
    {
        $quote = $this->getQuote();
        if ($quote->getIsVirtual())
            return 0;

        $address = $quote->getShippingAddress();
        $address->collectShippingRates();

        $taxes = $address->getItemsAppliedTaxes();

        if (!is_array($taxes['shipping']))
            return 0;

        foreach ($taxes['shipping'] as $tax)
        {
            if ($tax['item_type'] == "shipping")
                return $tax[$paramName];
        }

        return 0;
    }

    public function chargeShippingRecurringly()
    {
        $setting = $this->config->getConfigData("shipping", "subscriptions");
        return ($setting == "add_to_subscription");
    }

    public function chargeShippingOnlyOnce()
    {
        return !$this->chargeShippingRecurringly();
    }

    public function getSubscriptionDetails($product, $order, $item, $isDryRun, $trialEnd)
    {
        // Get billing interval and billing period
        $interval = $product->getStripeSubInterval();
        $intervalCount = $product->getStripeSubIntervalCount();

        if (!$interval)
            throw new \Exception("An interval period has not been specified for the subscription");

        if (!$intervalCount)
            $intervalCount = 1;

        // If it is a configurable product, switch to the parent item
        if ($item->getPrice() == 0 && $item->getParentItem() &&
            $item->getParentItem()->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
        {
            $item = $item->getParentItem();
        }

        // Get the subscription currency and amount
        $initialFee = $product->getStripeSubInitialFee();

        if (!is_numeric($initialFee))
            $initialFee = 0;

        if ($this->config->useStoreCurrency())
        {
            $amount = $item->getPrice();

            if ($isDryRun)
            {
                $currency = $order->getQuoteCurrencyCode();
                $rate = $order->getBaseToQuoteRate();
            }
            else
            {
                $currency = $order->getOrderCurrencyCode();
                $rate = $order->getBaseToOrderRate();
            }

            // This seems to be a Magento multi-currency bug, tested in v2.3.2
            if (is_numeric($rate) && $rate > 0 && $rate != 1 && $item->getPrice() == $item->getBasePrice())
                $amount = round($amount * $rate, 2); // We fix it by doing the calculation ourselves

            if (is_numeric($rate) && $rate > 0)
                $initialFee = $initialFee * $rate;

            $shipping = $this->getShippingExclTax($order, $item, $isDryRun);
        }
        else
        {
            $amount = $item->getBasePrice();

            if ($isDryRun)
                $currency = $order->getBaseCurrencyCode();
            else
                $currency = $order->getBaseCurrencyCode();

            $shipping = $this->getBaseShippingExclTax($order, $item, $isDryRun);
        }

        if (!is_numeric($amount))
            $amount = 0;

        $cents = 100;
        if ($this->paymentsHelper->isZeroDecimal($currency))
            $cents = 1;

        if ($isDryRun)
            $qty = $item->getQty();
        else
            $qty = $item->getQtyOrdered();

        $params = [
            'name' => $item->getName(),
            'qty' => $qty,
            'interval' => $interval,
            'interval_count' => $intervalCount,
            'amount_magento' => $amount,
            'amount_stripe' => round($amount * $cents),
            'initial_fee_stripe' => round($initialFee * $cents),
            'initial_fee_magento' => $initialFee,
            'shipping_magento' => $shipping,
            'shipping_stripe' => round($shipping * $cents),
            'currency' => strtolower($currency),
            'tax_percent' => $item->getTaxPercent(),
            'tax_amount_item' => $item->getTaxAmount(), // already takes $qty into account
            'tax_amount_shipping' => $this->getShippingTax("amount"),
            'trial_end' => null,
            'trial_days' => 0,
            'coupon_code' => null,
        ];

        $trialDays = 0;
        if (!$trialEnd)
        {
            $trialDays = $product->getStripeSubTrial();
            if (!empty($trialDays) && is_numeric($trialDays) && $trialDays > 0)
            {
                $params['trial_end'] = strtotime("+$trialDays days");
                $params['trial_days'] = $trialDays;
            }
        }

        return $params;
    }

    public function createSubscriptionForProduct($product, $order, $item, $isDryRun, $trialEnd = null)
    {
        $profile = $this->getSubscriptionDetails($product, $order, $item, $isDryRun, $trialEnd);

        $this->_subscriptionsTotal +=
            ($profile['qty'] * $profile['initial_fee_magento']) +
            ($profile['qty'] * $profile['amount_magento']) +
            $profile['shipping_magento'] +
            $profile['tax_amount_shipping'] +
            $profile['tax_amount_item'];

        if ($this->_isDryRun)
            return;

        $metadata = $this->collectMetadata($profile, $product, $order, $item);

        $orderId = $order->getIncrementId();
        $itemId = $item->getQuoteItemId();
        $key = "{$order->getQuoteId()}_subscription_id_item_" . $itemId;
        $subscriptionId = $this->cache->load($key);

        try
        {
            // if it returns false, it means that the subscription was not created yet
            if ($this->confirm($subscriptionId, $profile, $metadata, $order))
                return;
        }
        catch (SCANeededException $e)
        {
            if ($this->paymentsHelper->isAdmin())
            {
                $this->paymentsHelper->addError("This card cannot be used because it requires a 3D Secure authentication by the customer.");
                throw new LocalizedException(__("This card cannot be used because it requires a 3D Secure authentication by the customer."));
            }

            return;
        }

        $this->createProduct($profile, $product);
        $planId = $this->generatePlanId($profile, $product);
        $plan = $this->createPlan($profile, $planId, $product);
        $customer = $this->createCustomer($profile, $order);
        $this->attachCustomerToPaymentMethod($customer, $order->getPayment()->getAdditionalInformation('token'));

        $this->collectInitialFee($customer, $profile, $orderId);
        $this->collectShipping($customer, $profile, $orderId);

        $subscription = $this->subscribeCustomer(
            $customer,
            $plan,
            $order->getPayment()->getAdditionalInformation('token'),
            $orderId,
            $profile,
            $metadata
        );

        $this->_createdSubscriptions[$key] = $subscription->id;

        try
        {
            $this->confirm($subscription->id, $profile, $metadata, $order);
        }
        catch (SCANeededException $e)
        {
            if ($this->paymentsHelper->isAdmin())
            {
                $this->paymentsHelper->addError("This card cannot be used because it requires a 3D Secure authentication by the customer.");
                throw new LocalizedException(__("This card cannot be used because it requires a 3D Secure authentication by the customer."));
            }

            return;
        }
    }

    public function isAdminSubscriptionSwitch($data)
    {
        return (is_array($data['subscription']) &&
            isset($data['subscription']['switch']) &&
            $data['subscription']['switch'] == 'switch');
    }

    public function formatSubscriptionName($sub)
    {
        if (empty($sub)) return "Unknown subscription";

        $name = $sub->plan->name;
        if (empty($name) && isset($sub->plan->product) && is_numeric($sub->plan->product))
        {
            $product = $this->paymentsHelper->loadProductById($sub->plan->product);
            if ($product && $product->getName())
                $name = $product->getName();
        }

        $currency = $sub->plan->currency;
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION;
        $cents = 100;
        $qty = '';

        if ($this->paymentsHelper->isZeroDecimal($currency))
        {
            $cents = 1;
            $precision = 0;
        }

        $amount = $sub->plan->amount / $cents;

        if ($sub->quantity > 1)
        {
            $qty = " x " . $sub->quantity;
        }

        $this->priceCurrency->getCurrency()->setCurrencyCode(strtoupper($currency));
        $cost = $this->priceCurrency->format($amount, false, $precision);

        return "$name ($cost$qty)";
    }

    public function generatePlanId($profile, $product)
    {
        // Validate the billing period
        switch ($profile['interval'])
        {
            case 'day':
            case 'week':
            case 'month':
            case 'year':
                break;
            default:
                $this->paymentsHelper->dieWithError("Could not complete subscription because of an invalid billing period unit!");
                break;
        }

        $amount = $profile['amount_stripe'] . $profile['currency'];
        $frequency = $profile['interval_count'] . strtoupper($profile['interval']) . ($profile['interval_count'] > 1 ? 'S' : '');

        $pieces = [
            'amount' => $amount,
            'frequency' => $frequency,
            'product' => $product->getId()
        ];

        return implode('-', $pieces);
    }

    public function createPlan($profile, $planId, $product)
    {
        try
        {
            $plan = \Stripe\Plan::retrieve($planId);
        }
        catch (\Exception $e)
        {
            $plan = \Stripe\Plan::create([
                "amount" => $profile['amount_stripe'],
                "interval" => $profile['interval'],
                "interval_count" => $profile['interval_count'],
                "product" => $product->getId(),
                "currency" => $profile['currency'],
                "id" => $planId
            ]);
        }

        return $plan;
    }

    public function createProduct($profile, $product)
    {
        try
        {
            $product = \Stripe\Product::retrieve($product->getId());
        }
        catch (\Exception $e)
        {
            // Product does not exist yet
            $product = \Stripe\Product::create([
                "id" => $product->getId(),
                "name" => $product->getName(),
                "type" => "service",
            ]);
        }

        return $product;
    }

    public function collectMetadata($profile, $product, $order, $item)
    {
        // Build the metadata for this subscription - the customer will be able to edit these in the future
        $metadata = [
            "Product ID" => $product->getId(),
            "Customer ID" => $this->customer->getCustomerId(),
            "Order #" => $order->getIncrementId(),
            "Module" => \StripeIntegration\Payments\Model\Config::$moduleName . " v" . \StripeIntegration\Payments\Model\Config::$moduleVersion
        ];
        $shipping = $this->paymentsHelper->getAddressFrom($order);
        if ($shipping)
        {
            $metadata["Shipping First Name"] = $shipping["firstname"];
            $metadata["Shipping Last Name"] = $shipping["lastname"];
            $metadata["Shipping Company"] = $shipping["company"];
            $metadata["Shipping Street"] = $shipping["street"];
            $metadata["Shipping City"] = $shipping["city"];
            $metadata["Shipping Region"] = $shipping["region"];
            $metadata["Shipping Postcode"] = $shipping["postcode"];
            $metadata["Shipping Country"] = $shipping["country_id"];
            $metadata["Shipping Telephone"] = $shipping["telephone"];
        }

        if ($profile['trial_days'] > 0)
            $metadata["Trial"] = $profile['trial_days'] . " days";

        // Event to collect additional metadata, use this in your own local module
        $returnObject = new \Magento\Framework\DataObject();
        $this->eventManager->dispatch('stripe_subscriptions_metadata', array(
            'product' => $product,
            'order' => $order,
            'item' => $item,
            'metadata' => $metadata,
            'returnObject' => $returnObject
        ));

        foreach ((array) $returnObject->getMetadata() as $key => $value)
            $metadata[$key] = $value;

        return $metadata;
    }

    protected function customerDetailsHaveChanged($customer, $params)
    {
        if (isset($params['cross_border_classification']) && $customer->cross_border_classification != $params['cross_border_classification'])
            return true;

        if (!isset($params['cross_border_classification']) && $customer->cross_border_classification == "export")
            return true;

        if (isset($params['address']['country']))
        {
            if (empty($customer->address->country))
                return true;

            if ($customer->address->country != $params['address']['country'])
                return true;
        }

        return false;
    }

    public function createCustomer($profile, $order)
    {
        $quote = $order->getQuote();
        $params = [];
        $crossBorderClassification = $this->paymentsHelper->getCrossBorderClassification($quote);

        if ($crossBorderClassification == "export")
        {
            $params["cross_border_classification"] = "export";
            $params["address"] = $this->paymentsHelper->getExportAddress($quote);
        }

        $customerStripeId = $this->customer->getStripeId();
        if (!$customerStripeId)
        {
            $customer = $this->customer->createStripeCustomer($order, $params);
        }
        else
        {
            $customer = $this->customer->retrieveByStripeID($customerStripeId);

            if (!$customer || $this->customerDetailsHaveChanged($customer, $params))
                $customer = $this->customer->createStripeCustomer($order, $params); // This should overwrite the Stripe customer ID association
        }

        return $customer;
    }

    public function attachCustomerToPaymentMethod($customer, $paymentMethodId)
    {
        try
        {
            $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
            if (!empty($paymentMethod->customer))
            {
                if ($paymentMethod->customer != $customer->id)
                    $this->paymentsHelper->dieWithError("Error: This card belongs to a different customer.");
            }
            else
                $paymentMethod->attach([ 'customer' => $customer->id ]);
        }
        catch (\Stripe\Error\Card $e)
        {
            $this->paymentsHelper->dieWithError($e->getMessage());
        }
    }

    public function collectInitialFee($customer, $profile, $realOrderId)
    {
        $initialFee = $profile['initial_fee_stripe'] * $profile['qty'];
        $currency = $profile['currency'];

        if ($initialFee && $initialFee > 0)
        {
            try
            {
                $taxRate = $this->retrieveTaxRate(0); // VAT for initial fees is not currently supported

                $params = array(
                    'customer' => $customer->id,
                    'amount' => $initialFee,
                    'currency' => $currency,
                    'description' => "Initial fee",
                    'tax_rates' => [$taxRate]
                );
                $invoiceItem = \Stripe\InvoiceItem::create($params);
            }
            catch (\Stripe\Error $e)
            {
                $this->paymentsHelper->dieWithError($e->getMessage(), $e);
            }
            catch (\Exception $e)
            {
                $this->paymentsHelper->dieWithError($e->getMessage(), $e);
            }
        }
    }

    public function collectShipping($customer, $profile, $realOrderId, $subscriptionId = null)
    {
        $shippingAmount = $profile['shipping_stripe'];
        $currency = $profile['currency'];
        $isNonRecurringShippingCost = empty($subscriptionId);
        $isRecurringShippingCost = !$isNonRecurringShippingCost;

        if ($shippingAmount && $shippingAmount > 0)
        {
            if ($isRecurringShippingCost && $this->chargeShippingOnlyOnce())
                return;

            if ($isNonRecurringShippingCost && $this->chargeShippingRecurringly() && $profile['trial_days'] > 0)
                return;

            try
            {
                $params = array(
                    'customer' => $customer->id,
                    'amount' => $shippingAmount,
                    'currency' => $currency,
                    'description' => "Shipping"
                );

                if ($subscriptionId)
                    $params['subscription'] = $subscriptionId;

                $taxPercent = $this->getShippingTax("percent");
                if ($taxPercent && is_numeric($taxPercent))
                    $params['tax_rates'] = [$this->retrieveTaxRate($taxPercent)];
                else
                    $params['tax_rates'] = [$this->retrieveTaxRate(0)];

                $invoiceItem = \Stripe\InvoiceItem::create($params);
            }
            catch (\Stripe\Error $e)
            {
                $this->paymentsHelper->dieWithError($e->getMessage(), $e);
            }
            catch (\Exception $e)
            {
                $this->paymentsHelper->dieWithError($e->getMessage(), $e);
            }
        }
    }

    public function subscribeCustomer($customer, $plan, $paymentMethodId, $orderId, $profile, $metadata)
    {
        $taxPercent = $profile['tax_percent'];
        $trialDays = $profile['trial_days'];
        $shipping = $profile['shipping_stripe'];
        $couponCode = $profile['coupon_code'];
        $qty = $profile['qty'];

        $params = [
            'customer' => $customer->id,
            'plan' => $plan->id,
            'quantity' => (int)$qty,
            'default_payment_method' => $paymentMethodId,
            'enable_incomplete_payments' => true,
            'metadata' => $metadata,
            'expand' => ['latest_invoice.payment_intent']
        ];

        if (is_numeric($taxPercent) && $taxPercent > 0)
            $params['default_tax_rates'] = [$this->retrieveTaxRate($taxPercent)];

        if ($this->isCouponCodeValid($couponCode))
            $params['coupon'] = $couponCode;

        if ($trialDays && $trialDays > 0)
            $params['trial_end'] = time() + $trialDays * 24 * 60 * 60;

        $subscription = \Stripe\Subscription::create($params);

        if ($shipping && $shipping > 0)
        {
            $this->collectShipping($customer, $profile, $orderId, $subscription->id);
        }

        $this->subscriptions[$subscription->id] = $subscription;
        $this->paymentIntents[$subscription->id] = $subscription->latest_invoice->payment_intent;

        return $subscription;
    }

    public function isCouponCodeValid($couponCode)
    {
        if (empty($couponCode))
            return false;

        if (isset($this->couponCodes[$couponCode]))
            return $this->couponCodes[$couponCode];

        try
        {
            $this->couponCodes[$couponCode] = false;
            \Stripe\Coupon::retrieve($couponCode);
            $this->couponCodes[$couponCode] = true;
        }
        catch (\Stripe\Error $e)
        {
            $this->paymentsHelper->dieWithError($e->getMessage(), $e);
        }
        catch (\Exception $e)
        {
            $this->paymentsHelper->dieWithError($e->getMessage(), $e);
        }

        return $this->couponCodes[$couponCode];
    }

    public function updatePaymentIntentFrom($paymentIntent, $profile, $metadata)
    {
        if ($profile['qty'] > 1)
            $qty = $profile['qty'] . " x ";
        else
            $qty = "";

        $name = $profile['name'];

        $paymentIntent->description = $qty . $name;
        $paymentIntent->metadata = $metadata;
        $paymentIntent->save();
    }

    public function retrieveSubscription($subscriptionId)
    {
        if (isset($this->subscriptions[$subscriptionId]))
            return $this->subscriptions[$subscriptionId];

        try
        {
            $this->subscriptions[$subscriptionId] = \Stripe\Subscription::retrieve([ 'id' => $subscriptionId, 'expand' => ['latest_invoice.payment_intent'] ]);
            return $this->subscriptions[$subscriptionId];
        }
        catch (\Exception $e)
        {
            // In the case we have an invalid subscription ID, recreate the subscription
            return false;
        }
    }

    public function retrievePaymentIntentFor($subscriptionId, $profile)
    {
        if (isset($this->paymentIntents[$subscriptionId]))
            return $this->paymentIntents[$subscriptionId];

        try
        {
            $subscription = $this->retrieveSubscription($subscriptionId);

            if (empty($subscription->latest_invoice->payment_intent))
                return null;

            $this->paymentIntents[$subscriptionId] = $subscription->latest_invoice->payment_intent;

            return $this->paymentIntents[$subscriptionId];
        }
        catch (\Exception $e)
        {
            Logger::log($e->getMessage());
            return null;
        }
    }

    public function retrieveTaxRate($percent)
    {
        if (isset($this->taxRates[(string)$percent]))
            return $this->taxRates[(string)$percent];

        $rates = \Stripe\TaxRate::all(["limit" => 100]);

        foreach ($rates as $rate)
        {
            $this->taxRates[(string)$rate->percentage] = $rate;

            if ($rate->percentage == $percent)
            {
                return $rate;
            }
        }

        $rate = \Stripe\TaxRate::create([
            "display_name" => 'VAT',
            "description" => "$percent% VAT",
            "percentage" => $percent,
            "inclusive" => false
        ]);
        $this->taxRates[(string)$percent] = $rate;
        return $rate;
    }

    // Returns true if we have an active subscription
    // Returns false if the subscription has not been created yet
    // Throws an Exception if the card has been declined
    // Throws an SCANeededException if authentication is needed
    public function confirm($subscriptionId, $profile, $metadata, $order)
    {
        if (empty($subscriptionId))
            return false;

        $subscription = $this->retrieveSubscription($subscriptionId);

        // In the case we have an invalid subscription ID, recreate the subscription
        if (empty($subscription))
            return false;

        $paymentIntent = $this->retrievePaymentIntentFor($subscriptionId, $profile);

        if (empty($paymentIntent))
        {
            if ($profile['trial_days'] > 0)
            {
                // If 3DS is needed, we should send a link to the customer upon trial-end https://stripe.com/docs/billing/invoices/hosted
                // @todo - should maximize our chances for exemptions by authorizing for $0 as per https://stripe.com/docs/billing/subscriptions/payment
                return true;
            }
            else
                $this->_error = __("Could not retrieve Payment Intent for subscription");

            throw new SCANeededException("Unknown Error");
        }

        $subscription = $this->subscriptions[$subscriptionId];

        if ($subscription->status == "active"  || $subscription->status == "trialing")
        {
            $this->updatePaymentIntentFrom($paymentIntent, $profile, $metadata);

            // In the case that this is the 2nd confirmation, Magento has increased the order incrementID, so update it on the subscription
            // If not updated, recurring invoicing will not work
            $orderId = $order->getIncrementId();
            if ($orderId != $subscription->metadata->{"Order #"})
            {
                $subscription->metadata->{"Order #"} = $orderId;
                $subscription->save();

                // At this point we would have lost the initial invoice.payment_succeeded webhooks event due to an order # mismatch, so generate it manually
                $this->invoiceSubscriptionOrder($subscription, $order);
            }

            return true;
        }

        // In theory it should only be requires_action
        if ($paymentIntent->status == "requires_action" || $paymentIntent->status == "requires_source_action")
        {
            $this->_piSecrets[$paymentIntent->id] = $paymentIntent->client_secret;
            throw new SCANeededException("Authentication Required");
        }

        return true;
    }

    public function invoiceSubscriptionOrder($subscription, $order)
    {
        // With trialing subscriptions we may not have an invoice yet
        if (empty($subscription->latest_invoice))
            return;
        $stripeInvoice = \Stripe\Invoice::retrieve($subscription->latest_invoice);
        $invoice = $order->prepareInvoice();
        $params = [
            "amount" => $stripeInvoice->amount_paid,
            "currency" => $stripeInvoice->currency
        ];
        $this->paymentsHelper->adjustInvoiceAmounts($invoice, $params);
        $invoice->register();
        $order->addRelatedObject($invoice);
    }


    public function confirmUpcomingInvoices($subscription, $profile)
    {
        if ($profile['trial_days'] == 0)
            return;

        if ($this->hasInitialFees($profile))
        {
            // At the moment, upcoming invoices do not have a Payment Intent and we cannot return the PI secret for authorization
            $this->_error = __("The subscription has a trial period but the card requires immediate authorization. Please use a different card.");
        }
    }

    public function hasInitialFees($profile)
    {
        if ($profile['initial_fee_stripe'] > 0)
            return true;

        if ($profile['shipping_stripe'] > 0 && $this->chargeShippingOnlyOnce())
            return true;
    }

    public function validateCartItems($addToCartProduct = null)
    {
        if ($this->allowMixedCarts)
            return;

        $cartItems = $this->getQuote()->getAllItems();
        $cartHasSubscriptions = false;
        $cartHasRegularProducts = false;
        $cartIsEmpty = true;

        foreach ($cartItems as $cartItem)
        {
            $cartIsEmpty = false;

            if ($cartItem->getPrice() == 0 && $cartItem->getParentItem() &&
                $cartItem->getParentItem()->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
            {
                $item = $cartItem->getParentItem();
            }
            else
                $item = $cartItem;

            $product = $this->paymentsHelper->loadProductById($item->getProduct()->getEntityId());

            if ($product->getStripeSubEnabled())
                $cartHasSubscriptions = true;
            else
                $cartHasRegularProducts = true;
        }

        if ($cartHasSubscriptions && $cartHasRegularProducts)
            throw new LocalizedException(__('Sorry, purchasing subscriptions together with regular products is not possible. Please buy your subscriptions separately.'));

        // This triggers when adding a product to the cart
        if ($addToCartProduct)
        {
            if (!$cartIsEmpty && $addToCartProduct->getStripeSubEnabled())
                throw new LocalizedException(__('Sorry, subscriptions must be purchased individually. Please buy your subscription separately.'));

            if ($cartHasSubscriptions)
                throw new LocalizedException(__('Sorry, subscriptions must be purchased individually. You already have a subscription in your shopping cart.'));
        }
    }
}

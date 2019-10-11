<?php

namespace StripeIntegration\Payments\Controller\Customer;

use StripeIntegration\Payments\Helper\Logger;

class Subscriptions extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $session,
        \StripeIntegration\Payments\Helper\Generic $helper,
        \Magento\Sales\Model\Order $order,
        \StripeIntegration\Payments\Model\StripeCustomer $stripeCustomer
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);

        $session = $session;
        $this->helper = $helper;
        $this->order = $order;
        $this->stripeCustomer = $stripeCustomer;

        if (!$session->isLoggedIn())
            $this->_redirect('customer/account/login');
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if (isset($params['viewOrder']))
            return $this->viewOrder($params['viewOrder']);
        else if (isset($params['cancel']))
            return $this->cancelSubscription($params['cancel']);
        else if (isset($params['edit']))
            return $this->editSubscription($params['edit'], $params['data']);
        else if (!empty($params))
            $this->_redirect('stripe/customer/subscriptions');

        return $this->resultPageFactory->create();
    }

    protected function viewOrder($incrementOrderId)
    {
        $this->order->loadByIncrementId($incrementOrderId);

        if ($this->order->getId())
            $this->_redirect('sales/order/view/order_id/' . $this->order->getId());
        else
        {
            $this->helper->addError("Order #$incrementOrderId could not be found!");
            $this->_redirect('stripe/customer/subscriptions');
        }
    }

    protected function cancelSubscription($subscriptionId)
    {
        try
        {
            if (!$this->stripeCustomer->getStripeId())
                throw new \Exception("Could not load customer account for subscription with ID $subscriptionId!");

            $subscription = $this->stripeCustomer->getSubscription($subscriptionId);
            $subscription->cancel();
            $this->helper->addSuccess("Subscription <b>{$subscription->plan->name}</b> has been canceled!");
        }
        catch (\Stripe\Error $e)
        {
            $this->helper->addError($e->getMessage());
            $this->helper->logError("Could not cancel subscription with ID $subscriptionId: " . $e->getMessage());
            $this->helper->logError($e->getTraceAsString());
        }
        catch (\Exception $e)
        {
            $this->helper->addError("Sorry, the subscription could not be canceled. Please contact us for more help.");
            $this->helper->logError("Could not cancel subscription with ID $subscriptionId: " . $e->getMessage());
            $this->helper->logError($e->getTraceAsString());
        }

        $this->_redirect('stripe/customer/subscriptions');
    }

    protected function editSubscription($subscriptionId, $data)
    {
        try
        {
            $editableContent = \StripeIntegration\Payments\Block\Customer\Subscriptions::editableContent();

            if (!$this->stripeCustomer->getStripeId())
                throw new \Exception("Could not load customer account for subscription with ID $subscriptionId!");

            $subscription = $this->stripeCustomer->getSubscription($subscriptionId);

            foreach ($data as $key => $value)
            {
                if (in_array($key, $editableContent) && !empty($value))
                    $subscription->metadata[$key] = $value;
            }

            $subscription->save();

            $this->helper->addSuccess("Subscription <b>{$subscription->plan->name}</b> has been updated!");
        }
        catch (\Stripe\Error $e)
        {
            $this->helper->addError($e->getMessage());
            $this->helper->logError("Could not edit subscription with ID $subscriptionId: " . $e->getMessage());
            $this->helper->logError($e->getTraceAsString());
        }
        catch (\Exception $e)
        {
            $this->helper->addError("Sorry, the subscription could not be edited. Please contact us for more help.");
            $this->helper->logError("Could not edit subscription with ID $subscriptionId: " . $e->getMessage());
            $this->helper->logError($e->getTraceAsString());
        }

        $this->_redirect('stripe/customer/subscriptions');
    }
}

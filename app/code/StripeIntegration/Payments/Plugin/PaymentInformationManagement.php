<?php

namespace StripeIntegration\Payments\Plugin;

use Magento\Framework\Exception\CouldNotSaveException;

class PaymentInformationManagement
{
    /**
     * @var \Magento\Checkout\Helper\Data
     */
    private $checkoutHelper;

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    private $cartManagement;

    /**
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagement
     */
    public function __construct(
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Quote\Api\CartManagementInterface $cartManagement
    ) {

        $this->checkoutHelper = $checkoutHelper;
        $this->cartManagement = $cartManagement;
    }

    /**
     * Set payment information and place order for a specified cart.
     *
     * Override this method to get correct exceptions instead
     * "An error occurred on the server. Please try to place the order again."
     *
     * @param \Magento\Checkout\Model\PaymentInformationManagement $subject
     * @param \Closure $proceed
     * @param $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return int Order ID.
     */
    public function aroundSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Model\PaymentInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress
    ) {
        $subject->savePaymentInformation($cartId, $paymentMethod, $billingAddress);
        try {
            $orderId = $this->cartManagement->placeOrder($cartId);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __($e->getMessage()),
                $e
            );
        }

        return $orderId;
    }
}

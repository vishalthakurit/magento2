<?php
/**
 * Copyright © 2015 Voice . All rights reserved.
 */
namespace Voice\Notification\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_vocieCollectionDetails;
	protected $_customer;

	/**
     * @param \Magento\Framework\App\Helper\Context $context
     */
	public function __construct(\Magento\Framework\App\Helper\Context $context,
	\Voice\Notification\Model\VoiceFactory $vocieCollectionDetails,
	\Magento\Customer\Model\Session $customer
	) {
		$this->_vocieCollectionDetails = $vocieCollectionDetails;
		$this->_customer = $customer;
		parent::__construct($context);
	}
	public function getcollections(){
		$notificationData = $this->_vocieCollectionDetails->create()->getCollection();
		return $notificationData->addFieldToFilter('status',1);
	}
	public function getCustomerLogin(){
		if($this->_customer->isLoggedIn()) {
			return true;
		}
		return "hello";
	}
}
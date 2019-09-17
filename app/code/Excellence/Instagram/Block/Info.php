<?php
namespace Excellence\Instagram\Block;

class Info extends \Magento\Framework\View\Element\Template
{   
	protected $_userInfo;
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Excellence\Instagram\Helper\UserInfo $userInfo
	)
	{
		parent::__construct($context);
		$this->_userInfo = $userInfo;
	}
	public function getInstaUserInfo()
	{
		$info = $this->_userInfo->getInstaUserConfigs();
		return $info;
	}
	
}
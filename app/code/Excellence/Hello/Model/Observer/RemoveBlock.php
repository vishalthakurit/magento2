<?php

namespace Excellence\Hello\Model\Observer;

use Magento\Framework\Event\Observer;

use Magento\Framework\Event\ObserverInterface;

class RemoveBlock implements ObserverInterface

{

protected $_scopeConfig;

public function __construct(

    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig

) {

    $this->_scopeConfig = $scopeConfig;

}

public function execute(Observer $observer)

{

    /** @var \Magento\Framework\View\Layout $layout */

    // $layout = $observer->getLayout();

    // $block = $layout->getBlock('dashboard');

    // if ($block) {

    //     $remove = $this->_scopeConfig->getValue(

    //         'dashboard/settings/remove',

    //         \Magento\Store\Model\ScopeInterface::SCOPE_STORE

    //     );

    //     if ($remove) {

    //         $layout->unsetElement('dashboard');

    //     }

    // }

}

}
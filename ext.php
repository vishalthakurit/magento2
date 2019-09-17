<?php
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$obj = $bootstrap->getObjectManager();
$storeManager = $obj->get('\Magento\Store\Model\StoreManagerInterface');

$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();        

$appState = $objectManager->get('\Magento\Framework\App\State');
$appState->setAreaCode('frontend');

$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$customerObj = $objectManager->create('Magento\Customer\Model\Customer')->getCollection();
foreach($customerObj as $customerObjdata )
{
    echo "<pre/>";
    print_r($customerObjdata ->getData());
}
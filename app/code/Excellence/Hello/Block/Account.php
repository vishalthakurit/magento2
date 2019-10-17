<?php
namespace Excellence\Hello\Block;
  
class Account extends \Magento\Framework\View\Element\Template
{   
    protected $_testFactory;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Excellence\Hello\Model\TestFactory $testFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,  
        array $data = []
    )
    {
        $this->_testFactory = $testFactory;
        $this->productFactory = $productFactory;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {

    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    // public function getProduct($storeId, $productId) 
    // {
    //     $product = $this->productFactory->create()
    //                             ->setStoreId($storeId)
    //                             ->load($productId);
    //     return $product;
    // }

    // public function getProductCollection($storeId) {
    //     $productCollection = $this->productFactory->create()
    //                                     ->setStoreId($storeId)
    //                                     ->getCollection()
    //                                     ->addAttributeToSelect('*');
    //     return $productCollection;
    // }
}
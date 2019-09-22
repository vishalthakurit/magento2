<?php

namespace Excellence\Instagram\Block\Api;

class Index extends \Magento\Framework\View\Element\Template
{   
    protected $_executeApi;
    protected $_collection;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Excellence\Instagram\Helper\ExecuteApi $executeApi,
        \Excellence\Instagram\Model\InstagramFactory $collection
    )
    {
        $this->_executeApi = $executeApi;
        $this->_collection = $collection;
        parent::__construct($context);
    }

    /**
     * @return ApiExecutionInfo
     */
    public function getRecentPost()
    {
        $apiInfo = $this->_executeApi->getRecentPostFromInsta();
        return $apiInfo;
    }

    /**
     * @return Collection
     */
    public function getInstaCollection()
    {
        $resultPage = $this->_collection->create();
        $collection = $resultPage->getCollection(); 

        return $collection;
    }  

    public function getParameter(){
        $parameter = $this->getRequest()->getParam('instagram_id');
        return $parameter;
    }  

}
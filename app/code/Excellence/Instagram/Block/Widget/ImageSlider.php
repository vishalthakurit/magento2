<?php
namespace Excellence\Instagram\Block\Widget;

use Magento\Framework\View\Element\Template;

class ImageSlider extends Template
{
    protected $_template = "widget/imageslider.phtml";
    protected $_executeApi;
    protected $_collection;
		
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Excellence\Instagram\Helper\ExecuteApi $executeApi,
		\Excellence\Instagram\Model\InstagramFactory $collection,		
		array $data = []
	)
	{
		$this->_executeApi = $executeApi;
		$this->_collection = $collection;
		parent::__construct($context, $data);
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

	public function getRecentPost()
    {
    	$apiInfo = $this->_executeApi->getRecentPostFromInsta();
        return $apiInfo;
    }

}
<?php
namespace Excellence\Instagram\Controller\Adminhtml\Generic;
use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\Controller\Result;

class SliderConfig extends \Magento\Catalog\Controller\Adminhtml\Product
{
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
		\Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
		)
	{
		parent::__construct($context, $productBuilder);
		$this->resultLayoutFactory = $resultLayoutFactory;
	}
	public function execute()
	{
		$this->productBuilder->build($this->getRequest());
		$resultLayout = $this->resultLayoutFactory->create();
		$resultLayout->getLayout()->getBlock('excellence.generic.edit.tab.sliderconfig')->setProducts($this->getRequest()->getPost('generics', null));
		return $resultLayout;
	}
}

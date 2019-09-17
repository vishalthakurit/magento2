<?php

namespace Excellence\Instagram\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Excellence\Instagram\Controller\AbstractController\InstagramLoaderInterface
     */
    protected $instagramLoader;
	
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
	 * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, InstagramLoaderInterface $instagramLoader, PageFactory $resultPageFactory)
    {
        $this->instagramLoader = $instagramLoader;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Instagram view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->instagramLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
		return $resultPage;
    }
}

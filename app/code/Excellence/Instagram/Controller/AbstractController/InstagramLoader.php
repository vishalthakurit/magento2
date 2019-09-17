<?php

namespace Excellence\Instagram\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class InstagramLoader implements InstagramLoaderInterface
{
    /**
     * @var \Excellence\Instagram\Model\InstagramFactory
     */
    protected $instagramFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Excellence\Instagram\Model\InstagramFactory $instagramFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Excellence\Instagram\Model\InstagramFactory $instagramFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->instagramFactory = $instagramFactory;
        $this->registry = $registry;
        $this->url = $url;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return bool
     */
    public function load(RequestInterface $request, ResponseInterface $response)
    {
        $id = (int)$request->getParam('id');
        if (!$id) {
            $request->initForward();
            $request->setActionName('noroute');
            $request->setDispatched(false);
            return false;
        }

        $instagram = $this->instagramFactory->create()->load($id);
        $this->registry->register('current_instagram', $instagram);
        return true;
    }
}

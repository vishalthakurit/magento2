<?php

namespace Excellence\Instagram\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface InstagramLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Excellence\Instagram\Model\Instagram
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}

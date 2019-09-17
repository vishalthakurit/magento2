<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_LoginAsCustomer
 * @author     Extension Team
 * @copyright  Copyright (c) 2019-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\LoginAsCustomer\Controller\Customer;

use Magento\Framework\App\Action\Context;

/**
 * LoginAsCustomer login action
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Bss\LoginAsCustomer\Model\LoginFactory
     */
    protected $bssLoginFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param \Bss\LoginAsCustomer\Model\LoginFactory $bssLoginFactory
     */
    public function __construct(
        Context $context,
        \Bss\LoginAsCustomer\Model\LoginFactory $bssLoginFactory
    ) {
        parent::__construct($context);
        $this->bssLoginFactory = $bssLoginFactory;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->messageManager->getMessages(true);
        $login = $this->initLogin();
        if (!$login) {
            $this->_redirect('/');
            return;
        }

        try {
            $login->authenticateCustomer();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        $this->messageManager->addSuccess(
            __('You are logged in as customer: %1', $login->getCustomer()->getName())
        );

        $this->_redirect('*/*/proceed');
    }

    /**
     * Init login
     *
     * @return bool|\Magento\Framework\DataObject
     */
    protected function initLogin()
    {
        $secret = $this->getRequest()->getParam('secret');
        
        if (!$secret) {
            $this->messageManager->addError(__('Cannot login to account. No secret key provided.'));
            return false;
        }

        $login = $this->bssLoginFactory->create()->loadNotUsed($secret);

        if ($login->getId()) {
            return $login;
        } else {
            $this->messageManager->addError(__('Cannot login to account. Secret key is not valid.'));
            return false;
        }
    }
}

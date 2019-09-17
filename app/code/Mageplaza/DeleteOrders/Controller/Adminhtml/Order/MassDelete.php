<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_DeleteOrders
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\DeleteOrders\Controller\Adminhtml\Order;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\DeleteOrders\Helper\Data as DataHelper;
use Psr\Log\LoggerInterface;

/**
 * Class MassDelete
 * @package Mageplaza\DeleteOrders\Controller\Adminhtml\Order
 */
class MassDelete extends AbstractMassAction
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::delete';

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var DataHelper
     */
    protected $helper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderRepository $orderRepository
     * @param DataHelper $dataHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderRepository $orderRepository,
        DataHelper $dataHelper,
        LoggerInterface $logger
    ) {
        parent::__construct($context, $filter);

        $this->collectionFactory = $collectionFactory;
        $this->orderRepository = $orderRepository;
        $this->helper = $dataHelper;
        $this->logger = $logger;
    }

    /**
     * @param AbstractCollection $collection
     * @return Redirect|ResponseInterface|ResultInterface
     */
    protected function massAction(AbstractCollection $collection)
    {
        if ($this->helper->isEnabled()) {
            $deleted = 0;

            /** @var OrderInterface $order */
            foreach ($collection->getItems() as $order) {
                try {
                    /** delete order*/
                    $this->orderRepository->delete($order);
                    /** delete order data on grid report data related*/
                    $this->helper->deleteRecord($order->getId());

                    $deleted++;
                } catch (Exception $e) {
                    $this->logger->critical($e);
                    $this->messageManager->addErrorMessage(__('Cannot delete order #%1. Please try again later.', $order->getIncrementId()));
                }
            }
            if ($deleted) {
                $this->messageManager->addSuccessMessage(__('A total of %1 order(s) has been deleted.', $deleted));
            }
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }
}

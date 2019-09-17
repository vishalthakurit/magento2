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
 * @package    Bss_RefundRequest
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\RefundRequest\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class Status
{
    /**
     * @var
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $writeAdapter;

    /**
     * Status constructor.
     * @param CollectionFactory $orderCollectionFactory
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        CollectionFactory $orderCollectionFactory,
        ResourceConnection $resourceConnection
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->writeAdapter = $this->resourceConnection->getConnection('core_write');
    }

    /**
     * @param $orderIncrementId
     * @return string
     */
    public function getRefundStatus($orderIncrementId)
    {
        $table = $this->resourceConnection->getTableName('bss_refundrequest');
        $collection = $this->orderCollectionFactory->create()
            ->addAttributeToSelect('*')->addFieldToFilter('main_table.increment_id', $orderIncrementId);
        $collection->getSelect()->join(
            ['aggregation' => $table],
            'main_table.increment_id = aggregation.increment_id'
        );
        $data = $collection->getData();
        if (empty($data)) {
            return '';
        } else {
            $status = $data[0]["refund_status"];
        }
        return $status;
    }

    /**
     * Update Column status
     */
    public function updateOrderRefundStatus()
    {
        $collection = $this->orderCollectionFactory->create();
        $collectionData = $collection->getData();
        foreach ($collectionData as $orderData) {
            $incrementId = $orderData['increment_id'];
            $status = $this->getRefundStatus($incrementId);
            if ($status === '') {
                $status = null;
            }
            $table = $this->resourceConnection->getTableName('sales_order_grid');
            $this->updateData(
                $table,
                ['refund_status' => $status],
                "increment_id = $incrementId"
            );
        }
    }

    /**
     * @param $table
     * @param $data
     * @param $condition
     * @return int
     */
    public function updateData($table, $data, $condition)
    {
        return $this->writeAdapter->update($table, $data, $condition);
    }
}

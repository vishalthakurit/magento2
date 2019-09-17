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
namespace Bss\RefundRequest\Model\Attribute\Source;

use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;
use Bss\RefundRequest\Model\ResourceModel\Status;

class RefundOrder implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $orderStatusCollection;

    /**
     * @var Status
     */
    protected $bssRefundStatus;

    /**
     * RefundOrder constructor.
     * @param CollectionFactory $orderStatusCollection
     * @param Status $bssRefundStatus
     */
    public function __construct(
        CollectionFactory $orderStatusCollection,
        Status $bssRefundStatus
    ) {
        $this->orderStatusCollection = $orderStatusCollection;
        $this->bssRefundStatus = $bssRefundStatus;
    }

    /**
     * @return \Magento\Sales\Model\ResourceModel\Order\Status\Collection
     */
    public function getStatus()
    {
        $status = $this->orderStatusCollection->create();
        return $status;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $array = [];
        foreach ($this->getStatus() as $value) {
            $array[] = ['value' => $value->getStatus(), 'label' => $value->getLabel()];
        }
        return $array;
    }
}

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

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Bss\RefundRequest\Model\ResourceModel\Status as bssRefundStatus;

class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Remind Status values
     */
    const PENDING = 0;
    const ACCEPT = 1;
    const REJECT = 2;
    const NA = null;

    /**
     * @var CollectionFactory
     */
    protected $orderStatusCollection;

    /**
     * @var bssRefundStatus
     */
    protected $bssRefundStatus;

    /**
     * Status constructor.
     * @param CollectionFactory $orderStatusCollection
     * @param bssRefundStatus $bssRefundStatus
     */
    public function __construct(
        CollectionFactory $orderStatusCollection,
        bssRefundStatus $bssRefundStatus
    ) {
        $this->orderStatusCollection = $orderStatusCollection;
        $this->bssRefundStatus = $bssRefundStatus;
    }

    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $this->bssRefundStatus->updateOrderRefundStatus();

        return [
            ['value' => self::PENDING,  'label' => __('Pending')],
            ['value' => self::ACCEPT,  'label' => __('Accept')],
            ['value' => self::REJECT,  'label' => __('Reject')],
            ['value' => self::NA,  'label' => __('N/A')]
        ];
    }
}

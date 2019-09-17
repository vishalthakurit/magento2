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
namespace Bss\RefundRequest\Model;

use Magento\Framework\Model\AbstractModel;

class Request extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Bss\RefundRequest\Model\ResourceModel\Request');
    }

    /**
     * @param $oderId
     */
    public function setOrderId($oderId)
    {
        $this->setData("increment_id", $oderId);
    }

    /**
     * @param $reasonComment
     */
    public function setReasonComment($reasonComment)
    {
        $this->setData("reason_comment", $reasonComment);
    }

    /**
     * @param $time
     */
    public function setTime($time)
    {
        $this->setData("create_at", $time);
    }

    /**
     * @param $option
     */
    public function setOption($option)
    {
        $this->setData("reason_option", $option);
    }

    /**
     * @param $radio
     */
    public function setRadio($radio)
    {
        $this->setData("radio_option", $radio);
    }

    /**
     * @param $customerName
     */
    public function setCustomerName($customerName)
    {
        $this->setData("customer_name", $customerName);
    }

    /**
     * @param $customerEmail
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->setData("customer_email", $customerEmail);
    }
}

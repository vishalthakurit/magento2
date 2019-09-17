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
namespace Bss\RefundRequest\Helper;

class RecentOder
{

    /**
     * @var Data
     */
    protected $helperConfigAdmin;

    /**
     * RecentOder constructor.
     * @param Data $helperConfigAdmin
     */
    public function __construct(
        Data $helperConfigAdmin
    ) {
        $this->helperConfigAdmin = $helperConfigAdmin;
    }

    //General config admin
    /**
     * @return string
     */
    public function getTemplate()
    {
        if ($this->helperConfigAdmin->getConfigEnableModule()) {
            $template =  'Bss_RefundRequest::order/recent.phtml';
        } else {
            $template = 'Magento_Sales::order/recent.phtml';
        }

        return $template;
    }

    /**
     * @return string
     */
    public function getTemplateMyOder()
    {
        if ($this->helperConfigAdmin->getConfigEnableModule()) {
            $template =  'Bss_RefundRequest::order/history.phtml';
        } else {
            $template = 'Magento_Sales::order/history.phtml';
        }
        return $template;
    }
}

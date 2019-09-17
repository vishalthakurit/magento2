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
namespace Bss\LoginAsCustomer\Model\ResourceModel\Login\Grid;

/**
 * LoginAsCustomer collection
 */
class Collection extends \Bss\LoginAsCustomer\Model\ResourceModel\Login\Collection
{
    /**
     * Constructor
     *
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_map['fields']['email'] = 'c.email';
    }

    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->joinLeft(
                ['c' => $this->getTable('customer_entity')],
                'c.entity_id = main_table.customer_id',
                ['email']
            )->joinLeft(
                ['a' => $this->getTable('admin_user')],
                'a.user_id = main_table.admin_id',
                ['username']
            );
        $this->addFilterToMap('created_at', 'main_table.created_at');
        return $this;
    }
}

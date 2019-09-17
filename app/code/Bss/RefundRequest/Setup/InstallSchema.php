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
namespace Bss\RefundRequest\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * Create Database
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        /**
         * Create table 'bss_refundrequest'
         */
        $installer = $setup;

        $installer->startSetup();

        $installer->getConnection()
            ->addColumn(
                $installer->getTable('sales_order_grid'),
                'refund_status',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => '2M',
                    'nullable' => true,
                    'comment' => 'Refund Status'
                ]
            );

        $table = $installer->getConnection()
            ->newTable($installer->getTable('bss_refundrequest'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Id'
            )
            ->addColumn(
                'refund_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                '2M',
                ['nullable' => false, 'default' => 0],
                'Refund Status'
            )
            ->addColumn(
                'increment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                [],
                'Increment Id'
            )
            ->addColumn(
                'customer_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Customer Name'
            )
            ->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Customer Email'
            )
            ->addColumn(
                'reason_comment',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Reason'
            )
            ->addColumn(
                'reason_option',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Reason for refund'
            )
            ->addColumn(
                'radio_option',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                '2M',
                [],
                'Product Status'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Post Created At'
            )
            ->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Post Updated At'
            )
            ->addIndex(
                $installer->getIdxName('bss_refundrequest', ['id']),
                ['id']
            )
            ->setComment("Refund Request");
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('bss_requestlabel'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                ],
                'Id'
            )
            ->addColumn(
                'request_label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Request Label'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                '2M',
                ['nullable' => false, 'default' => 0],
                'Status'
            )
            ->addIndex(
                $installer->getIdxName('bss_requestlabel', ['id']),
                ['id']
            )
            ->setComment("Refund Request Dropdown Options");
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}

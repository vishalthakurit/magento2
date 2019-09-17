<?php
/**
 * Copyright © 2015 Voice. All rights reserved.
 */

namespace Voice\Notification\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
	
        $installer = $setup;

        $installer->startSetup();

		/**
         * Create table 'notification_voice'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('notification_voice')
        )
		->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'notification_voice'
        )
		->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'name'
        )
		->addColumn(
            'store',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'store'
        )
		->addColumn(
            'customer',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'customer'
        )
		->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'status'
        )
		->addColumn(
            'voice_text',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'voice_text'
        )
        ->addColumn(
            'language',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            '64k',
            [],
            'language'
        )
		/*{{CedAddTableColumn}}}*/
		
		
        ->setComment(
            'Voice Notification notification_voice'
        );
		
		$installer->getConnection()->createTable($table);
		/*{{CedAddTable}}*/

        $installer->endSetup();

    }
}

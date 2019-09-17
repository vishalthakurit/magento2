<?php


namespace Excellence\Instagram\Setup;

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
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
		$installer = $setup;
		$installer->startSetup();

		/**
		 * Creating table excellence_instagram
		 */
		$table = $installer->getConnection()->newTable(
			$installer->getTable('excellence_instagram')
		)->addColumn(
			'instagram_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Entity Id'
		)->addColumn(
			'image_url',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,'2M',
			
			['nullable' => true],
			'image_url'
		)->addColumn(
			'product_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			255,
			['nullable' => true,'default' => null],
			'product_id'
		)->addColumn(
			'insta_tag',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'insta_tag'
		);
		$installer->getConnection()->createTable($table);
		$installer->endSetup();

	}
}
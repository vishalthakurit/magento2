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
		 * Creating table excellence_generic_slider
		 */
		$table = $installer->getConnection()->newTable(
			$installer->getTable('excellence_generic_slider')
		)->addColumn(
			'generic_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Entity Id'
		)->addColumn(
			'slider_name',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,'2M',	
			['nullable' => true],
			'Slider Name'
		)->addColumn(
			'display_page',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Display Slider On'
		)->addColumn(
			'slider_position',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Slider Position'
		)->addColumn(
			'image_urls',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Images URIs'
		)->addColumn(
			'image_tag',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Image Tag Name'
		)->addColumn(
			'username',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Username'
		)->addColumn(
			'likes',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Like'
		)->addColumn(
			'comments',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Comments'
		)->addColumn(
			'post_link',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Post Link'
		)->addColumn(
			'slider_heading',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Slider Heading'
		)->addColumn(
			'slider_subheading',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Slider Sub Heading'
		)->addColumn(
			'status',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Status'
		)->addColumn(
			'auto_play',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Auto Play'
		)->addColumn(
			'number_of_images',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Number Of Images'
		)->addColumn(
			'time_interval',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Time Interval'
		)->addColumn(
			'store_view',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Store View'
		);
		$installer->getConnection()->createTable($table);
		$installer->endSetup();

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
		)->addColumn(
			'tag_name',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'tag_name'
		)->addColumn(
			'image_link',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'image_link'
		)->addColumn(
			'image_tag_name',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Image Tag Name'
		)->addColumn(
			'username',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Username'
		)->addColumn(
			'likes',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Like'
		)->addColumn(
			'comments',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Comments'
		)->addColumn(
			'post_link',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Post Link'
		)->addColumn(
			'slider_heading',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Slider Heading'
		)->addColumn(
			'slider_subheading',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Slider Sub Heading'
		)->addColumn(
			'status',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Status'
		)->addColumn(
			'auto_play',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Auto Play'
		)->addColumn(
			'number_of_images',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Number Of Images'
		)->addColumn(
			'time_interval',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Time Interval'
		)->addColumn(
			'store_view',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Store View'
		);
		$installer->getConnection()->createTable($table);
		$installer->endSetup();
	}
}
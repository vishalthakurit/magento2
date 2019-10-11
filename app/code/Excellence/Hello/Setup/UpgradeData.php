<?php
 
namespace Excellence\Hello\Setup;
 
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
 
class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    private $blockFactory;
 
    /**
     * @var \Magento\Widget\Model\Widget\InstanceFactory
     */
    private $widgetFactory;
 
    /**
     * @var \Magento\Framework\App\State
     */
    private $state;
 
    /**
     * UpgradeData constructor
     * 
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     * @param \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory
     */
    public function __construct(
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Magento\Framework\App\State $state
    )
    {
        $this->blockFactory = $blockFactory;
        $this->widgetFactory = $widgetFactory;
        $this->state = $state;
    }
 
    /**
     * Upgrade data for the module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        // Set Area code to prevent the Exception during setup:upgrade 
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
 
        $setup->startSetup();
        if (version_compare($context->getVersion(), '0.1.1') < 0) {
            $cmsBlockData = [
                'title' => 'Test Block',
                'identifier' => 'test-block',
                'content' => "<div class='block'>
                                <div class='block-title'><strong role='heading' aria-level='2'>My test block</strong></div>
                                <div class='block-content'>
                                <div class='empty'>Learn more about the site.</div>
                                </div>
                                </div>",
                'is_active' => 1,
                'stores' => [0],
                'sort_order' => 0
            ];
 
            $cmsBlock = $this->blockFactory->create()->setData($cmsBlockData)->save();
 
            $widgetData = [
                'instance_type' => 'Magento\Cms\Block\Widget\Block',
                'instance_code' => 'cms_static_block',
                'theme_id' => 5,
                'title' => 'Test Widget',
                'store_ids' => '0',
                'widget_parameters' => '{"block_id":"'.$cmsBlock->getId().'"}',
                'sort_order' => 0,
                'page_groups' => [[
                    'page_id' => 1,
                    'page_group' => 'all_pages',
                    'layout_handle' => 'default',
                    'for' => 'all',
                    'all_pages' => [
                        'page_id' => null,
                        'layout_handle' => 'default',
                        'block' => 'sidebar.additional',
                        'for' => 'all',
                        'template' => 'widget/static_block/default.phtml'
                    ]
                ]]
            ];    
 
            $this->widgetFactory->create()->setData($widgetData)->save();    
        }
        $setup->endSetup();
    }
}
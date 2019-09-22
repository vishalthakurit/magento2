<?php
namespace Excellence\Instagram\Block\Adminhtml\Instagram\Edit;

/**
 * Admin instagram left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Manage Product Slider'));
    }
    protected function _prepareLayout()
    {
        $this->addTab(
            'productgrid',
            [
                'label' => __('Product'),
                'url' => $this->getUrl('instagram/*/productgrid', ['_current' => true]),
                'class' => 'ajax',
            ]
        );
    }
}

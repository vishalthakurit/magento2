<?php
namespace Excellence\Instagram\Block\Adminhtml\Generic\Edit;

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
        $this->setTitle(__('Manage Generic Slider'));
    }
    protected function _prepareLayout()
    {
        // $this->addTab(
        //     'productgrid',
        //     [
        //         'label' => __('Product'),
        //         'url' => $this->getUrl('generic/*/productgrid', ['_current' => true]),
        //         'class' => 'ajax',
        //     ]
        // );
    }
}

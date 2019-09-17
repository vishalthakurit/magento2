<?php
namespace Voice\Notification\Block\Adminhtml\Voice\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_voice_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Voice Information'));
    }
}
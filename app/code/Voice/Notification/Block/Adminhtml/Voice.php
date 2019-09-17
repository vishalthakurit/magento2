<?php
namespace Voice\Notification\Block\Adminhtml;
class Voice extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_voice';/*block grid.php directory*/
        $this->_blockGroup = 'Voice_Notification';
        $this->_headerText = __('Voice');
        $this->_addButtonLabel = __('Add New Entry'); 
        parent::_construct();
		
    }
}

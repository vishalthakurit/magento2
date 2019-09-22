<?php
/**
 * Adminhtml instagram list block
 *
 */
namespace Excellence\Instagram\Block\Adminhtml;

class Generic extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_generic';
        $this->_blockGroup = 'Excellence_Instagram';
        $this->_headerText = __('Manage Generic Slider');
        $this->_addButtonLabel = __('Add New Slider');
        parent::_construct();
        if ($this->_isAllowedAction('Excellence_Instagram::save')) {
            $this->buttonList->update('add', 'label', __('Add New Slider'));
        } else {
            $this->buttonList->remove('add');
        }
    }
    
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}

<?php
/**
 * Adminhtml instagram list block
 *
 */
namespace Excellence\Instagram\Block\Adminhtml;

class Instagram extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_instagram';
        $this->_blockGroup = 'Excellence_Instagram';
        $this->_headerText = __('Instagram');
        $this->_addButtonLabel = __('Add New Instagram');
        parent::_construct();
        if ($this->_isAllowedAction('Excellence_Instagram::save')) {
            $this->buttonList->update('add', 'label', __('Add New Instagram'));
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

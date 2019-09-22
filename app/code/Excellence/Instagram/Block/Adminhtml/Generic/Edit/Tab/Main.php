<?php
namespace Excellence\Instagram\Block\Adminhtml\Generic\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('generic');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Excellence_Instagram::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('generic_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Select Instagram Images')]);

        if ($model->getId()) {
            $fieldset->addField('generic_id', 'hidden', ['name' => 'generic_id']);
        }
        $fieldImage = $fieldset->addField(
            'image',
            'text',
            [
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
                'required' => true,
            ]
        );
        $renderer = $this->getLayout()->createBlock(
            'Excellence\Instagram\Block\Adminhtml\CustomImageHtml\Edit\Renderer\GenericImageRenderer'
        );
        $fieldImage->setRenderer($renderer);
 
        $this->_eventManager->dispatch('adminhtml_generic_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Instagram Images');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Select Instagram Images');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
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

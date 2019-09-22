<?php
namespace Excellence\Instagram\Block\Adminhtml\Instagram\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Sliderconfig extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        $model = $this->_coreRegistry->registry('instagram');

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

        $form->setHtmlIdPrefix('instagram_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Product Slider Configuration')]);

        if ($model->getId()) {
            $fieldset->addField('instagram_id', 'hidden', ['name' => 'instagram_id']);
        }

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_view',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    // 'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_view',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }
        $fieldImage = $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Enable'),
                'title' => __('Enable'),
                'options' => array('1' => 'Yes', '0' => 'No'),
            ]
        );
        $fieldImage = $fieldset->addField(
            'auto_play',
            'select',
            [
                'name' => 'auto_play',
                'label' => __('Auto Play'),
                'title' => __('Auto Play'),
                'options' => array('1' => 'Yes', '0' => 'No'),
            ]
        );
        $fieldImage = $fieldset->addField(
            'slider_heading',
            'text',
            [
                'name' => 'slider_heading',
                'label' => __('Slider Heading'),
                'title' => __('Slider Heading'),
                'class' => 'slider_heading',
            ]
        );
        $fieldImage = $fieldset->addField(
            'slider_subheading',
            'text',
            [
                'name' => 'slider_subheading',
                'label' => __('Slider Sub Heading'),
                'title' => __('Slider Sub Heading'),
                'class' => 'slider_sub_heading',
            ]
        );
        $fieldImage = $fieldset->addField(
            'number_of_images',
            'text',
            [
                'name' => 'number_of_images',
                'label' => __('Number Of Images'),
                'title' => __('Number Of Images'),
                'class' => 'number_of_images'
            ]
        );
        $fieldImage = $fieldset->addField(
            'time_interval',
            'text',
            [
                'name' => 'time_interval',
                'label' => __('Slider Time Interval'),
                'title' => __('Slider Time Interval'),
                'class' => 'time_interval'
            ]
        );
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
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('General');
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

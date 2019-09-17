<?php
namespace Voice\Notification\Block\Adminhtml\Voice\Edit\Tab;
class BasicInformation extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    protected $_status;
    protected $_customerDetails;

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
        \Voice\Notification\Model\Adminhtml\Config\Source\EnableDisable $status,
        \Voice\Notification\Model\Adminhtml\Config\Source\CustomerGroup $customerDetails,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
        $this->_customerDetails = $customerDetails;
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
        $model = $this->_coreRegistry->registry('notification_voice');
		$isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Basic Information')));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }

		$fieldset->addField(
            'status',
            'select',
            array(
                'name' => 'status',
                'label' => __('enable'),
                'title' => __('enable'),
                'values' => $this->_status->toOptionArray(),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'name',
            'text',
            array(
                'name' => 'name',
                'label' => __('name'),
                'title' => __('name'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'store',
            'multiselect',
            array(
                'name' => 'store',
                'label' => __('store view'),
                'title' => __('store view'),
                'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'customer',
            'multiselect',
            array(
                'name' => 'customer',
                'label' => __('customer'),
                'title' => __('customer'),
                'values' => $this->_customerDetails->toOptionArray(),
                /*'required' => true,*/
            )
        );
		/*{{CedAddFormField}}*/
        
        if (!$model->getId()) {
            $model->setData('status', $isElementDisabled ? '2' : '1');
        }

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
        return __('Basic Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Basic Information');
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

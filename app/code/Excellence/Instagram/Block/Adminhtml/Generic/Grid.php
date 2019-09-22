<?php
namespace Excellence\Instagram\Block\Adminhtml\Generic;

/**
 * Adminhtml Generic grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Excellence\Instagram\Model\ResourceModel\Generic\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Excellence\Instagram\Model\Generic
     */
    protected $_instagram;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Excellence\Instagram\Model\Generic $instagramPage
     * @param \Excellence\Instagram\Model\ResourceModel\Generic\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Excellence\Instagram\Model\Generic $instagram,
        \Excellence\Instagram\Model\ResourceModel\Generic\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_instagram = $instagram;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('genericGrid');
        $this->setDefaultSort('generic_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        /* @var $collection \Excellence\Instagram\Model\ResourceModel\Generic\Collection */
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('generic_id', [
            'header'    => __('Id'),
            'index'     => 'generic_id',
        ]);
        $this->addColumn(
           'image_urls',
           array(
               'header' => __('Image Slider'),
               'index' => 'image_urls',
               'filter' => false,
               'sortable' => false,
               'renderer'  => '\Excellence\Instagram\Block\Adminhtml\Generic\Grid\Renderer\Slider'
           )
        );
        $this->addColumn('insta_tag', ['header' => __('Image Tag'), 'index' => 'insta_tag']);
        $this->addColumn('slider_name', 
            [
                'header' => __('Slider Name'),
                'index' => 'slider_name'
            ]
        );
        $this->addColumn(
            'action',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'generic_id'
                    ]
                ],
                'sortable' => false,
                'filter' => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['generic_id' => $row->getId()]);
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}

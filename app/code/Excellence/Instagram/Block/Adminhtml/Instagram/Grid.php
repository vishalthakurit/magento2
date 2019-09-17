<?php
namespace Excellence\Instagram\Block\Adminhtml\Instagram;

/**
 * Adminhtml Instagram grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Excellence\Instagram\Model\ResourceModel\Instagram\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Excellence\Instagram\Model\Instagram
     */
    protected $_instagram;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Excellence\Instagram\Model\Instagram $instagramPage
     * @param \Excellence\Instagram\Model\ResourceModel\Instagram\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Excellence\Instagram\Model\Instagram $instagram,
        \Excellence\Instagram\Model\ResourceModel\Instagram\CollectionFactory $collectionFactory,
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
        $this->setId('instagramGrid');
        $this->setDefaultSort('instagram_id');
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
        /* @var $collection \Excellence\Instagram\Model\ResourceModel\Instagram\Collection */
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
        $this->addColumn('instagram_id', [
            'header'    => __('ID'),
            'index'     => 'instagram_id',
        ]);
        $this->addColumn(
           'image_url',
           array(
               'header' => __('IMAGE'),
               'index' => 'image_url',
               'filter' => false,
               'sortable' => false,
               'renderer'  => '\Excellence\Instagram\Block\Adminhtml\Instagram\Grid\Renderer\Slider',
           )
       );
        $this->addColumn('insta_tag', ['header' => __('IMAGE TAG'), 'index' => 'insta_tag']);
        $this->addColumn('product_id', ['header' => __('PRODUCT ID'), 'index' => 'product_id']);
        
       
       
        
        $this->addColumn(
            'action',
            [
                'header' => __('EDIT'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'instagram_id'
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
        return $this->getUrl('*/*/edit', ['instagram_id' => $row->getId()]);
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

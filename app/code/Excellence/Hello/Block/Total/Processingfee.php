<?php
namespace Excellence\Hello\Block\Total;

class Processingfee extends \Magento\Framework\View\Element\Template
{
    /**
     * Tax configuration model
     *
     * @var \Magento\Tax\Model\Config
     */
    protected $config;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $source;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param array $data
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Tax\Model\Config $taxConfig,
        array $data = []
    ) {
        $this->config = $taxConfig;
        parent::__construct($context, $data);
    }


    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->source;
    }

      /**
       * @return Order
       */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Initialize all order totals relates with tax
     *
     * @return \Magento\Tax\Block\Sales\Order\Tax
     */
    public function initTotals()
    {

        $parent = $this->getParentBlock();
        $this->order = $parent->getOrder();
        
        $this->source = $parent->getSource();
        $store = $this->getStore();
        $order = $this->order->load($this->order->getId());
        $processingFee = $order->getData('processing_fee');
        if ($processingFee) {
            $charges = new \Magento\Framework\DataObject(
                [
                    'code' => 'processing_fee',
                    'strong' => false,
                    'value' => $processingFee,
                    'label' => __('Processing Fee'),
                ]
            );
            $parent->addTotal($charges, 'processing_fee');
            $parent->addTotal($charges, 'processing_fee');
        }
            return $this;
    }
}
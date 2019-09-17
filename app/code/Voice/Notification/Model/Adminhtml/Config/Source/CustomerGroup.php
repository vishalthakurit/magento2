<?php
namespace Voice\Notification\Model\Adminhtml\Config\Source;
 
class CustomerGroup implements \Magento\Framework\Option\ArrayInterface
{
    protected $_customerGroup;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Model\Group $customerGroupCollection,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        array $data = []
    ) {
        $this->_customerGroup = $customerGroupCollection;
        $this->_messageManager = $messageManager;
    }
    
    public function toOptionArray()
    {
        $customer = $this->_customerGroup->getCollection();
        $customerCounts = sizeof($customer->getData());
        for ($i=0; $i < $customerCounts; $i++) {
           $customers[] = ['value' => $i, 'label' => __($customer->getData()[$i]['customer_group_code'])];
        }
        return $customers;
    }
}
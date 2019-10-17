<?php
namespace Excellence\Hello\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Eav\Model\Config;

class MassSendConfirmation extends \Magento\Customer\Controller\Adminhtml\Index\AbstractMassAction
{
    protected $customerRepository;
    protected $eavConfig;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CustomerRepositoryInterface $customerRepository,
        Config $eavConfig
    ) {
        parent::__construct($context, $filter, $collectionFactory);
        $this->customerRepository = $customerRepository;
        $this->eavConfig = $eavConfig;
    }

    protected function massAction(AbstractCollection $collection)
    {
        $customersUpdated = 0;
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        foreach ($collection->getAllIds() as $customerId) {
                // Verify customer exists
            echo "===> ".$customerId;
            try {
                $customer = $this->customerRepository->getById($customerId);
                // $value = $this->UpdateText();
                // $customer->setCustomAttribute("your_attributes", $value);
                // $this->saveAttribute($customer);
                $customersUpdated++;
                if ($customersUpdated) {
                        $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $customersUpdated));
                }
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong when save customer'));
            }
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            // $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            // $resultRedirect->setPath($this->getComponentRefererUrl());

            // return $resultRedirect;
        }die;
    }

    protected function saveAttribute($customer)
    {
        return $this->customerRepository->save($customer);
    }

    protected function UpdateText()
    {
        $attribute = $this->eavConfig->getAttribute('customer', 'your_attributes');
        $options = $attribute->getSource()->getAllOptions();
        foreach ($options as $option) {
            if ($option['label'] == 'Text1') {
                $value = $option['value'];
            }
        }
        return $value;
    }
}
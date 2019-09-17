<?php
// namespace Excellence\Hello\Setup;


// use Magento\Eav\Setup\EavSetup;
// use Magento\Eav\Setup\EavSetupFactory;
// use Magento\Framework\Setup\InstallDataInterface;
// use Magento\Framework\Setup\ModuleContextInterface;
// use Magento\Framework\Setup\ModuleDataSetupInterface;
// use Magento\Eav\Model\Config;
// use Magento\Customer\Model\Customer;

// class InstallData implements \Magento\Framework\Setup\InstallDataInterface
// {
//     private $eavSetupFactory;
     
//     public function __construct(EavSetupFactory $eavSetupFactory, Config $eavConfig)
//     {
//         $this->eavSetupFactory = $eavSetupFactory;  
//         $this->eavConfig       = $eavConfig;
//     }
 
//     public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
//     {
//         $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
//         $eavSetup->addAttribute(
//             \Magento\Customer\Model\Customer::ENTITY,
//             'skype_profile',
//             [
//                 'type'         => 'varchar',
//                 'label'        => 'Skype Profile',
//                 'input'        => 'text',
//                 'required'     => false,
//                 'visible'      => true,
//                 'user_defined' => true,
//                 'position'     => 999,
//                 'system'       => 0,
//             ]
//         );

//         $sampleAttribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'skype_profile');

//         // $eavSetup->$this->eavConfig->getAttribute(Customer::ENTITY, 'skype_profile')->setData('is_user_defined',1)->setData('default_value','')->setData('used_in_forms', ['adminhtml_customer', 'checkout_register', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'])->save();
 
//         // more used_in_forms ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address', 'customer_account_create']
        
//         $sampleAttribute->setData(
//             'used_in_forms',
//             ['adminhtml_customer', 'customer_account_create', 'customer_account_edit']
 
//         );
//         $sampleAttribute->save();
//     }
// }



namespace Excellence\Hello\Setup;


use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface 
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $setup->startSetup();

        $attributeCode = 'account_id';
        $eavSetup->addAttribute(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, 'account_id', [
            'label' => 'Account Id',
            'required' => false,
            'user_defined' => 1,
            'system' => 0,
            'position' => 100,
            'input' => 'text'
        ]);

        $eavSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            $attributeCode);

        $amountId = $this->eavConfig->getAttribute(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, $attributeCode);
        $amountId->setData('used_in_forms', [
            'adminhtml_customer',
            'customer_account_create',
            'customer_account_edit'
        ]);
        $amountId->getResource()->save($amountId);

        // add New Attribute

        $attributeCode = 'amount_spend';

        $eavSetup->addAttribute(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, $attributeCode, [
            'label' => 'Amount Spent',
            'required' => false,
            'user_defined' => 1,
            'system' => 0,
            'position' => 110
        ]);

        $eavSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            $attributeCode);

        $amountSpend = $this->eavConfig->getAttribute(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, $attributeCode);
        $amountSpend->setData('used_in_forms', [
            'adminhtml_customer',
            'customer_account_create',
            'customer_account_edit'
        ]);
        $amountSpend->getResource()->save($amountSpend);

        $setup->endSetup();
    }
}
<?php
namespace Excellence\Hello\Block;
  
class Main extends \Magento\Framework\View\Element\Template
{   
    protected $_testFactory;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Excellence\Hello\Model\TestFactory $testFactory
    )
    {
        $this->_testFactory = $testFactory;
        parent::__construct($context);
    }
    protected function _prepareLayout()
    {


// $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
// $customerObj = $objectManager->create('Magento\Customer\Model\Customer')->getCollection();
// foreach($customerObj as $customerObjdata )
// {
//     echo "<pre/>";
//     print_r($customerObjdata ->getData());
// }
// die;
        // $test = $this->_testFactory->create();
        // $test->setTitle('Hello Vishal Thakur Buddy !!!!');
        // $test->save();
        // $this->setTestModel($test);

        // **************************************

        // $test->load(3); 
        // print_r($test->getData());
         
        // $test->delete(3);

        // $collection = $test->getCollection();
        // foreach($collection as $row){
        //    echo "<pre>"; print_r($row->getData());
        // }

        // ****************************************

        $test = $this->_testFactory->create();
        $test->loadByTitle('thakur');
        // echo "==<pre>"; print_r($test->getData());
        $this->setTestModel($test);

    }
}
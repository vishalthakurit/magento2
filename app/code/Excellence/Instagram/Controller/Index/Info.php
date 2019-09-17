<?php
namespace Excellence\Instagram\Controller\Index;
use Magento\Framework\Data\Form\FormKey\Validator;
 
class Info extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @param Validator $formKeyValidator
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Validator $formKeyValidator,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ){
        $this->resultPageFactory = $resultPageFactory;   
        $this->formKeyValidator = $formKeyValidator;    
        return parent::__construct($context);
    }
     
    public function execute()
    {
        $validFormKey = $this->formKeyValidator->validate($this->getRequest());
        $formData = $this->getRequest()->getPostValue();
        return "Hello";
    } 
}
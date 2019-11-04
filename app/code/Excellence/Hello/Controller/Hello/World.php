<?php
namespace Excellence\Hello\Controller\Hello;
 
use Zend\Log\Filter\Timestamp;
 
class World extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $_inlineTranslation;
    protected $_transportBuilder;
    protected $_scopeConfig;
    protected $_logLoggerInterface;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->_logLoggerInterface = $loggerInterface;
        $this->messageManager = $context->getMessageManager();   
        return parent::__construct($context);
    }

    public function execute()
    {
        $post = array('name' => 'Vishal THakur', 'email' => 'vishal_t@excellencetechnologies.in');//$this->getRequest()->getPost();
        try
        {
            // Send Mail
            $this->_inlineTranslation->suspend();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
             
            $sender = [
                'name' => $post['name'],
                'email' => $post['email']
            ];
             
            $to = 'vshlrjpt80@gmail.com';
            $templateId = 3;
             
            $transport = $this->_transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions(
                [
                    'area' => 'frontend',
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
                )
                ->setTemplateVars([
                    'name'  => $post['name'],
                    'email'  => $post['email'],
                    'report_date' => date("j F Y", strtotime('+1 day')),
                    'orders_count' => rand(1, 10),
                    'order_items_count' => rand(1, 10),
                    'avg_items' => rand(1, 10)
                ])
                ->setFrom($sender)
                ->addTo($to)
                ->getTransport();
                 
                $transport->sendMessage();
                 
                $this->_inlineTranslation->resume();
                $this->messageManager->addSuccess('Email sent successfully');
                $this->_redirect('*/*/');
                 
        } catch(\Exception $e){
            $this->messageManager->addError($e->getMessage());
            $this->_logLoggerInterface->debug($e->getMessage());
            exit;
        }
        return $this->resultPageFactory->create();          
    }
}
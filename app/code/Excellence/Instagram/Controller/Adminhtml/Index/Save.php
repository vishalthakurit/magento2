<?php

namespace Excellence\Instagram\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(Action\Context $context, PostDataProcessor $dataProcessor,\Magento\Framework\Json\Helper\Data $jsonHelper,\Magento\Framework\Message\ManagerInterface $messageManager)
    {
        $this->dataProcessor = $dataProcessor;
        parent::__construct($context);
        $this->_jsonHelper = $jsonHelper;
         $this->_messageManager = $messageManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Excellence_Instagram::save');
    }

    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $data = $this->dataProcessor->filter($data);

            $model = $this->_objectManager->create('Excellence\Instagram\Model\Instagram');


            $id = $this->getRequest()->getParam('instagram_id');
            if ($id) {
                $model->load($id);
            }

            // save image data and remove from data array
            if (isset($data['image'])) {
                $imageData = $data['image'];
                unset($data['image']);
            } else {
                $imageData = array();
            }
            
            // $model->addData($data);

            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect('*/*/edit', ['instagram_id' => $model->getId(), '_current' => true]);
                return;
            }

            try {

                $imageHelper = $this->_objectManager->get('Excellence\Instagram\Helper\Data');

                if (isset($imageData['delete']) && $model->getImage()) {
                    $imageHelper->removeImage($model->getImage());
                    $model->setImage(null);
                }
                if (array_key_exists("instaImgUrl",$data)) {
                 $imageUrl = $data['instaImgUrl'];
                 $imageUrlJson =  $this->_jsonHelper->jsonEncode($imageUrl);
                $imageUrlJson = json_encode($imageUrl,true);
                 $model->setImageUrl($imageUrlJson);
                }else{
                    $message = 'Please select image';
                    $this->_messageManager->addError($message);
                }
                if (array_key_exists("products_id",$data)) {
                     $productId = $data['products_id'];
                     $model->setProductId($productId);
                }else{
                    $message = 'Choose Product';
                    $this->_messageManager->addError($message);
                }
                
                if (array_key_exists("instaTag",$data)) {
                    $instatag = $data['instaTag'];
                    $model->setInstaTag($instatag);
                }

                if(array_key_exists("products_id",$data) && array_key_exists("instaImgUrl",$data)){
                    $model->save();
                    $this->messageManager->addSuccess(__('The Data has been saved.'));
                    $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', ['instagram_id' => $model->getId(), '_current' => true]);
                        return;
                    }
                    $this->_redirect('*/*/');
                }else{
                    if ($id){
                        $this->_redirect('*/*/edit', ['instagram_id' => $model->getId(), '_current' => true]);
                    }else{
                        $this->_redirect('*/*/new');
                    }
                    
                }
                
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['instagram_id' => $this->getRequest()->getParam('instagram_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
}

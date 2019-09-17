<?php
namespace Excellence\Instagram\Block\Adminhtml\CustomImageHtml\Edit\Renderer;

use Magento\Framework\App\ObjectManager;
/**
* CustomFormField Customformfield field renderer
*/
class ImageRenderer extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element implements
    \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * Form element which re-rendering
     *
     * @var \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected $_element;
    /**
     * @var string
     */
    protected $_template = 'instagrid/elements/images.phtml';
    /**
     * Retrieve an element
     *
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */

    protected $instaFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Excellence\Instagram\Model\InstagramFactory $instaFactory,
        array $data = []

    ) {
        $this->instaFactory = $instaFactory;
        parent::__construct($context, $data);
    }

    public function getInstaCollection()
    {
        $collection = $this->instaFactory->create()->getCollection();
        return $collection;
    }

    public function getElement()
    {
        return $this->_element;
    }
    /**
     * Render element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }

    /**
     * Render Access Token And User ID
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function getUserConfigs()
    {
        $access_Token = $this->_scopeConfig->getValue(
            'instagramSection/setting/access_Token',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $user_id = $this->_scopeConfig->getValue(
            'instagramSection/setting/user_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $apiUrl = "https://api.instagram.com/v1/users/$user_id/media/recent?access_token=$access_Token";

        $init = curl_init($apiUrl); 
        curl_setopt($init, CURLOPT_CONNECTTIMEOUT, 20); 
        curl_setopt($init, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($init, CURLOPT_SSL_VERIFYPEER, false); 

        $json = curl_exec($init); 
        $data = json_decode($json, TRUE);

        return $data;
    }

    public function getParameter(){
        $parameter = $this->getRequest()->getParam('instagram_id');
        return $parameter;
    }
}
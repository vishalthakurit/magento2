<?php
namespace Excellence\Slider\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;
use Excellence\Slider\Model\SlidesFactory;
use Excellence\Slider\Model\SliderpagesFactory; 

class SliderWidget extends Template implements BlockInterface
{    
    protected $_sliderdata;
    protected $_scopeConfigObject;
    protected $_template = "widget/slider-widget.phtml";
    protected $_storeManager;
    protected $_messageManager;

    public function __construct(
        Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
        SlidesFactory $sliderCollections,
        SliderpagesFactory $sliderpagesCollections,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_sliderpagesCollections = $sliderpagesCollections;
        $this->_sliderCollections = $sliderCollections;
        $this->_scopeConfigObject = $scopeConfigObject;
        $this->_storeManager = $storeManager;
        $this->_messageManager = $messageManager;
        parent::__construct($context);
    }
    public function getSliderStretchInfo($sliderId){
        $sliderInfoDetails = $this->_sliderpagesCollections->create()->getCollection()->addFieldToFilter('id', array('eq' => $sliderId));
        return $sliderInfoDetails;

    }
    public function getSliderDetails($sliderId){   
        if(!empty($this->getSlider($sliderId))){
        return $this->getSlidesCollection($sliderId);            
       }
       else{
        // widget data changed from admin grid showing error message
        $message = __("Slider configuration changed !  Please reconfigure widget on this cms page from admin panel or May be it is disable from config.");
        // $this->_messageManager->addError($message);
       }

    }


    public function getSlider($sliderId){
        $slider = array();
        if($this->_scopeConfigObject->getValue('slider/slider/enable')){
            $storeId = $this->_storeManager->getStore()->getId();
        
            $slider = $this->_sliderpagesCollections->create()->getCollection()
                ->addFieldToFilter('is_active', '1')
                ->addFieldToFilter('id', $sliderId)
                ->addFieldToFilter('slider_display_page', '6')
                ->addFieldToFilter('store_id',array('like' => '%'.$storeId));
        }
        
        return $slider;
    }

    public function getSlidesCollection($sliderId){
        $slides = $this->_sliderCollections->create()->getCollection()
                    ->addFieldToFilter('is_active', '1')
                    ->addFieldToFilter('slider_name', $sliderId);
        return $slides;
    }

    public function getSliderType(){
        if(!($this->_scopeConfigObject->getValue('slider/slider/enable'))){
            return;
        }
        $sliderInfo['sliderType'] = $this->_scopeConfigObject->getValue('slider/slider/select_slider');
        $sliderSpeed = $this->_scopeConfigObject->getValue('slider/slider/select_speed');
        $sliderInfo['sliderSpeed'] = ($sliderSpeed * 1000);
        $sliderInfo['fade'] = $this->_scopeConfigObject->getValue('slider/slider/select_fade');
        $sliderInfo['pauseonhover'] = $this->_scopeConfigObject->getValue('slider/slider/select_pauseonhover');
        $sliderInfo['loop'] = $this->_scopeConfigObject->getValue('slider/slider/select_loop');
        $sliderInfo['dots'] = $this->_scopeConfigObject->getValue('slider/slider/select_dots');
        $sliderInfo['imagecounts'] = $this->_scopeConfigObject->getValue('slider/slider/select_image_numbers');
        $sliderInfo['autoplay'] = $this->_scopeConfigObject->getValue('slider/slider/select_autoplay');
        return $sliderInfo;
    }
}
<?php
namespace Excellence\Instagram\Block\Adminhtml\Generic\Grid\Renderer;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;

class Slider extends AbstractRenderer
{
    private $_storeManager;
    /**

     * @param \Magento\Backend\Block\Context $context

     * @param array $data

     */

    public function __construct(

        \Magento\Backend\Block\Context $context, 

        StoreManagerInterface $storemanager, 

        array $data = []

        )

    {

        $this->_storeManager = $storemanager;

        parent::__construct($context, $data);

        $this->_authorization = $context->getAuthorization();

    }

    /**

     * Renders grid column

     *

     * @param Object $row

     * @return  string

     */
    public function render(DataObject $row)
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $rowData = $row->getData();
        $rowData = (array) json_decode($rowData['image_urls'],true);
        $html = "<ul class='bxslider'>";
        if(count($rowData)) {
            foreach ($rowData as $value) {
                $html .= "<li class='item'>
                            <img src='".$value."' />
                        </li>";
            }
        }
            
        $html.= "</ul>";
        $html.= "<script> require([
                'jquery'
                ], function($){
                  $(document).ready(function(){
                      $('.bxslider').bxSlider();
                    });
                });
                </script>";

        return $html;
        // return "<img height=75px; width=75px; src='".$mediaUrl."/".$rowData['image']."'>";
    }

}
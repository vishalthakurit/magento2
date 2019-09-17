<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Excellence\Instagram\Block\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Button extends Field
{
    protected  $_redirectUriController = 'instagram';
    protected $_baseStoreUrl;

    /**
     * @var string
     */
    protected $_template = 'Excellence_Instagram::system/config/collect.phtml';

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $baseStoreUrl,
        array $data = []
    ) {
        $this->_baseStoreUrl = $baseStoreUrl;
        parent::__construct($context, $data);
    }

    /**
     * Remove scope label
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }
    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for collect button
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('Excellence_Instagram/system_config/collect');
    }

    /**
     * Generate collect button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'collect_button',
                'label' => __('Instagram Connect'),
            ]
        );

        return $button->toHtml();
    }

    /**
     * Get Client Id
     *
     * @return string
     */
    public function getClientId()
    {
        $client_ID = $this->_scopeConfig->getValue(
            'instagramSection/setting/client_ID',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $client_ID;
    }

    /**
     * Get Redirect Url
     *
     * @return string
     */
    public function getRedirectUri()
    {
        $redirect_uri = $this->_baseStoreUrl->getStore()->getBaseUrl();
        $redirect_uri = $redirect_uri.$this->_redirectUriController;
        
        return $redirect_uri."/";
    }
}

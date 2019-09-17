<?php
/**
 * Copyright © 2015 Excellencecommerce. All rights reserved.
 */
namespace Excellence\Instagram\Helper;

/**
 * Instagram Config Helper
 */
class ExecuteApi extends \Magento\Framework\View\Element\Template
{
    public $_apiBaseUrl = "https://api.instagram.com/v1";

    public function getRecentPostFromInsta(){
        $access_Token = $this->_scopeConfig->getValue(
            'instagramSection/setting/access_Token',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $user_id = $this->_scopeConfig->getValue(
            'instagramSection/setting/user_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $apiUrl = $this->_apiBaseUrl."/users/self/media/recent/?access_token=$access_Token";
        
        $init = curl_init($apiUrl); 
        curl_setopt($init, CURLOPT_CONNECTTIMEOUT, 20); 
        curl_setopt($init, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($init, CURLOPT_SSL_VERIFYPEER, false); 

        $json = curl_exec($init); 
        $data = json_decode($json, TRUE);
        
        return $data;
    }

}

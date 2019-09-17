<?php
/**
 * Copyright © 2015 Excellencecommerce. All rights reserved.
 */
namespace Excellence\Instagram\Helper;

/**
 * Instagram Config Model
 */
class UserInfo extends \Magento\Framework\View\Element\Template
{
    public function getInstaUserConfigs(){

        $is_active = $this->_scopeConfig->getValue(
            'instagramSection/setting/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if($is_active){
            $client_id = $this->_scopeConfig->getValue(
                'instagramSection/setting/client_ID',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $client_secret_ID = $this->_scopeConfig->getValue(
                'instagramSection/setting/client_secret_ID',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $code_var = $this->_scopeConfig->getValue(
                'instagramSection/setting/code_var',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $url = $this->_scopeConfig->getValue(
                'instagramSection/setting/api_uri',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $access_token_parameters = array(
                'client_id' => $client_id,
                'client_secret' => $client_secret_ID,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $user_redirect_uri,
                'code' => $code_var,
            );

            $curl = curl_init($url);
            curl_setopt($curl,CURLOPT_POST,true);
            curl_setopt($curl,CURLOPT_POSTFIELDS,$access_token_parameters);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($curl);
            curl_close($curl);

            return $result; 
        }   
        return null;
    }

}
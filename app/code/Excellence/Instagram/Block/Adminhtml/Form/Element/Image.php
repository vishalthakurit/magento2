<?php

/**
 * Instagram Form Image File Element Block
 *
 */
namespace Excellence\Instagram\Block\Adminhtml\Form\Element;

class Image
{ 
    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        return $this->getValue();
    }
}

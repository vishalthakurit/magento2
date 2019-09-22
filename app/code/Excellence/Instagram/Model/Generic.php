<?php

namespace Excellence\Instagram\Model;

/**
 * Instagram Model
 *
 * @method \Excellence\Instagram\Model\Resource\Page _getResource()
 * @method \Excellence\Instagram\Model\Resource\Page getResource()
 */
class Generic extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Excellence\Instagram\Model\ResourceModel\Generic');
    }

}

<?php
/**
 * Copyright © 2015 Voice. All rights reserved.
 */
namespace Voice\Notification\Model\ResourceModel;

/**
 * Voice resource
 */
class Voice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('notification_voice', 'id');
    }

  
}

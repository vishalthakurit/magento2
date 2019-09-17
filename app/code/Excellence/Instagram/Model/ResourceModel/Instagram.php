<?php

namespace Excellence\Instagram\Model\ResourceModel;

/**
 * Instagram Resource Model
 */
class Instagram extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('excellence_instagram', 'instagram_id');
    }
}

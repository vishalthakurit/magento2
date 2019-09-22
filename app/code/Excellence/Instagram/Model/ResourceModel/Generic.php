<?php

namespace Excellence\Instagram\Model\ResourceModel;

/**
 * Instagram Resource Model
 */
class Generic extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('excellence_generic_slider', 'generic_id');
    }
}

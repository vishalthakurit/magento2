<?php

/**
 * Instagram Resource Collection
 */
namespace Excellence\Instagram\Model\ResourceModel\Generic;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Excellence\Instagram\Model\Generic', 'Excellence\Instagram\Model\ResourceModel\Generic');
    }
}

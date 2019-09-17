<?php
namespace Excellence\Instagram\Model\Config\Source;

class AutoPlay implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Yes')],
            ['value' => 0, 'label' => __('No')]];
    }
}



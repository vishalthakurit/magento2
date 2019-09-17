<?php
namespace Excellence\Instagram\Model\Config\Source;

class ListOption implements \Magento\Framework\Option\ArrayInterface
{
	/**
  * XML configuration path for Instagram Dropdown
  */
  const RECENT_IMAGE = 'recentImage';
  const HASHTAG_IMAGE = 'hashTagImage';
  
  public function toOptionArray()
  {
    return [
      [
        'value' => self::RECENT_IMAGE,
        'label' => __('Recently Uploaded Images on Instagram'),
      ],
      [
        'value' => self::HASHTAG_IMAGE,
        'label' => __('Image Uploaded With HashTag on Instagram')
      ]
    ];
  }
}
?>
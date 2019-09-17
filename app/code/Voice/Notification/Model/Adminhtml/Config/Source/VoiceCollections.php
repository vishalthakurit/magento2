<?php
namespace Voice\Notification\Model\Adminhtml\Config\Source;
 
class VoiceCollections implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        array $data = []
    ) {
        $this->_messageManager = $messageManager;
    }
    const USENGLISH = 1;
    const UKENGLISHFEMALE = 2;
    const UKENGLISHMALE = 3;
    const HINDI = 7;
    
    public function toOptionArray()
    {
        return [['value' => NULL, 'label' => __('-- Select Page --')],
                ['value' => self:: USENGLISH, 'label' => __('Google US English')], 
                ['value' => self:: UKENGLISHFEMALE, 'label' => __('Google UK English Female')],
                ['value' => self:: UKENGLISHMALE, 'label' => __('Google UK English Male')], 
                ['value' => self:: HINDI, 'label' => __('Google हिन्दी')],
                ]; 
    }
}
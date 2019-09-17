<?php

namespace Excellence\Base\Block\Adminhtml\System\Config\Form;

use Magento\Store\Model\ScopeInterface;

class Version extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_moduleList;
    protected $_moduleManager;
    protected $_productMetadata;
    protected $_serverAddress;
    protected $_storeManager;
    protected $_cacheManager;
    protected $_objectManager;

    protected $_wikiLink;// = 'http://wiki.xmagestore.com';
    protected $_moduleName;// = 'Geoip Store Switcher';

    public function __construct(
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\HTTP\PhpEnvironment\ServerAddress $serverAddress,
        \Magento\Framework\App\Cache\Proxy $cacheManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,

        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_moduleList       = $moduleList;
        $this->_moduleManager    = $moduleManager;
        $this->_storeManager     = $storeManager;
        $this->_productMetadata  = $productMetadata;
        $this->_serverAddress    = $serverAddress;
        $this->_cacheManager    = $cacheManager;
        $this->_objectManager    = $objectManager;
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->getModuleInfoHtml();
    }

    public function getWikiLink()
    {
        return $this->_wikiLink;
    }

    public function getModuleTitle()
    {
        return $this->_moduleName;
    }

    public function getModuleInfoHtml()
    {
        $m = $this->_moduleList->getOne($this->getModuleName());
        $html = '<tr><td class="label" colspan="4" style="text-align: left;"><div style="padding:10px;background-color:#f8f8f8;border:1px solid #ddd;margin-bottom:7px;">
            ' . $this->_moduleName . ' v' . $m['setup_version'] . ' was developed by <a href="https://xmagestore.com" target="_blank">xMagestore</a>.
            For manual & tutorials please refer to <a href="' . $this->_wikiLink . '" target="_blank">our online documentation<a/>.
         </div></td></tr>';

         return $html;
    }
}
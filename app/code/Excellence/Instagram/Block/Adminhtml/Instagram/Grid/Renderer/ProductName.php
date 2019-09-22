<?php
namespace Excellence\Instagram\Block\Adminhtml\Instagram\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class ProductName extends AbstractRenderer
{
  protected $_productRepository; 
  /**

   * @param \Magento\Backend\Block\Context $context

   * @param array $data

   */

  public function __construct(
    \Magento\Catalog\Model\ProductRepository $productRepository,
    \Magento\Backend\Block\Context $context,
    array $data = []
  ){
    $this->_productRepository = $productRepository;
    parent::__construct($context, $data);
  }

  /**

   * Renders grid column

   *

   * @param Object $row

   * @return  string

   */
  public function render(DataObject $row)
  {
      $rowData = $row->getData();
      return $this->_productRepository->getById($rowData['product_id'])->getName();
  }

}
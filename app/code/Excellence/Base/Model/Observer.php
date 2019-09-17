<?php
namespace Excellence\Base\Model;
class Observer 
{  
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productModel,
        \Magento\Customer\Model\CustomerFactory $customerModel,
        \Magento\Catalog\Model\Session $session,
        \Magento\Checkout\Model\Cart $cart
        ){
        $this->productModel = $productModel; 
        $this->customerModel = $customerModel;
        $this->sessionModelCatalog = $session;
        $this->cart = $cart;
    }

    public function baseRule($condition_data, $orderData) {
        $condition_array = array();
        $condition_success = array();

        $items = $orderData->getAllVisibleItems();
     
        foreach ($items as $item) {

            foreach ($condition_data['conditions'] as $cond) {
                $status = false;
                $condition_type = $cond['type'];
                $attribute = $cond['attribute'];
                $value = $cond['value'];
                $operator = $cond['operator'];
                /*
                 * return 0 if true -1 for false
                 */
                 
                $condition_check_address = strcmp(trim($condition_type), 'Magento\SalesRule\Model\Rule\Condition\Address');
                $condition_check_product = strcmp(trim($condition_type), 'Magento\SalesRule\Model\Rule\Condition\Product');
                $condition_check_customer = strcmp(trim($condition_type), 'Magento\SalesRule\Model\Rule\Condition\Customer');
                $condition_check_cumbine = strcmp(trim($condition_type), 'Magento\SalesRule\Model\Rule\Condition\Combine');
                $condition_check_subselect = strcmp(trim($condition_type), 'Magento\SalesRule\Model\Rule\Condition\Product\Subselect');
                $condition_check_salesrule_cumbine = strcmp(trim($condition_type), 'Magento\SalesRule\Model\Rule\Condition\Product\Combine');
                $condition_check_salesrule_product = strcmp(trim($condition_type), 'Magento\SalesRule\Model\Rule\Condition\Address\Product');
                $condition_check_salesrule_product_found = strcmp(trim($condition_type), 'Magento\SalesRule\Model\Rule\Condition\Product\Found');
                $condition_check_salesrule_product_attr_assigned = strcmp(trim($condition_type), 'Magento\SalesRule\Model\Rule\Condition\Product\Attribute\Assigned');

                $totalQuantity = $this->cart->getQuote()->getItemsQty();

                $product_id = $item->getProductId();
           
                $_product = $this->productModel->create()->load($product_id);

                /*
                 * check for conditon if condition is 'product' type
                 */

                if ($condition_check_product == 0) {
                    $options = $item->getProductOptions();
                    $type_attribute = $_product[$attribute];

                    if ($item->getProductType() == 'configurable') {
                        $productAttributeOptions = $_product->getTypeInstance(true)->getConfigurableAttributesAsArray($_product);

                        foreach ($options['attributes_info'] as $opInfo) {
                            $attLabel = $opInfo['label'];
                            foreach ($productAttributeOptions as $proAttr) {
                                $proAttrLabel = $proAttr['label'];

                                if (strcmp($attLabel, $proAttrLabel) == 0) {
                                    foreach ($proAttr['values'] as $attvalue) {

                                        if (trim($attvalue['label']) == trim($opInfo['value'])) {
                                            if (trim($attvalue['value_index']) == trim($value)) {
                                                $type_attribute = $attvalue['value_index'];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $status = $this->checkRules($attribute, $value, $operator, $type_attribute, $_product);
                }
                /*
                 * check for conditon if condition is 'customer' type
                 */
                if ($condition_check_customer == 0) {
                    $customerId = $orderData->getCustomerId();
                    $customer = $this->customerModel->create()->load($customerId);
                    $type_attribute = $customer[$attribute];
                    $status = $this->checkRules($attribute, $value, $operator, $type_attribute, $_product);
                }

                /*
                 * check for conditon if condition is 'address' type
                 */
                if ($condition_check_address == 0) {
                    if ($attribute == 'base_subtotal') {
                        if ($this->cart->getQuote()) {
                            $type_attribute = $this->cart->getQuote()->getBaseSubtotal();
                        }
                    } elseif ($attribute == 'total_qty') {
                        $type_attribute = $totalQuantity;
                    } elseif ($attribute == 'weight') {
                        $totalWeight = 0;
                        foreach ($items as $item) {
                            $orderedQty = $item->getQtyOrdered();
                            $weightItem = $item->getWeight();
                            $totalWeight += $weightItem * $orderedQty;
                        }
                        $type_attribute = $totalWeight;
                    } elseif ($attribute == 'payment_method') {
                        if ($this->cart->getQuote()) {
                            $paymentMethod = $this->sessionModelCatalog->getQuote()->getPayment()->getMethodInstance()->getCode();
                            if (!empty($paymentMethod)) {
                                $type_attribute = $this->sessionModelCatalog->getQuote()->getPayment()->getMethodInstance()->getCode();
                            }
                        }
                    } else {
                        $shippingAddress = $this->sessionModelCatalog->getQuote()->getShippingAddress();
                        if (!empty($shippingAddress[$attribute])) {
                            $type_attribute = $shippingAddress[$attribute];
                        }
                    }

                    $status = $this->checkRules($attribute, $value, $operator, $type_attribute);
                }

                /*
                 * check for conditon if condition is 'product_type_subselect' type
                 */

                if ($condition_check_subselect == 0) {
                    if ($attribute == 'qty') {
                        $type_attribute = $totalQuantity;
                    } elseif ($attribute == 'base_row_total') {
                        $subTotal = $orderData->getSubtotal();
                        $type_attribute = $subTotal;
                    }
                    $status = $this->checkRules($attribute, $value, $operator, $type_attribute);
                    if ($status == 1) {
                        $status = $this->baseRule($cond, $orderData);
                    }
                }
                /*
                 * check for conditon if condition is 'product' of 'subselect type
                 */
                if ($condition_check_salesrule_product == 0) {
                    $type_attribute = $_product[$attribute];
                    if ($attribute == 'quote_item_price') {
                        $type_attribute = $_product['price'];
                    } elseif ($attribute == 'quote_item_qty') {
                        $type_attribute = $totalQuantity;
                    } elseif ($attribute == 'quote_item_row_total') {
                        $totalItems = $this->cart->getQuote()->getItemsCount();
                        $type_attribute = $totalItems;
                    }

                    $status = $this->checkRules($attribute, $value, $operator, $type_attribute, $_product);
                }
                /*                    
                 *      
                 */
                if ($condition_check_salesrule_product_attr_assigned == 0) {
                    $type_attribute = $_product[$attribute];
                    $status = $this->checkRules($attribute, $value, $operator, $type_attribute, $_product);
                }
                /*
                 * check for conditon if condition is 'combine' type
                 */
                if ($condition_check_cumbine == 0) {
                    $status = $this->baseRule($cond, $orderData);
                }
                /*
                 * check for conditon if condition is 'sales_combine' type
                 */
                if ($condition_check_salesrule_cumbine == 0) {
                    $status = $this->baseRule($cond, $orderData);
                }
                /*
                 * check for conditon if condition is 'sales_product found' type
                 */
                if ($condition_check_salesrule_product_found == 0) {
                    $status = $this->baseRule($cond, $orderData);
                }
                $condition_array[] = $status;
            }
            // Mage::Log($condition_array);
            $condition_success[] = $this->getAggregator($condition_array, $condition_data['aggregator'], $condition_data['value']);
            $condition_array = array();
        }

        if (in_array("0", $condition_success)) {
            $conditionStatus = 0;
        } else {
            $conditionStatus = 1;
        }
        
        return $conditionStatus;
    }

    public function getAggregator($condition_array, $aggregator, $aggregator_value) {
        $first = 0;
        $result = 0;
        switch ($aggregator) {
            case "all":
                if (!in_array(!$aggregator_value, $condition_array)) {
                    $first = true;
                }
                if ($first) {
                    $result = true;
                }
                break;
            case "any":
                if (in_array($aggregator_value, $condition_array)) {
                    $first = true;
                }
                if ($first) {
                    $result = true;
                }
                break;
        }
        return $result;
    }

    public function checkRules($attribute, $value, $operator, $type_attribute, $_product = null) {
        $status = false;
        if ($attribute == 'category_ids') {
            $value_array = explode(',', $value);
            $value_array = array_map('trim', $value_array);
            $categories = $_product->getCategoryIds();
            switch ($operator) {
                case '==':
                    $result = array_intersect($categories, $value_array);
                    ((sizeof($result) > 0)) ? ($status = true) : ($status = 0);
                    break;
                case '!=':
                    $result = array_intersect($categories, $value_array);
                    ((sizeof($result) == 0)) ? ($status = true) : ($status = 0);
                    break;
                case "{}":
                    $result = array_intersect($categories, $value_array);
                    ((sizeof($result) > 0)) ? ($status = true) : ($status = 0);
                    break;
                case '!{}':
                    $result = array_intersect($categories, $value_array);
                    ((sizeof($result) == 0)) ? ($status = true) : ($status = 0);
                    break;
                case "()":
                    $result = array_intersect($categories, $value_array);
                    ((sizeof($result) > 0)) ? ($status = true) : ($status = 0);
                    break;
                case '!()':
                    $result = array_intersect($categories, $value_array);
                    ((sizeof($result) == 0)) ? ($status = true) : ($status = 0);
                    break;
                case 'is_assigned':
                    ((count($categories) > 0)) ? ($status = true) : ($status = 0);
                    break;
                case 'is_not_assigned':
                    ((count($categories) == 0)) ? ($status = true) : ($status = 0);
                    break;
            }
        } else {
            switch ($operator) {
                case "<=":
                    if (strtotime($value)) {
                        $status = $this->compareDate($value, $type_attribute, $operator);
                    } else {
                        (($type_attribute <= $value)) ? ($status = true) : ($status = 0);
                    }
                    break;
                case '==':
                    if (strtotime($value)) {
                        $status = $this->compareDate($value, $type_attribute, $operator);
                    } else {
                        (($type_attribute == $value)) ? ($status = true) : ($status = 0);
                    }
                    break;
                case ">=":
                    if (strtotime($value)) {
                        $status = $this->compareDate($value, $type_attribute, $operator);
                    } else {
                        (($type_attribute >= $value)) ? ($status = true) : ($status = 0);
                    }
                    break;
                case '!=':
                    if (strtotime($value)) {
                        $status = $this->compareDate($value, $type_attribute, $operator);
                    } else {
                        (($type_attribute != $value)) ? ($status = true) : ($status = 0);
                    }
                    break;
                case "<":
                    if (strtotime($value)) {
                        $status = $this->compareDate($value, $type_attribute, $operator);
                    } else {
                        (($type_attribute < $value)) ? ($status = true) : ($status = 0);
                    }
                    break;
                case '>':
                    if (strtotime($value)) {
                        $status = $this->compareDate($value, $type_attribute, $operator);
                    } else {
                        (($type_attribute > $value)) ? ($status = true) : ($status = 0);
                    }
                    break;
                case "{}":
                    $value_array = explode(',', $value);
                    $value_array = array_map('trim', $value_array);
                    ((in_array($type_attribute, $value_array))) ? ($status = true) : ($status = 0);
                    break;
                case '!{}':
                    $value_array = explode(',', $value);
                    $value_array = array_map('trim', $value_array);
                    ((!in_array($type_attribute, $value_array))) ? ($status = true) : ($status = 0);
                    break;
                case "()":
                    $value_array = explode(',', $value);
                    $value_array = array_map('trim', $value_array);
                    ((in_array($type_attribute, $value_array))) ? ($status = true) : ($status = 0);
                    break;
                case '!()':
                    $value_array = explode(',', $value);
                    $value_array = array_map('trim', $value_array);
                    ((!in_array($type_attribute, $value_array))) ? ($status = true) : ($status = 0);
                    break;
                case 'is_assigned':
                    (!empty($type_attribute)) ? ($status = true) : ($status = 0);
                    break;
                case 'is_not_assigned':
                    (empty($type_attribute)) ? ($status = true) : ($status = 0);
                    break;
            }
        }
        return $status;
    }

    public function compareDate($value, $type_attribute, $operator) {
        $status = false;
        $time_value = strtotime($value);
        $newformat_value_time = date('Y-m-d', $time_value);
        $time_attribute = strtotime($type_attribute);
        $newformat_attri_time = date('Y-m-d', $time_attribute);
        switch ($operator) {
            case "==":
                (($newformat_attri_time == $newformat_value_time)) ? ($status = true) : ($status = 0);
                break;
            case "!=":
                (($newformat_attri_time != $newformat_value_time)) ? ($status = true) : ($status = 0);
                break;
            case ">":
                (($newformat_attri_time > $newformat_value_time)) ? ($status = true) : ($status = 0);
                break;
            case "<":
                (($newformat_attri_time < $newformat_value_time)) ? ($status = true) : ($status = 0);
                break;
            case ">=":
                (($newformat_attri_time >= $newformat_value_time)) ? ($status = true) : ($status = 0);
                break;
            case "<=":
                (($newformat_attri_time <= $newformat_value_time)) ? ($status = true) : ($status = 0);
                break;
        }

        return $status;
    }

}

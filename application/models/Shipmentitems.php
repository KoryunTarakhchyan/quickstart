<?php

class Atlas_Model_Shipmentitems
{
    protected $_id;
    protected $_orderId;
    protected $_orderItemId;
    protected $_lineItemKey;
    protected $_sku;
    protected $_skuqty;
    protected $_name;
    protected $_imageUrl;
    protected $_weight;
    protected $_quantity;
    protected $_unitPrice;
    protected $_taxAmount;
    protected $_shippingAmount;
    protected $_warehouseLocation;
    protected $_itemoptions;
    protected $_productId;
    protected $_fulfillmentSku;
    protected $_adjustment;
    protected $_upc;
    protected $_createDate;
    protected $_modifyDate;
    
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
 
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid shipment property');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid shipment property');
        }
        return $this->$method();
    }
    
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }    
 
     public function toArray()
    {
        $class_vars = get_class_vars(__CLASS__);
        $results    = array();
        foreach( $class_vars as $index=>$value ){
            $results[substr($index, 1)] = $this->$index;
        }
        return $results;
    } #end toArray() function  
    
    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }
 
    public function getId()
    {
        return $this->_id;
    }
    
    public function setOrderId($id)
    {
        $this->_orderId = (int) $id;
        return $this;
    }
 
    public function getOrderId()
    {
        return $this->_orderId;
    }
    public function setOrderItemId($id)
    {
        $this->_orderItemId = (int) $id;
        return $this;
    }
 
    public function getOrderItemId()
    {
        return $this->_orderItemId;
    }
    
    public function setLineItemKey($id)
    {
        $this->_lineItemKey = (int) $id;
        return $this;
    }
 
    public function getLineItemKey()
    {
        return $this->_lineItemKey;
    }
    
    public function setSku($text)
    {
        $this->_sku = (string) $text;
        return $this;
    }
 
    public function getSku()
    {
        return $this->_sku;
    }
    
    public function setSkuqty($qt)
    {
        $this->_skuqty = (int) $qt;
        return $this;
    }
 
    public function getSkuqty()
    {
        return $this->_skuqty;
    }
    
    public function setName($text)
    {
        $this->_name = (string) $text;
        return $this;
    }
 
    public function getName()
    {
        return $this->_name;
    }
    public function setImageUrl($text)
    {
        $this->_imageUrl = (string) $text;
        return $this;
    }
 
    public function getImageUrl()
    {
        return $this->_imageUrl;
    }
    public function setWeight($weight)
    {
        $this->_weight =  $weight;
        return $this;
    }
 
    public function getWeight()
    {
        return $this->_weight;
    }
    
    public function setQuantity($id)
    {
        $this->_quantity = (int) $id;
        return $this;
    }
 
    public function getQuantity()
    {
        return $this->_quantity;
    }
    public function setUnitPrice($id)
    {
        $this->_unitPrice = (int) $id;
        return $this;
    }
 
    public function getUnitPrice()
    {
        return $this->_unitPrice;
    }
    public function setTaxAmount($id)
    {
        $this->_taxAmount = (int) $id;
        return $this;
    }
 
    public function getTaxAmount()
    {
        return $this->_taxAmount;
    }
    public function setShippingAmount($id)
    {
        $this->_shippingAmount = (int) $id;
        return $this;
    }
 
    public function getShippingAmount()
    {
        return $this->_shippingAmount;
    }
    public function setWarehouseLocation($text)
    {
        $this->_warehouseLocation = (string) $text;
        return $this;
    }
 
    public function getWarehouseLocation()
    {
        return $this->_warehouseLocation;
    }
    public function setItemoptions($text)
    {
        $this->_itemoptions = (string) $text;
        return $this;
    }
 
    public function getItemoptions()
    {
        return $this->_itemoptions;
    }
    
    public function setProductId($id)
    {
        $this->_productId = (int) $id;
        return $this;
    }
 
    public function getProductId()
    {
        return $this->_productId;
    }
    public function setFulfillmentSku($text)
    {
        $this->_fulfillmentSku = (string) $text;
        return $this;
    }
 
    public function getFulfillmentSku()
    {
        return $this->_id;
    }
    
    public function setAdjustment($text)
    {
        $this->_adjustment = (string) $text;
        return $this;
    }
 
    public function getAdjustment()
    {
        return $this->_adjustment;
    }
    
    public function setUpc($text)
    {
        $this->_upc = (string) $text;
        return $this;
    }
 
    public function getUpc()
    {
        return $this->_upc;
    }
    
    
    public function setCreateDate($date)
    {
        $this->_createDate = $date;
        return $this;
    }
 
    public function getCreateDate()
    {
        return $this->_createDate;
    }
    
    
    public function setModifyDate($date)
    {
        $this->_modifyDate = $date;
        return $this;
    }
 
    public function getModifyDate()
    {
        return $this->_modifyDate;
    }
}
<?php

class Atlas_Model_ShipmentsHeader

{

    protected $_id;
    protected $_shipmentId;
    protected $_orderId;
    protected $_orderKey;
    protected $_userId;
    protected $_customerEmail;
    protected $_orderNumber;
    protected $_createDate;
    protected $_shipDate;
    protected $_shipmentCost;
    protected $_insuranceCost;
    protected $_trackingNumber;
    protected $_isReturnLabel;
    protected $_batchNumber;
    protected $_carrierCode;
    protected $_serviceCode;
    protected $_packageCode;
    protected $_confirmation;
    protected $_warehouseId;
    protected $_voided;
    protected $_voidDate;
    protected $_marketplaceNotified;
    protected $_notifyErrorMessage;
    protected $_shipTo_name;
    protected $_shipTo_company;
    protected $_shipTo_street1;
    protected $_shipTo_street2;
    protected $_shipTo_street3;
    protected $_shipTo_city;
    protected $_shipTo_state;
    protected $_shipTo_postalCode;
    protected $_shipTo_country;
    protected $_shipTo_phone;
    protected $_shipTo_residential;
    protected $_shipTo_addressVerified;
    protected $_weight_value;
    protected $_weight_units;
    protected $_weightUnits;
    protected $_storeId;
    
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
    
    public function setShipmentId($id)
    {
        $this->_shipmentId = (int) $id;
        return $this;
    }
 
    public function getShipmentId()
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
    
    public function setOrderKey($id)
    {
        $this->_orderKey = (int) $id;
        return $this;
    }
 
    public function getOrderKey()
    {
        return $this->_orderKey;
    }
    
    public function setUserId($text)
    {
        $this->_userId = (string) $text;
        return $this;
    }
 
    public function getUserId()
    {
        return $this->_userId;
    }
    
    public function setCustomerEmail($text)
    {
        $this->_customerEmail = (string) $text;
        return $this;
    }
 
    public function getCustomerEmail()
    {
        return $this->_customerEmail;
    }
    
    public function setOrderNumber($text)
    {
        $this->_orderNumber = (string) $text;
        return $this;
    }
 
    public function getOrderNumber()
    {
        return $this->_orderNumber;
    }
    
    public function setCreateDate($cd)
    {
        $this->_createDate =  $cd;
        return $this;
    }
 
    public function getCreateDate()
    {
        return $this->_createDate;
    }
    
    public function setShipDate($cd)
    {
        $this->_shipDate =  $cd;
        return $this;
    }
 
    public function getShipDate()
    {
        return $this->_shipDate;
    }
    
    public function setShipmentCost($cost)
    {
        $this->_shipmentCost =  $cost;
        return $this;
    }
 
    public function getShipmentCost()
    {
        return $this->_shipmentCost;
    }
    
    public function setInsuranceCost($cost)
    {
        $this->_insuranceCost =  $cost;
        return $this;
    }
 
    public function getInsuranceCost()
    {
        return $this->_insuranceCost;
    }
    
    public function setTrackingNumber($text)
    {
        $this->_trackingNumber = (string) $text;
        return $this;
    }
 
    public function getTrackingNumber()
    {
        return $this->_trackingNumber;
    }
    
    public function setIsReturnLabel($text)
    {
        $this->_isReturnLabel = (string) $text;
        return $this;
    }
 
    public function getIsReturnLabel()
    {
        return $this->_isReturnLabel;    
    }
    
    public function setBatchNumber($number)
    {
        $this->_batchNumber = (int) $number;
        return $this;
    }
 
    public function getBatchNumber()
    {
        return $this->_batchNumber;
    
    }
    
    public function setCarrierCode($text)
    {
        $this->_carrierCode = (string) $text;
        return $this;
    }
 
    public function getCarrierCode()
    {
        return $this->_carrierCode;
    
    }
    
    public function setServiceCode($text)
    {
        $this->_serviceCode = (string) $text;
        return $this;
    }
 
    public function getServiceCode()
    {
        return $this->_serviceCode;
    
    }
    
    public function setPackageCode($text)
    {
        $this->_packageCode = (string) $text;
        return $this;
    }
 
    public function getPackageCode()
    {
        return $this->_packageCode;
    
    }
    
    public function setConfirmation($text)
    {
        $this->_confirmation = (string) $text;
        return $this;
    }
 
    public function getConfirmation()
    {
        return $this->_confirmation;
    
    }
    
    public function setWarehouseId($id)
    {
        $this->_warehouseId = (int) $id;
        return $this;
    }
 
    public function getWarehouseId()
    {
        return $this->_warehouseId;
    
    }
    
    public function setVoided($text)
    {
        $this->_voided = (string) $text;
        return $this;
    }
 
    public function getVoided()
    {
        return $this->_voided;
    
    }
    
    public function setVoidDate($vd)
    {
        $this->_voidDate = $vd;
        return $this;
    }
 
    public function getVoidDate()
    {
        return $this->_voidDate;
    
    }
    
    public function setMarketplaceNotified($number)
    {
        $this->_marketplaceNotified = (int) $number;
        return $this;
    }
 
    public function getMarketplaceNotified()
    {
        return $this->_marketplaceNotified;
    
    }
    
    public function setNotifyErrorMessage($text)
    {
        $this->_notifyErrorMessage = (string) $text;
        return $this;
    }
 
    public function getNotifyErrorMessage()
    {
        return $this->_notifyErrorMessage;
    
    }
    
    public function setShipTo_name($text)
    {
        $this->_shipTo_name = (string) $text;
        return $this;
    }
 
    public function getShipTo_name()
    {
        return $this->_shipTo_name;
    
    }
    
    public function setShipTo_company($text)
    {
        $this->_shipTo_company = (string) $text;
        return $this;
    }
 
    public function getShipTo_company()
    {
        return $this->_shipTo_company;
    
    }
    
    public function setShipTo_street1($text)
    {
        $this->_shipTo_street1 = (string) $text;
        return $this;
    }
 
    public function getShipTo_street1()
    {
        return $this->_shipTo_street1;
    
    }
    
    public function setShipTo_street2($text)
    {
        $this->_shipTo_street2 = (string) $text;
        return $this;
    }
 
    public function getShipTo_street2()
    {
        return $this->_shipTo_street2;
    
    }
    
    public function setShipTo_street3($text)
    {
        $this->_shipTo_street3 = (string) $text;
        return $this;
    }
 
    public function getShipTo_street3()
    {
        return $this->_shipTo_street3;
    
    }
    
    public function setShipTo_city($text)
    {
        $this->_shipTo_city = (string) $text;
        return $this;
    }
 
    public function getShipTo_city()
    {
        return $this->_shipTo_city;
    
    }
    
    public function setShipTo_state($text)
    {
        $this->_shipTo_state = (string) $text;
        return $this;
    }
 
    public function getShipTo_state()
    {
        return $this->_shipTo_state;
    
    }
    
    public function setShipTo_postalCode($text)
    {
        $this->_shipTo_postalCode = (string) $text;
        return $this;
    }
 
    public function getShipTo_postalCode()
    {
        return $this->_shipTo_postalCode;
    
    }
    
    public function setShipTo_country($text)
    {
        $this->_shipTo_country = (string) $text;
        return $this;
    }
 
    public function getShipTo_country()
    {
        return $this->_shipTo_country;
    
    }
    
    public function setShipTo_phone($text)
    {
        $this->_shipTo_phone = (string) $text;
        return $this;
    }
 
    public function getShipTo_phone()
    {
        return $this->_shipTo_phone;
    
    }
    
    public function setShipTo_residential($text)
    {
        $this->_shipTo_residential = (string) $text;
        return $this;
    }
 
    public function getShipTo_residential()
    {
        return $this->_shipTo_residential;
    
    }
    
    public function setShipTo_addressVerified($text)
    {
        $this->_shipTo_addressVerified = (string) $text;
        return $this;
    }
 
    public function getShipTo_addressVerified()
    {
        return $this->_shipTo_addressVerified;
    
    }
    
    public function setWeight_value($number)
    {
        $this->_weight_value = (int) $number;
        return $this;
    }
 
    public function getWeight_value()
    {
        return $this->_weight_value;
    
    }
    
    public function setWeight_units($text)
    {
        $this->_weight_units = (string) $text;
        return $this;
    }
 
    public function getWeight_units()
    {
        return $this->_weight_units;
    
    }
    
    public function setWeightUnits($number)
    {
        $this->_weightUnits = (int) $number;
        return $this;
    }
 
    public function getWeightUnits()
    {
        return $this->_weightUnits;
    
    }
    
    public function setStoreId($number)
    {
        $this->_storeId = (int) $number;
        return $this;
    }
 
    public function getStoreId()
    {
        return $this->_weightUnits;
    
    }
    
}


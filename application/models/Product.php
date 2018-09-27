<?php

class Atlas_Model_Product
{

    protected $_id;
	protected $_name;
	protected $_price;
	protected $_quantity;
    protected $_created_at;
    protected $_updated_at;
 
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
            throw new Exception('Invalid ticket property');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid ticket property');
        }
        return $this->$method();
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
 
    public function setPrice($text)
    {
        $this->_price =  $text;
        return $this;
    }
 
    public function getPrice()
    {
        return $this->_price;
    } 
 
    public function setQuantity($qtt)
    {
        $this->_quantity = (int) $qtt;
        return $this;
    }
 
    public function getQuantity()
    {
        return $this->_quantity;
    } 
     
    public function setCreated_at($cd)
    {
        $this->_created_at = $cd;
        return $this;
    }
 
    public function getCreated_at()
    {
        return $this->_created_at;
    }
     
    public function setUpdated_at($ud)
    {
        $this->_updated_at = $ud;
        return $this;
    }
 
    public function getUpdated_at()
    {
        return $this->_updated_at;
    }
     
    
 
    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }
 
    public function getId()
    {
        return $this->_id;
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

}


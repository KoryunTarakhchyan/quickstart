<?php

class Atlas_Model_Inventory
{

    protected $_id;
	protected $_name;
	protected $_model;
	protected $_core;
    protected $_memory;
    protected $_os;
    protected $_tag;
    protected $_location;
    protected $_support_date;
    protected $_type;
 
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
 
    public function setModel($text)
    {
        $this->_model = (string) $text;
        return $this;
    }
 
    public function getModel()
    {
        return $this->_model;
    } 
 
    public function setCore($text)
    {
        $this->_core = (string) $text;
        return $this;
    }
 
    public function getCore()
    {
        return $this->_core;
    } 
     
    public function setMemory($text)
    {
        $this->_memory = $text;
        return $this;
    }
 
    public function getMemory()
    {
        return $this->_memory;
    }
     
    public function setOs($text)
    {
        $this->_os = $text;
        return $this;
    }
 
    public function getOs()
    {
        return $this->_os;
    }
     
    public function setTag($text)
    {
        $this->_tag = $text;
        return $this;
    }
 
    public function getTag()
    {
        return $this->_tag;
    }
 
    public function setLocation($lid)
    {
        $this->_location = (int) $lid;
        return $this;
    }
 
    public function getLocation()
    {
        return $this->_location;
    }
 
    public function setSupport_date($sd)
    {
        $this->_support_date = $sd;
        return $this;
    }
 
    public function getSupport_date()
    {
        return $this->_support_date;
    }
 
    public function setType($type)
    {
        $this->_type = (int) $type;
        return $this;
    }
 
    public function getType()
    {
        return $this->_type;
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

    // public function setOptions(array $options)
    // {

    //     // get a list of all setter methods and set each

    //     // value from the given array into the object

    //     $methods = get_class_methods($this);

    //     foreach( $options as $key=>$value ){

    //         $method = 'set' . ucfirst(strtolower($key));

    //         if( in_array($method, $methods) ){

    //                         $this->$method($value);

    //         }

    //     }

    //     return $this;

    // } #end setOptions function
 
     public function toArray()
    {

        $class_vars = get_class_vars(__CLASS__);

        $results    = array();

        foreach( $class_vars as $index=>$value ){

                        $results[substr($index, 1)] = $this->$index;

        }

        return $results;

    } #end toArray() function  

}


<?php

class Atlas_Model_Ticket
{

    protected $_id;
	protected $_name;
	protected $_subject;
	protected $_detail;
    protected $_createdDate;
    protected $_updatedDate;
    protected $_requestedDate;
    protected $_processedBy;
    protected $_userId;
    protected $_status;
 
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
 
    public function setSubject($text)
    {
        $this->_subject = (string) $text;
        return $this;
    }
 
    public function getSubject()
    {
        return $this->_subject;
    } 
 
    public function setDetail($text)
    {
        $this->_detail = (string) $text;
        return $this;
    }
 
    public function getDetail()
    {
        return $this->_detail;
    } 
     
    public function setCreatedDate($cd)
    {
        $this->_createdDate = $cd;
        return $this;
    }
 
    public function getCreatedDate()
    {
        return $this->_createdDate;
    }
     
    public function setUpdatedDate($ud)
    {
        $this->_updatedDate = $ud;
        return $this;
    }
 
    public function getUpdatedDate()
    {
        return $this->_updatedDate;
    }
     
    public function setRequestedDate($rd)
    {
        $this->_requestedDate = $rd;
        return $this;
    }
 
    public function getRequestedDate()
    {
        return $this->_requestedDate;
    }
 
    public function setRequestedBy($rby)
    {
        $this->_requestedBy = (int) $rby;
        return $this;
    }
 
    public function getRequestedBy()
    {
        return $this->_requestedBy;
    }
 
    public function setUserId($uid)
    {
        $this->_userId = (int) $uid;
        return $this;
    }
 
    public function getUserId()
    {
        return $this->_userId;
    }
 
    public function setStatus($status)
    {
        $this->_status = (int) $status;
        return $this;
    }
 
    public function getStatus()
    {
        return $this->_status;
    }
 
 
    public function setProcessedBy($text)
    {
        $this->_processedBy =  (string) $text;
        return $this;
    }
 
    public function getProcessedBy()
    {
        return $this->_processedBy;
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


<?php

class Atlas_Model_Picklist
{
	protected $_pl_id;
	protected $_so_no;
	protected $_date_time;
	protected $_user_id;
	protected $_picklist;
	protected $_invoicing;
	protected $_shipment;
	protected $_carrier;
	protected $_tracking;
	protected $_i_date_time;
	protected $_s_date_time;
	protected $_i_user_id;
	protected $_s_user_id;
	
	public function __construct(array $options = NULL)
	{
		// if attributes were given set the base values
		if( is_array($options) ){
			$this->setOptions($options);
		}
	} #end __construct function
	
	
	public function __set($name, $value)
	{
		// if an unknown variable is used throw exception
		$method = 'set' . $name;
		if( !method_exists($this, $method) ){
			throw new Exception('Invalid picklist property used');
		}
		$this->$method($value);
	} #end __set function
	
	
	public function __get($name)
	{
		// if an unknown variable is used throw exception
		$method = 'get' . $name;
		if( !method_exists($this, $method) ){
			throw new Exception('Invalid picklist property used');
		}
		return $this->method();
	} #end __get function
	
	
	public function setOptions(array $options)
	{
		// get a list of all setter methods and set each
		// value from the given array into the object
		$methods = get_class_methods($this);
		foreach( $options as $key=>$value ){
			$method = 'set' . ucfirst(strtolower($key));
			if( in_array($method, $methods) ){
				$this->$method($value);
			}
		}
		return $this;
	} #end setOptions function
	
	
	public function setPl_id($pl_id)
	{
		$this->_pl_id = $pl_id;
		return $this;
	} #end setPl_id function
	
	
	public function getPl_id()
	{
		return $this->_pl_id;
	} #end getPl_id function
	
	
	public function setSo_no($so_no)
	{
		$this->_so_no = $so_no;
		return $this;
	} #end setSo_no function
	
	
	public function getSo_no()
	{
		return $this->_so_no;
	} #end getSo_no function
	
	
	public function setDate_time($date_time)
	{
		$this->_date_time = $date_time;
		return $this;
	} #end setDate_time function
	
	
	public function getDate_time()
	{
		return $this->_date_time;
	} #end getDate_time function
	
	
	public function setUser_id($user_id)
	{
		$this->_user_id = $user_id;
		return $this;
	} #end setUser_id function
	
	
	public function getUser_id()
	{
		return $this->_user_id;
	} #end getUser_id function
	
	
	public function setPicklist($picklist)
	{
		$this->_picklist = $picklist;
		return $this;
	} #end setPicklist function
	
	
	public function getPicklist()
	{
		return $this->_picklist;
	} #end getPicklist function
	
	
	public function setInvoicing($invoicing)
	{
		$this->_invoicing = $invoicing;
		return $this;
	} #end setInvoicing function
	
	
	public function getInvoicing()
	{
		return $this->_invoicing;
	} #end getInvoicing function
	
	
	public function setShipment($shipment)
	{
		$this->_shipment = $shipment;
		return $this;
	} #end setShipment function
	
	
	public function getShipment()
	{
		return $this->_shipment;
	} #end getShipment function
	
	
	public function setCarrier($carrier)
	{
		$this->_carrier = $carrier;
		return $this;
	} #end setCarrier function
	
	
	public function getCarrier()
	{
		return $this->_carrier;
	} #end getCarrier function
	
	
	public function setTracking($tracking)
	{
		$this->_tracking = $tracking;
		return $this;
	} #end setTracking function
	
	
	public function getTracking()
	{
		return $this->_tracking;
	} #end getTracking function
	
	
	public function setI_date_time($date_time)
	{
		$this->_i_date_time = $date_time;
		return $this;
	} #end setI_date_time function
	
	
	public function getI_date_time()
	{
		return $this->_i_date_time;
	} #end getI_date_time function
	
	
	public function setS_date_time($date_time)
	{
		$this->_s_date_time = $date_time;
		return $this;
	} #end setS_date_time function
	
	
	public function getS_date_time()
	{
		return $this->_s_date_time;
	} #end getS_date_time function
	
	
	public function setI_user_id($user_id)
	{
		$this->_i_user_id = $user_id;
		return $this;
	} #end setI_user_id function
	
	
	public function getI_user_id()
	{
		return $this->_i_user_id;
	} #end getI_user_id function
	
	
	public function setS_user_id($user_id)
	{
		$this->_s_user_id = $user_id;
		return $this;
	} #end setS_user_id function
	
	
	public function getS_user_id()
	{
		return $this->_s_user_id;
	} #end getS_user_id function
	
	
	public function toArray()
	{
		$class_vars = get_class_vars(__CLASS__);
		$results    = array();
		foreach( $class_vars as $index=>$value ){
			$results[substr($index, 1)] = $this->$index;
		}
		return $results;
	} #end toArray function

}

?>
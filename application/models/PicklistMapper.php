<?php

class Atlas_Model_PicklistMapper
{
	protected $_dbTable;
	protected $_db  =   'atlas_dev';
	// set the default db handle
	public function setDbTable($dbTable)
	{
		// if a string was given return an object
		if( is_string($dbTable) ){
			$dbTable = new $dbTable();
		}
		// ensure the dbTable is of the correct instance
		if( !$dbTable instanceof Zend_Db_Table_Abstract ){
			throw new Exception('Invalid table data object provided');
		}
		
		// set the db table and return the handle
		$this->_dbTable = $dbTable;
		return $this;
	} #end setDbTable() function
	
	// return the default db handle
	public function getDbTable()
	{
		// if the object is not set, set it and return it
		if( NULL === $this->_dbTable ){
			$this->setDbTable('Atlas_Model_DbTable_Picklist');
		}
		return $this->_dbTable;
	} #end getDbTable() function
	
	// save the attributes of a given db object
	public function save(Atlas_Model_Picklist $picklist)
	{
		// push the data into an array
		$data = $picklist->toArray();
		
		// if the row in the db doesnt exist create the row
		// otherwise update the existing row
		if( NULL === ($pl_id = $picklist->getPl_id()) || $pl_id == 0 ){
			unset($data['pl_id']);
			$pl_id = $this->getDbTable()->insert($data);
			return $pl_id;
		}
		else {
			$this->getDbTable()->update($data, array('pl_id = ?' => $pl_id));
			return $pl_id;
		}
	} #end save() function
	
	// remove a row from the database that matches the id given
	public function remove($pl_id)
	{
		$this->getDbTable()->delete("pl_id='$pl_id'");
		
	} #end remove() function
	
	// find a row in the database based on the primary key and set the values
	// in the db object given by the user
	public function find($pl_id)
	{
		$picklist = new Atlas_Model_Picklist();
		
		// attempt to locate the row in the database
		// if it doesn't exist throw an exception
		$result = $this->getDbTable()->find($pl_id);
		if( 0 == count($result) ){
			throw new Exception("Given entry doesn't exist");
		}
		
		// get the data and push it to the object
		$row = $result->current();
		$picklist->setOptions($row->toArray());
		
		return $picklist;
		
	} #end find() function
	
	// find all entries from the database for the given table
	public function fetchAll()
	{
		// gather all of the entries in the database
		// and push their values into an array
		$resultSet = $this->selectAll()->query()->fetchAll();
		$entries   = array();
		foreach( $resultSet as $row ){
			$entry = new Atlas_Model_Picklist();
			$entry->setOptions($row);
			
			$entries[] = $entry;
		}
		
		// return the results
		return $entries;
	} #end fetchAll() function
	
	// transform a select statement into a result set
	public function fetch($select = NULL)
	{
		if( $select != NULL ) {
			return $select->query()->fetchAll();
		} else {
			return array();
		}
		
	} #end fetch() function
	
	// return a select statement for the table
	public function selectAll()
	{
		$select = $this->getDbTable()->select();
		$select->setIntegrityCheck(false)
			->from(array("p"=>"pick_list_archive"),
				array("p.pl_id", "p.so_no", "p.date_time", "p.user_id", "p.picklist", "p.invoicing", "p.shipment", 
					"p.carrier", "p.tracking", "p.i_date_time", "p.s_date_time", "p.i_user_id", "p.s_user_id"))
			->order("p.date_time DESC");
			   
		return $select;
	} #end selectAll() function
	
	public function getArchiveSearch( $so="", $begin="", $end="", $user_id=0, $type=1 )
	{
		$select = $this->getDbTable()->select();
		$select->setIntegrityCheck(false);
		if( $type == 1 ) {
			$select->from(array("p"=>"pick_list_archive"),
					array("p.pl_id", "p.so_no", "p.date_time", "p.user_id", "u.name"))
				->join(array("u"=>"$this->_db.users"), "p.user_id=u.user_id", array());
		} else if( $type == 2 ) {
			$select->from(array("p"=>"pick_list_archive"),
					array("p.pl_id", "p.so_no", "p.i_date_time", "p.i_user_id", "u.name"))
				->join(array("u"=>"$this->_db.users"), "p.i_user_id=u.user_id", array());
		} else if( $type == 3 ) {
			$select->from(array("p"=>"pick_list_archive"),
					array("p.pl_id", "p.so_no", "p.s_date_time", "p.s_user_id", "u.name"))
				->join(array("u"=>"$this->_db.users"), "p.s_user_id=u.user_id", array());
		}
		if( trim($so) != "" ) {
			$select->where("p.so_no LIKE '%".$so."%'", $so);
		} if( trim($begin) != "" && $type == 1 ) {
			$select->where("DATE_FORMAT(p.date_time, '%Y-%m-%d') >= ?", date("Y-m-d", strtotime($begin)));
		} if( trim($begin) != "" && $type == 2 ) {
			$select->where("DATE_FORMAT(p.i_date_time, '%Y-%m-%d') >= ?", date("Y-m-d", strtotime($begin)));
		} if( trim($begin) != "" && $type == 3 ) {
			$select->where("DATE_FORMAT(p.s_date_time, '%Y-%m-%d') >= ?", date("Y-m-d", strtotime($begin)));
		} if( trim($end) != "" && $type == 1 ) {
			$select->where("DATE_FORMAT(p.date_time, '%Y-%m-%d') <= ?", date("Y-m-d", strtotime($end)));
		} if( trim($end) != "" && $type == 2 ) {
			$select->where("DATE_FORMAT(p.i_date_time, '%Y-%m-%d') <= ?", date("Y-m-d", strtotime($end)));
		} if( trim($end) != "" && $type == 3 ) {
			$select->where("DATE_FORMAT(p.s_date_time, '%Y-%m-%d') <= ?", date("Y-m-d", strtotime($end)));
		} 
		if( (int)$user_id > 0 && $type == 1 ) {
			$select->where("p.user_id = ?", $user_id);
		} if( (int)$user_id > 0 && $type == 2 ) {
			$select->where("p.i_user_id = ?", $user_id);
		} if( (int)$user_id > 0 && $type == 3 ) {
			$select->where("p.s_user_id = ?", $user_id);
		}
		if( $type == 1 ) {
			$select->where("p.picklist = ?", 1)
				->order("p.date_time ASC");
		} else if( $type == 2 ) {
			$select->where("p.invoicing = ?", 1)
				->order("p.i_date_time ASC");
		} else if( $type == 3 ) {
			$select->where("p.shipment = ?", 1)
				->order("p.s_date_time ASC");
		}
		return $select;
	} #end getArchiveSearch() function
	
	public function buildArchiveSearchCount( $so="", $begin="", $end="", $user_id=0, $type=1 )
	{
		$select = $this->getDbTable()->select();
		$select->setIntegrityCheck(false)
			->from(array("p"=>"pick_list_archive"),
				array("count(*) AS total"));
		if( trim($so) != "" ) {
			$select->where("p.so_no LIKE '%".$so."%'", $so);
		} if( trim($begin) != "" && $type == 1 ) {
			$select->where("DATE_FORMAT(p.date_time, '%Y-%m-%d') >= ?", date("Y-m-d", strtotime($begin)));
		} if( trim($begin) != "" && $type == 2 ) {
			$select->where("DATE_FORMAT(p.i_date_time, '%Y-%m-%d') >= ?", date("Y-m-d", strtotime($begin)));
		} if( trim($begin) != "" && $type == 3 ) {
			$select->where("DATE_FORMAT(p.s_date_time, '%Y-%m-%d') >= ?", date("Y-m-d", strtotime($begin)));
		} if( trim($end) != "" && $type == 1 ) {
			$select->where("DATE_FORMAT(p.date_time, '%Y-%m-%d') <= ?", date("Y-m-d", strtotime($end)));
		} if( trim($end) != "" && $type == 2 ) {
			$select->where("DATE_FORMAT(p.i_date_time, '%Y-%m-%d') <= ?", date("Y-m-d", strtotime($end)));
		} if( trim($end) != "" && $type == 3 ) {
			$select->where("DATE_FORMAT(p.s_date_time, '%Y-%m-%d') <= ?", date("Y-m-d", strtotime($end)));
		} 
		if( (int)$user_id > 0 && $type == 1 ) {
			$select->where("p.user_id = ?", $user_id);
		} if( (int)$user_id > 0 && $type == 2 ) {
			$select->where("p.i_user_id = ?", $user_id);
		} if( (int)$user_id > 0 && $type == 3 ) {
			$select->where("p.s_user_id = ?", $user_id);
		}
		if( $type == 1 ) {
			$select->where("p.picklist = ?", 1)
				->order("p.date_time ASC");
		} else if( $type == 2 ) {
			$select->where("p.invoicing = ?", 1)
				->order("p.i_date_time ASC");
		} else if( $type == 3 ) {
			$select->where("p.shipment = ?", 1)
				->order("p.s_date_time ASC");
		}
		$result = $select->query()->fetchAll();
		return (int)$result[0]['total'];
	} #end buildArchiveSearchCount() function
	
	public function getSearch( $search, $user_id=0, $type=1 )
	{
		$select = $this->getDbTable()->select();
		$select->setIntegrityCheck(false)
			->from(array("p"=>"pick_list_archive"),
				array("p.pl_id", "p.so_no", "p.date_time", "p.user_id", "u.name", "p.i_date_time", "p.s_date_time",
					"p.i_user_id", "p.s_user_id"))
			->joinLeft(array("u"=>"$this->_db.users"), "p.user_id=u.user_id", array())
			->where("p.so_no LIKE '%".$search."%'");
		if( $type == 1 ) {
			$select->where("p.picklist = ?", 1)
				->order("p.date_time DESC");
			if( (int)$user_id > 0 ) {
				$select->where("p.user_id = ?", $user_id);
			}
		} else if( $type == 2 ) {
			$select->where("p.invoicing = ?", 1)
				->order("p.i_date_time DESC");
			if( (int)$user_id > 0 ) {
				$select->where("p.i_user_id = ?", $user_id);
			}
		} else if( $type == 3 ) {
			$select->where("p.shipment = ?", 1)
				->order("p.s_date_time DESC");
			if( (int)$user_id > 0 ) {
				$select->where("p.s_user_id = ?", $user_id);
			}
		}
		return $select;
	} #end getSearch() function
	
	public function getTodaysPicklists($user_id=0, $type=1)
	{
		$select = $this->getDbTable()->select();
		$select->setIntegrityCheck(false)
			->from(array("p"=>"pick_list_archive"),
				array("p.pl_id", "p.so_no", "p.date_time", "p.user_id", "u.name", "p.i_date_time", "p.s_date_time",
					"p.i_user_id", "p.s_user_id"))
			->joinLeft(array("u"=>"$this->_db.users"), "p.user_id=u.user_id", array());
		if( $type == 1 ) {
			$select->where("p.picklist = ?", 1)
				->order("p.date_time DESC");
			if( (int)$user_id > 0 ) {
				$select->where("p.user_id = ?", $user_id);
			}
		} else if( $type == 2 ) {
			$select->where("p.invoicing = ?", 1)
				->order("p.i_date_time DESC");
			if( (int)$user_id > 0 ) {
				$select->where("p.i_user_id = ?", $user_id);
			}
		} else if( $type == 3 ) {
			$select->where("p.shipment = ?", 1)
				->order("p.s_date_time DESC");
			if( (int)$user_id > 0 ) {
				$select->where("p.s_user_id = ?", $user_id);
			}
		}
                $select->order(array("p.pl_id DESC"));
                $select->limit(20);
		return $select->query()->fetchAll();
	} #end getTodaysPicklists() function
	
	public function rebuildWithBMData( $orders )
	{
		$final_results = array();
		$i             = 0 ;
		$mapper = new Atlas_Model_BmArhdrMapper();
		foreach( $orders as $order ) {
			try {
				$entry = $mapper->buildInvoiceBySO($order['so_no']);
			} catch( Exception $e ) {
				$entry = array();
			}
			$final_results[$i]               = $order;
			$final_results[$i]['custkey']    = $entry['custkey'];
			$final_results[$i]['custname']   = $entry['custname'];
			$final_results[$i]['oedocid']    = $entry['oedocid'];
			$final_results[$i]['custpono']   = $entry['custpono'];
			$final_results[$i]['fintranno']  = $entry['fintranno'];
			$final_results[$i]['user1']      = $entry['user1'];
			++$i;
		}
		return $final_results;
	} #end rebuildWithBMData() function
	
	public function buildBySoNumber( $so_number )
	{
		$select = $this->getDbTable()->select();
		$select->setIntegrityCheck(false)
			->from(array("p"=>"pick_list_archive"),
				array("p.pl_id", "p.so_no", "p.date_time", "p.user_id", "p.picklist", "p.invoicing", "p.shipment", 
					"p.carrier", "p.tracking", "p.i_date_time", "p.s_date_time", "p.i_user_id", "p.s_user_id",
					"u1.name AS picking_user", "u2.name AS invoice_user", "u3.name AS shipment_user"))
			->joinLeft(array("u1"=>"$this->_db.users"), "u1.user_id=p.user_id", array())
			->joinLeft(array("u2"=>"$this->_db.users"), "u2.user_id=p.i_user_id", array())
			->joinLeft(array("u3"=>"$this->_db.users"), "u3.user_id=p.s_user_id", array())
			->where("p.so_no = ?", $so_number);
			   
		$results = $select->query()->fetchAll();
		if( !is_array($results) || count($results) <= 0 ) {
			throw new Exception("SO Number not found in the system");
		}
		return $results[0];
	} #end buildBySoNumber() function
	public function dailyTotals( $date ){
            $select1 = $this->getDbTable()->select();
            $select1->setIntegrityCheck(true)
                    ->from(array("p"=>"pick_list_archive"),
                            array('(select 1 from dual)',"COUNT(*) AS totals"))
                    ->where("DATE_FORMAT(p.date_time, '%Y-%m-%d') >= ?", date("Y-m-d", strtotime($date)))
                    ->where("DATE_FORMAT(p.date_time, '%Y-%m-%d') <= ?", date("Y-m-d", strtotime($date)));
            
            $select2 = $this->getDbTable()->select();
            $select2->setIntegrityCheck(true)
                    ->from(array("p"=>"pick_list_archive"), 
                            array('(select 2 from dual)',"COUNT(*) AS total2"))
                    ->where("DATE_FORMAT(p.i_date_time, '%Y-%m-%d') >= ?", date("Y-m-d", strtotime($date)))
                    ->where("DATE_FORMAT(p.i_date_time, '%Y-%m-%d') <= ?", date("Y-m-d", strtotime($date)))
                    ->where("p.invoicing = ?", 1);
            
            $select3 = $this->getDbTable()->select();
            $select3->setIntegrityCheck(true)
                    ->from(array("p"=>"pick_list_archive"),
                            array('(select 3 from dual)',"COUNT(*) AS total3"))
                    ->where("DATE_FORMAT(p.s_date_time, '%Y-%m-%d') >= ?", date("Y-m-d", strtotime($date)))
                    ->where("DATE_FORMAT(p.s_date_time, '%Y-%m-%d') <= ?", date("Y-m-d", strtotime($date)))
                    ->where("p.shipment = ?", 1);
            
                $select = $this->getDbTable()->select()
                            ->union(array($select1, $select2,$select3));
                $results = $select->query()->fetchAll();        

                return $results;
        }
                
	public function buildReport( $start_date, $end_date, $user_id=0, $type=1 )
	{
		$start_ts      = strtotime($start_date);
		$end_ts        = strtotime($end_date);
		$current_date  = date("Ymd", $start_ts);
		$finish_date   = date("Ymd", $end_ts);
		$final_results = array();
		$i             = 0;
		while( $current_date <= $finish_date ) {
			$ts = mktime(0,0,0,date("m",$start_ts),(date("d",$start_ts)+$i),date("Y",$start_ts));
			$check_date   = date("Y-m-d", $ts);
			$select = $this->getDbTable()->select();
			$select->setIntegrityCheck(false)
				->from(array("p"=>"pick_list_archive"),
					array("COUNT(*) AS total"));
			if( $type == 1 ) {
				$select->where("p.picklist = ?", 1)
					->where("DATE_FORMAT(p.date_time, '%Y-%m-%d') = ?", $check_date)
					->group("DATE_FORMAT(p.date_time, '%Y-%m-%d')")
					->order("p.date_time ASC");
				if( (int)$user_id > 0 ) {
					$select->where("p.user_id = ?", $user_id);
				}
			} else if( $type == 2 ) {
				$select->where("p.invoicing = ?", 1)
					->where("DATE_FORMAT(p.i_date_time, '%Y-%m-%d') = ?", $check_date)
					->group("DATE_FORMAT(p.i_date_time, '%Y-%m-%d')")
					->order("p.i_date_time ASC");
				if( (int)$user_id > 0 ) {
					$select->where("p.i_user_id = ?", $user_id);
				}
			} else if( $type == 3 ) {
				$select->where("p.shipment = ?", 1)
					->where("DATE_FORMAT(p.s_date_time, '%Y-%m-%d') = ?", $check_date)
					->group("DATE_FORMAT(p.s_date_time, '%Y-%m-%d')")
					->order("p.s_date_time ASC");
				if( (int)$user_id > 0 ) {
					$select->where("p.s_user_id = ?", $user_id);
				}
			}
			
			$results = $select->query()->fetchAll();
			if( is_array($results) && count($results) > 0 ) {
				$final_results['dates'][]  = date("m/d/Y", $ts);
				$final_results['points'][] = $results[0]['total'];
			} else {
				$final_results['dates'][]  = date("m/d/Y", $ts);
				$final_results['points'][] = 0;
			}
			
			++$i;
			$ts = mktime(0,0,0,date("m",$start_ts),(date("d",$start_ts)+$i),date("Y",$start_ts));
			$current_date = date("Ymd", $ts);
		}
		
		return $final_results;
	} #end buildReport() function
	
	// check if so is in the system already
	public function checkSO( $so_number, $type=1 )
	{
		$select = $this->getDbTable()->select();
		$select->setIntegrityCheck(false)
			->from(array("p"=>"pick_list_archive"),
				array("COUNT(*) AS so_exists"))
			->where("p.so_no = ?", $so_number);
		if( $type == 1 ) {
			$select->where("p.picklist = ?", 1);
		} else if( $type == 2 ) {
			$select->where("p.invoicing = ?", 1);
		} else if( $type == 3 ) {
			$select->where("p.shipment = ?", 1);
		}
			   
		$result = $select->query()->fetchAll();
		if( !is_array($result) || (int)$result[0]['so_exists'] <= 0 ) {
			return false;
		} else {
			return true;
		}
	} #end checkSO() function
	
	// process data from the bin form
	public function savePicklist( $so_number, $user_id )
	{
		if( !$this->checkSO( $so_number ) ) {
			$picklist = new Atlas_Model_Picklist();
			$picklist->setSo_no(strtoupper($so_number))
				->setDate_time(date("Y-m-d H:i:s", time()))
				->setUser_id($user_id)
				->setPicklist(1)
				->setInvoicing(0)
				->setShipment(0);
			$this->save($picklist);
		} else {
			throw new Exception("The SO number (".$so_number.") has already been picked.");
		}
	} #end processForm() function
	
	public function setInvoicing( $so_number, $user_id )
	{
		if( !$this->checkSO($so_number, 1) ) {
			throw new Exception("The SO Number (".$so_number.") hasn't been entered into the picklist archive yet.");
		} else if( $this->checkSO($so_number, 2) ) {
			throw new Exception("The SO Number (".$so_number.") has already been invoiced.");
		} else {
			$picklist = $this->buildBySoNumber($so_number);
			$picklist['invoicing']   = 1;
			$picklist['i_date_time'] = date("Y-m-d H:i:s", time());
			$picklist['i_user_id']   = $user_id;
			$this->save(new Atlas_Model_Picklist($picklist));
		}
	} #end setInvoicing() function
	
	public function setShipment( $so_number, $user_id )
	{
		if( !$this->checkSO($so_number, 1) ) {
			throw new Exception("The SO Number (".$so_number.") hasn't been entered into the picklist archive yet.");
		} else if( !$this->checkSO($so_number, 2) ) {
			throw new Exception("The SO Number (".$so_number.") hasn't been entered into the invoicing archive yet.");
		} else if( $this->checkSO($so_number, 3) ) {
			throw new Exception("The SO Number (".$so_number.") has already been shipped.");
		} else {
            /*
			$mapper        = new Atlas_Model_SSPackMapper();
			$tracking_data = $mapper->buildTrackingData($so_number);*/
			$picklist      = $this->buildBySoNumber($so_number);
			//$tracking      = $tracking_data[0]['mastertrackingnumber'];
			//$tokens        = explode(",",$tracking);
			$picklist['shipment']    = 1;
			$picklist['s_date_time'] = date("Y-m-d H:i:s", time());
			$picklist['s_user_id']   = $user_id;
			/*
            if( count($tokens) > 1 ) {
				$picklist['tracking'] = trim($tokens[1]);
			} else {
				$picklist['tracking'] = $tracking_data[0]['mastertrackingnumber'];
			}
			if( $tracking_data[0]['billingaccountnumber'] == "111776156" ) {
				$picklist['carrier'] = "FEDEX";
			} else if( $tracking_data[0]['billingaccountnumber'] == "2A7667" ) {
				$picklist['carrier'] = "UPS";
			} else {
				$picklist['carrier'] = "UNKNOWN";
			}*/

            $picklist['tracking'] = "N/A";
            $picklist['carrier']  = "N/A";
			$this->save(new Atlas_Model_Picklist($picklist));
		}
	} #end setInvoicing() function
        
        
        #get last order numbers
        public function getSoNumbers(){
            
            $select = $this->getDbTable()->select();
            $select->setIntegrityCheck(false)
                    ->from(array("p"=>"pick_list_archive"),
                            array("p.so_no"))
                    ->order("date_time DESC")
                    ->limit(2000);
            $result = array_column($select->query()->fetchAll(), 'so_no');            
            return $result;
        }
	
}

?>
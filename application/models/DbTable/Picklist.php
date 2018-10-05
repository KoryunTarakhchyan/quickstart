<?php

class Atlas_Model_DbTable_Picklist extends Zend_Db_Table_Abstract
{

    protected $_name = 'pick_list_archive';
	protected $_id   = 'pl_id';

	protected function _setupDatabaseAdapter()
	{
		$this->_db = Zend_Db::factory(Zend_Registry::get('mysql')->db);
		parent::_setupDatabaseAdapter();
	} #end _setupDatabaseAdapter function
}

?>
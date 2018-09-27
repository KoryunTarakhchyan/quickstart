<?php

class Atlas_Model_DbTable_Product extends Zend_Db_Table_Abstract
{

    protected $_name = 'product';
    protected $_id   = 'id';

	protected function _setupDatabaseAdapter()
	{
        $this->_db = Zend_Db::factory(Zend_Registry::get('mysqlcop')->db);
        parent::_setupDatabaseAdapter();
	} #end _setupDatabase
}


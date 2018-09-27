<?php

class Application_Model_DbTable_Guestbook extends Zend_Db_Table_Abstract
{

    protected $_name = 'guestbook';

	protected $_id   = 'id';

	protected function _setupDatabaseAdapter()
	{
        $this->_db = Zend_Db::factory(Zend_Registry::get('mysql')->db);
        parent::_setupDatabaseAdapter();
	} #end _setupDatabaseAdapter function

}


<?php
class Atlas_Model_DbTable_Ticket extends Zend_Db_Table_Abstract
{

    protected $_name = 'ticket';
    protected $_id   = 'id';

    protected function _setupDatabaseAdapter()
    {
            $this->_db = Zend_Db::factory(Zend_Registry::get('mysql')->db);
            parent::_setupDatabaseAdapter();
    } #end _setupDatabaseAdapter function
}


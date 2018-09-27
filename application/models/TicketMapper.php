<?php

class Atlas_Model_TicketMapper
{
    protected $_dbTable;

    public function setDbTable($dbTable) {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable() {
        if (null === $this->_dbTable) {
            $this->setDbTable('Atlas_Model_DbTable_Ticket');
        }
        return $this->_dbTable;
    }

    // save the attributes of a given db object
    public function save(Atlas_Model_Ticket $ticket) {
        // push the data into an array
        $data = $ticket->toArray();

        // if the row in the db doesnt exist create the row
        // otherwise update the existing row
        if (NULL === ($id = $ticket->getId()) || $id == 0) {
            unset($data['id']);
            $id = $this->getDbTable()->insert($data);
            return $id;
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
            return $id;
        }
    }#end save() function

    public function find($id) {
        $ticket = new Atlas_Model_Ticket();
        // attempt to locate the row in the database
        // if it doesn't exist return NULL
        $result = $this->getDbTable()->find((int) $id);
        if (0 == count($result)) {
            require_once 'Zend/Exception.php';
            throw new Zend_Exception("Given table row doesn't exist");
        }
        // get the data and push it to the object
        $row = $result->current();
        $ticket->setOptions($row->toArray());
        return $ticket;
    }

    // find all entries from the database for the given table
    public function fetchAll() {
        // gather all of the entries in the database
        // and push their values into an array
        $resultSet = $this->selectAll()->query()->fetchAll();
        $entries = array();
        foreach ($resultSet as $row) {
            $entry = new Atlas_Model_Ticket();
            $entry->setOptions($row);

            $entries[] = $entry;
        }

        // return the results
        return $entries;
    }#end fetchAll() function

    public function buildAllTickets() {
        $select = $this->getDbTable()->select();
        $select->setIntegrityCheck(false)
                ->from(array("ak" => "ticket"), array("ak.*"));
        return $select->query()->fetchAll();
    }#end buildAllTickets() function
}


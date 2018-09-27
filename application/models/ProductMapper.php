<?php

class Atlas_Model_ProductMapper
{
	 protected $_dbTable;
 
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
 
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Atlas_Model_DbTable_Product');
        }
        return $this->_dbTable;
    }
 
    public function save(Atlas_Model_Product $product)
    {
      
        $data = array(
            'name'   => $porduct->getName(),
            'price' => $porduct->getPrice(),
            'quantity' => $porduct->getQuantity(),
            'created_at' => $porduct->getCreated_at(),
            'updated_at' => $porduct->getUpdated_at()
        );
 
        
        if (null === ($id = $porduct->getId()) || 0 === ($id = $porduct->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function find($id) {

        $porduct = new Atlas_Model_Porduct(); 

        // attempt to locate the row in the database

        // if it doesn't exist return NULL

        $result = $this->getDbTable()->find((int) $id);

        if (0 == count($result)) {

            require_once 'Zend/Exception.php';

            throw new Zend_Exception("Given table row doesn't exist");

        } 

        // get the data and push it to the object

        $row = $result->current();

        $porduct->setOptions($row->toArray());

        return $porduct;

    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();

        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Atlas_Model_Product();
            $entry->setId($row->id)
                  ->setName($row->name)
                  ->setPrice($row->price)
                  ->setQuantity($row->quantity)
                  ->setUpdated_at($row->updated_at)
                  ->setCreated_at($row->created_at);
            $entries[] = $entry;
        }
        return $entries;
    }
}


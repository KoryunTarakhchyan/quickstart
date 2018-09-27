<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');

        $m3db = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini', 'm3');
        Zend_Registry::set('m3', $m3db);
        
        $mysqldb = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini','mysqldb');
        Zend_Registry::set('mysql', $mysqldb);
        
        $cop = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini','cop');
        Zend_Registry::set('mysqlcop', $cop);
        
        Zend_Registry::set("root_path",   "C:/xampp/htdocs/quickstart");   
        
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Utility_');        

    }
}


<?php

class PortalController extends Zend_Controller_Action
{

    public function init() {
        $this->view->messages = Utility_FlashMessenger::popMessage();
    }

    public function indexAction()
    {
    	
        $tickets = new Atlas_Model_TicketMapper();
        $this->view->entries = $tickets->fetchAll();
    }

    public function proceedAction()
    {
        $request = $this->getRequest();        
        if ($request->isPost()) {
            $proceed = new Atlas_Model_inform3Mapper();
            $results = $proceed->saveProceed($request->getPost());            
           return $this->_helper->json(1);
        }
        return $this->_helper->json(0);
    }
    
    public function reportAction () 
    {
        $request = $this->getRequest();
        $mapper = new Atlas_Model_inform3Mapper();
        if ($request->isPost()) {
            $form_data          =   $request->getPost();
            $results            =   $mapper->buildMonthlySales($form_data);    
            $this->view->records        =   $results;   
            $this->view->entries        =   $form_data;
        }  
    }
    
    public function porAction () 
    {
        $request = $this->getRequest();        
        $mapper = new Atlas_Model_inform3Mapper();
        if ($request->isPost()) {
            $form_data    =   $request->getPost();
            $results = $mapper->por($form_data); 
            if (isset($form_data['export'])) {
                $mapper = new Atlas_Model_PDFMapper('utf-8', 'A4-L', 'fullpage');
                $mapper->addCSSFile(Zend_Registry::get("root_path") . "/public/css/global.css");
                $mapper->addCSSFile(Zend_Registry::get("root_path") . "/public/css/infordashboard.css");
                $mapper->addContent(
                        $this->view->partial('/partials/portal/printorderpdf.phtml', array(
                            "records" => $results,
                            "entries" => $form_data
                        ))
                );
                $mapper->outputPDF();
                die();
            } else {
                $this->view->records = $results;
                $this->view->entries = $form_data;                
            }  
        }        
        
    }  
    
    public function printpickAction() 
    {
        $this->_helper->layout('homelayout')->disableLayout();
        $request = $this->getRequest();        
        $mapper = new Atlas_Model_PDFMapper('utf-8','A4', "fullpage"); 
        $inform3Mapper = New Atlas_Model_inform3Mapper();
        $orderNumbers = array();
        $picklist = array();
        $barcodes = array();
        
        if ($request->isPost()){
            $data   =   $request->getPost();
        } else {
            $data['orderNumbers'][] = $request->getParam('id',null);
        }
        $printType = $request->getParam('print',null);  
        
        // collecting all orderNumbers in one array
        if (count($data['onPicklist'])>0 && count($data['onBarcode'])>0) { 
            $allNumbers = array_unique(array_merge($data['onPicklist'],$data['onBarcode']));
        } else if (count($data['onPicklist'])>0) {
            $allNumbers  = $data['onPicklist'];        
        } else if (count($data['onBarcode'])>0) {
            $allNumbers  = $data['onBarcode'];
        }
       
        //generating barcodes for all orderNumbers
        foreach ($allNumbers as $orderNumber) {       
            $options = array('barHeight' => 70,  'barThinWidth' => 3, 'text' => $orderNumber, 'drawText' => FALSE, 'imageType' => 'jpeg');
            $barcode = new Zend_Barcode_Object_Code128();
            $barcode->setOptions($options);
            $barcodeOBj = Zend_Barcode::factory($barcode);
            $imageResource = $barcodeOBj->draw();
            imagejpeg($imageResource , 'pdf\barcode_'.$orderNumber.'.png');  
            
            $header [$orderNumber] = $inform3Mapper->buildPicklistheader($orderNumber);
            $list   [$orderNumber] = $inform3Mapper->buildPicklist($orderNumber);
        } 
        //generating the picklist
        if (count($data['onPicklist'])>0) {
            foreach ($data['onPicklist'] as $orderNumber){
                //update who had printed out the picklist 
                $inform3Mapper->updateProceedUser($orderNumber,$printType);                       

                $picklist [$orderNumber]['header']  = $header [$orderNumber];
                $picklist [$orderNumber]['lines']   = $list   [$orderNumber];
                if (!in_array($orderNumber, $orderNumbers )) {
                    $orderNumbers[] = $orderNumber;
                }            
            }
        }
        
        //generating separate barcode
        if (count($data['onBarcode'])>0) {
            foreach ($data['onBarcode'] as $orderNumber){                
                $barcodes [] =$orderNumber;
                $barcodes [$orderNumber]['header']  = $header [$orderNumber];
                $barcodes [$orderNumber]['lines']   = $list   [$orderNumber];
                if (!in_array($orderNumber, $orderNumbers )) {
                    $orderNumbers[] = $orderNumber;
                } 
            }
        }        
       
        
        $mapper->addCSSFile(Zend_Registry::get("root_path") . "/public/css/global.css");
        $mapper->addContent(
                $this->view->partial('/portal/picklist.phtml', array(
                    "orderNumbers" => $orderNumbers, 
                    "picklist" => $picklist ,
                    "barcodes" => $barcodes
                )));             
        $time = time();
        if ($request->isPost()){
            $mapper->outputPDFtoFile("pdf/orderNumbers_".$time.".pdf");
        } else {
            $mapper->outputPDF();
        }   
        foreach ($orderNumbers as $on){
            unlink('pdf/barcode_'.$on.'.png');
        }
        echo $time;

    }
    
    public function pickordersAction()
    {        
        $request = $this->getRequest();
        $mapper = new Atlas_Model_inform3Mapper(); 
        $picklistMapper = New Atlas_Model_PicklistMapper();
        $form_data = '';
        if ($request->isPost()) {
            $form_data  =   Utility_Filter_DBSafe::clean($request->getPost());
        }        
        $so_no = $picklistMapper->getSoNumbers();
        $page = $request->getParam('page',0);
        $results    =   $mapper->buildReleasedOrders($form_data,0,$page);
        $this->view->sono = $so_no;
        $this->view->entries = $results;
    }
    
    public function releasedordersAction()
    {
        $request = $this->getRequest();   
        $mapper = new Atlas_Model_inform3Mapper();
        $picklistMapper = New Atlas_Model_PicklistMapper();
        $form_data = null;
        if ($request->isPost()) {
            $form_data    =   Utility_Filter_DBSafe::clean($request->getPost());
        }   
        $so_no = $picklistMapper->getSoNumbers();
        $totalcount = $mapper->buildReleasedOrdersCount();
        $page = $request->getParam('page',1);
        $results = $mapper->buildReleasedOrders($form_data,1,$page);
        $pagination = $mapper->createPagination($totalcount,$page);
        
        $this->view->sono = $so_no;
        $this->view->entries = $results;
        $this->view->pagination = $pagination;
    }
    
    public function ticketsAction()
    {
        $this->view->title = "Tickets";
        $tickets = new Atlas_Model_TicketMapper();
        $this->view->reocrds = $tickets->buildAllTickets();
    }

    public function ticketAction()
    {
        $this->view->title = "Modify/Create Ticket";
        $request    =   $this->getRequest();
        $id         =   $request->getParam('id',null);
        $form       =   new Atlas_Form_Ticket();        
        $ticketMapper  = new Atlas_Model_TicketMapper();
        if ($id != null) {
            $ticket = $ticketMapper->find($id); 
            $form->populate($ticket->toArray());
        }
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $form_data      =   Utility_Filter_DBSafe::clean($request->getPost());
                $form_data['requestedDate'] =   date('Y-m-d',strtotime($form_data['requestedDate']));
                $form_data['updatedDate']   =   date('Y-m-d H:i:s');
                $form_data['userId']   =   1;
                if ($id == null) {                  
                    $form_data['createdDate']   =   date('Y-m-d H:i:s');;
                }
                $ticket     =   new Atlas_Model_Ticket($form_data);
                $mapper     =   new Atlas_Model_TicketMapper();
                $mapper->save($ticket);
                Utility_FlashMessenger::addMessage('<div class="success">Ticket has been updated</div>');
                return $this->_redirect("/portal/tickets");
            }else{
                $message = Utility_Error::buildErrors($form->getMessages());
                $this->view->messages = $message;
            }
        }
        $this->view->form = $form;
    }

    public function inventoriesAction()
    {
        $this->view->title = "Inventory";
        $items = new Atlas_Model_InventoryMapper();
        $this->view->entries = $items->buildAllInventoryItems();
    }

    public function inventoryAction()
    {
        $this->view->title = "Modify/Create Inventory Item";
        $request       = $this->getRequest();        
        $id            = $request->getParam('id',null);
        $form          = new Atlas_Form_Inventory();
        $ticketMapper  = new Atlas_Model_InventoryMapper;
        if ($id != null) {
            $item = $ticketMapper->find($id);         
            $form->populate($item->toArray());
        }
        if ($this->getRequest()->isPost()) {              
            if ($form->isValid($request->getPost())) {
                $form_data               = Utility_Filter_DBSafe::clean($request->getPost());
                $form_data               = $request->getPost();
                $form_data['support_date'] = date('Y-m-d',strtotime($form_data['support_date']));
                $inventory  = new Atlas_Model_Inventory($form_data);
                $mapper  = new Atlas_Model_InventoryMapper();
                $mapper->save($inventory);
                Utility_FlashMessenger::addMessage('<div class="success">Ticket has been updated</div>');
                return $this->_redirect("/portal/inventories");
            }else{
                $message = Utility_Error::buildErrors($form->getMessages());
                $this->view->messages = $message;
            }
        } 
        $this->view->form = $form;
    }
    
    public function pbonAction () 
    {
        $this->view->title = "Print By Order Number";        
        $mapper         = new Atlas_Model_PDFMapper('utf-8','A4', "fullpage"); 
        $inform3Mapper  = New Atlas_Model_inform3Mapper();
        $request        = $this->getRequest();
        $form_data      = null;
        $barcodes       = array();
         if ($request->isPost()) {
            $form_data      =   Utility_Filter_DBSafe::clean($request->getPost()); 
            $orderNumber    =   $form_data['order_number'];
            $orderNumbers = array($orderNumber);

            $options = array('barHeight' => 70,  'barThinWidth' => 3, 'text' => $orderNumber, 'drawText' => FALSE, 'imageType' => 'jpeg');
            $barcode = new Zend_Barcode_Object_Code128();
            $barcode->setOptions($options);
            $barcodeOBj = Zend_Barcode::factory($barcode);
            $imageResource = $barcodeOBj->draw();
            imagejpeg($imageResource , 'pdf\barcode_'.$orderNumber.'.png');

            $picklist [$orderNumber]['header']  = $inform3Mapper->buildPicklistheader($orderNumber);
            $picklist [$orderNumber]['lines']       = $inform3Mapper->buildPicklist($orderNumber, 1);

            if ($form_data['barcode'] == 'on') {
                $barcodes [] =$orderNumber;
            }

            $mapper->addCSSFile(Zend_Registry::get("root_path") . "/public/css/global.css");
            $mapper->addContent(
                    $this->view->partial('/portal/picklist.phtml', array(
                        "orderNumbers" => $orderNumbers, 
                        "picklist" => $picklist ,
                        "barcodes" => $barcodes
                    )));     

            $mapper->outputPDF();
            die();
        }
    } 
        
    public function agingAction()
    {
        $this->view->title = "Aging Report";
        $request = $this->getRequest();
        $form_data = null;
        $mapper = new Atlas_Model_inform3Mapper();
        if ($request->isPost()) {
            $form_data  =   Utility_Filter_DBSafe::clean($request->getPost());
             $this->view->entries        =   $form_data;
            $this->view->records = $mapper->agingReport($form_data);
            
        }  
                     
       
    }
    
    public function ssreportAction() {
        $this->view->title = "Item Lot Data Report";
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form_data              =   $request->getPost();
            $this->view->entries    =   $form_data; 
            $inform3mapper          =   new Atlas_Model_inform3Mapper();
            $header                 =   new Atlas_Model_ShipmentsheaderMapper(); 
            $erp                    =   $inform3mapper->lotShipmentReport($form_data);
            //AND OBCUNO = 'AMAZ100'
            $batchnumbers = array();
            foreach($erp as $items) {
                $batchnumbers[$items ['Order_No']] = $items ['PO_No'];
            }
            $this->view->batchnumbers   =   $batchnumbers;
            $results    =   '';
            if(is_array($batchnumbers) && count($batchnumbers)>0)
                $results    =   $header->buildShipments(trim($form_data['itemkey']),$batchnumbers); 
            $this->view->records        =   $results; 
        }  
    }

    
}


<?php

class TicketController extends Zend_Controller_Action
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
                        $this->view->partial('/partials/ticket/printorderpdf.phtml', array(
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
    
    public function printorderAction() 
    {
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
        if (count($data['onPicklist'])>0) {
            foreach ($data['onPicklist'] as $orderNumber){
                if ($printType == 1) {
                    $inform3Mapper->updateProceedUser($orderNumber);
                }
                $options = array('barHeight' => 70,  'barThinWidth' => 3, 'text' => $orderNumber, 'drawText' => FALSE, 'imageType' => 'jpeg');
                $barcode = new Zend_Barcode_Object_Code128();
                $barcode->setOptions($options);
                $barcodeOBj = Zend_Barcode::factory($barcode);
                $imageResource = $barcodeOBj->draw();
                imagejpeg($imageResource , 'pdf\barcode_'.$orderNumber.'.png');  

                $picklist [$orderNumber]['header']  = $inform3Mapper->buildPicklistheader($orderNumber);
                $picklist [$orderNumber]['lines']       = $inform3Mapper->buildPicklist($orderNumber);
                if (!in_array($orderNumber, $orderNumbers )) {
                    $orderNumbers[] = $orderNumber;
                }            
            }
        }
        if (count($data['onBarcode'])>0) {
            foreach ($data['onBarcode'] as $orderNumber){
                $barcodes [] =$orderNumber;
                if (!in_array($orderNumber, $orderNumbers )) {
                    $orderNumbers[] = $orderNumber;
                } 
            }
        }        
        
        $mapper->addCSSFile(Zend_Registry::get("root_path") . "/public/css/global.css");
        $mapper->addContent(
                $this->view->partial('\ticket\picklist.phtml', array(
                    "orderNumbers" => $orderNumbers, 
                    "picklist" => $picklist ,
                    "barcodes" => $barcodes
                )));             
       
        if ($request->isPost()){
            $mapper->outputPDFtoFile("pdf\\orderNumbers.pdf");
        } else {
            $mapper->outputPDF();
            die();
        }   
    }
    
    public function pickordersAction()
    {        
        $request = $this->getRequest();
        $mapper = new Atlas_Model_inform3Mapper(); 
        $form_data = '';
        if ($request->isPost()) {
            $form_data  =   Utility_Filter_DBSafe::clean($request->getPost());
        }        
        $page = $request->getParam('page',0);
        $results    =   $mapper->buildReleasedOrders($form_data,0,$page);
        $this->view->entries = $results;
    }
    
    public function releasedordersAction()
    {
        $request = $this->getRequest();   
        $mapper = new Atlas_Model_inform3Mapper();
        $form_data = null;
        if ($request->isPost()) {
            $form_data    =   Utility_Filter_DBSafe::clean($request->getPost());
        }   
        $totalcount = $mapper->buildReleasedOrdersCount();
        $page = $request->getParam('page',1);
        $results = $mapper->buildReleasedOrders($form_data,1,$page);
        $pagination = $mapper->createPagination($totalcount,$page);
        
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
                return $this->_redirect("/ticket/tickets");
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
                return $this->_redirect("/ticket/inventories");
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
                    $this->view->partial('\ticket\picklist.phtml', array(
                        "orderNumbers" => $orderNumbers, 
                        "picklist" => $picklist ,
                        "barcodes" => $barcodes
                    )));     

            $mapper->outputPDF();
            die();
        }
    } 
        
    public function shipmentAction()
    {
        $this->view->title = "Shipment";
//        ini_set('max_execution_time', 300);
//        $mapper     =   new Atlas_Model_ShipstationMapper(); 
//        $firstresults    =   $mapper->PullData('shipments',
//                                array(  'startDate' =>  '2018-09-26',
//                                        'endDate'   =>  '2018-09-26',
//                                        'includeShipmentItems'  =>"true",
//                                        'page'=>1,
//                                        'sortBy'=>"CreateDate",
//                                        'sortDir'=>"ASC",
//                                        'pageSize'=>500
//                                    ));
//        $pages =  $firstresults['pages'];
//       
//        $data = array();
//        $i = 1;    
//        while ($i <= $pages) {
//            $mapper     =   new Atlas_Model_ShipstationMapper(); 
//            $results    =   $mapper->PullData('shipments',
//                                array(  'startDate' =>  '2018-09-26',
//                                        'endDate'   =>  '2018-09-26',
//                                        'includeShipmentItems'  =>"true",
//                                        'page'=>$i,
//                                        'sortBy'=>"CreateDate",
//                                        'sortDir'=>"ASC",
//                                        'pageSize'=>500
//                                    ));
//
//            foreach($results['shipments'] as $result){
//                if ($result['batchNumber'] != NULL && $result['batchNumber'] != "") {                    
//                    $headermapper = new Atlas_Model_ShipmentsheaderMapper();
//                    $itemmapper = new Atlas_Model_ShipmentitemsMapper();
//                    
//
//                    $data['header'][$i] = $result;
//                    $data['header'][$i]['createDate'] = date('Ymd', strtotime($result['createDate']));
//                    $data['header'][$i]['shipDate'] = date('Ymd', strtotime($result['shipDate']));
//                    $data['header'][$i]['voidDate'] = date('Ymd', strtotime($result['voidDate']));
//                    $data['header'][$i]['shipTo_name'] = $result['shipTo']['name'];
//                    $data['header'][$i]['shipTo_company'] = $result['shipTo']['company'];
//                    $data['header'][$i]['shipTo_street2'] = $result['shipTo']['street2'];
//                    $data['header'][$i]['shipTo_street3'] = $result['shipTo']['street3'];
//                    $data['header'][$i]['shipTo_city'] = $result['shipTo']['city'];
//                    $data['header'][$i]['shipTo_state'] = $result['shipTo']['state'];
//                    $data['header'][$i]['shipTo_postalCode'] = $result['shipTo']['postalCode'];
//                    $data['header'][$i]['shipTo_postalCode'] = $result['shipTo']['country'];
//                    $data['header'][$i]['shipTo_phone'] = $result['shipTo']['phone'];
//                    $data['header'][$i]['shipTo_residential'] = $result['shipTo']['residential'];
//                    $data['header'][$i]['shipTo_addressVerified'] = $result['shipTo']['addressVerified'];
//                    $data['header'][$i]['weight_value'] = $result['weight']['value'];
//                    $data['header'][$i]['weight_units'] = $result['weight']['units'];
//                    $data['header'][$i]['weightUnits'] = $result['weight']['WeightUnits'];
//                    $data['header'][$i]['storeId'] = $result['advancedOptions']['storeId'];

//                    $header = New Atlas_Model_Shipmentsheader($result);
//                    $headermapper->save($header);
                    
//                    foreach ($result['shipmentItems'] as $shipmentItems) { 
//                        $data['items'][$i] =$shipmentItems;
//                        $data['items'][$i]['orderId'] =$result['orderId'];
//                        $data['items'][$i]['itemoptions'] =$shipmentItems['options'];
//
//                        $pieces = explode("-", $shipmentItems['sku']);
//                        $data['items'][$i]['sku'] = trim($pieces[0]);
//                        if ((int) $pieces[1]>0){
//                            $data['items'][$i]['skuqty'] = $pieces[1];
//                        } else {
//                            $data['items'][$i]['skuqty'] = 1;
//                        }
////                        $item = new Atlas_Model_Shipmentitems($shipmentItems);
////                        $itemmapper->save($item);
//                    } 
//                }        
//            }
//            $i++;
//        }                        
//        print_r('<pre>');
//        print_r($data);
//        die();
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form_data          =   $request->getPost();
            $this->view->entries        =   $form_data;
            
            $header = new Atlas_Model_ShipmentsheaderMapper();
            $items = new Atlas_Model_ShipmentitemsMapper();
            $this->view->records = $header->buildShipments($form_data['itemkey']);
            
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
    
}


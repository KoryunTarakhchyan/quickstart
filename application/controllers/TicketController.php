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
        $this->view->title = "Import Ship Station Data";
        $request = $this->getRequest();
//        if ($request->isPost()) {
//            ini_set('max_execution_time', 60 * 60 * 4);
//            $form_data      =   $request->getPost();
//            $start_date     =   date('Y-m-d', strtotime($form_data['start_date']));
//            $end_date       =   date('Y-m-d', strtotime($form_data['end_date']));
//            $page           =   1;
//            $results        =   array();
//            $mapper = new Atlas_Model_ShipstationMapper();
//            $values = array(    'shipDateStart'     => $start_date,
//                                'shipDateEnd'       => $end_date,
//                                'includeShipmentItems' => "true",
//                                'page'      => $page,
//                                'sortBy'    => "CreateDate",
//                                'sortDir'   => "ASC",
//                                'pageSize'  => 500   );
//            $results[]      =   $mapper->PullData('shipments', $values);
//            $total_pages    =   $results[0]['pages'];
//            
//            if ( (int) $total_pages > 0) {
//                for($i=2; $i<=$total_pages; $i++){
//                    $values['page'] =   $i;
//                    $mapper         =   new Atlas_Model_ShipstationMapper();
//                    $results[]      =   $mapper->PullData('shipments', $values);
//                }
//                
//            }
//            
//            
//            $data = array();
//            $y = 1;
//            foreach($results as $result){
//                foreach ($result['shipments'] as $data) {                   
//                    if ($data['batchNumber'] != NULL && $data['batchNumber'] != "") { 
//                        foreach ($data['shipmentItems'] as $shipmentItems) { 
//                   
//                            $shipmentItems['orderId']       = $data['orderId'];
//                            $shipmentItems['itemoptions']   = $shipmentItems['options'];
//                            unset($shipmentItems['options']);
//                            $pieces = explode("-", $shipmentItems['sku']);
//                            $shipmentItems['sku'] = trim($pieces[0]);
//                            if ( (int) $pieces[1]>0){
//                                $shipmentItems['skuqty'] = $pieces[1];
//                            } else {
//                                $shipmentItems['skuqty'] = 1;
//                            }
//                            $itemmapper = new Atlas_Model_ShipmentitemsMapper();
//                            $item = new Atlas_Model_Shipmentitems($shipmentItems);
//                            $itemmapper->save($item);
//    
//                        }   
//                        
//                        
//                        $data['createDate']               = date('Ymd', strtotime($data['createDate']));
//                        $data['shipDate']                 = date('Ymd', strtotime($data['shipDate']));
//                        $data['voidDate']                 = date('Ymd', strtotime($data['voidDate']));
//                        $data['shipTo_name']              = $data['shipTo']['name'];
//                        $data['shipTo_company']           = $data['shipTo']['company'];
//                        $data['shipTo_street1']           = $data['shipTo']['street1'];
//                        $data['shipTo_street2']           = $data['shipTo']['street2'];
//                        $data['shipTo_street3']           = $data['shipTo']['street3'];
//                        $data['shipTo_city']              = $data['shipTo']['city'];
//                        $data['shipTo_state']             = $data['shipTo']['state'];
//                        $data['shipTo_postalCode']        = $data['shipTo']['postalCode'];
//                        $data['shipTo_country']           = $data['shipTo']['country'];
//                        $data['shipTo_phone']             = $data['shipTo']['phone'];
//                        $data['shipTo_residential']       = $data['shipTo']['residential'];
//                        $data['shipTo_addressVerified']   = $data['shipTo']['addressVerified'];
//                        $data['weight_value']             = $data['weight']['value'];
//                        $data['weight_units']             = $data['weight']['units'];
//                        $data['weightUnits']              = $data['weight']['WeightUnits'];
//                        $data['storeId']                  = $data['advancedOptions']['storeId'];
//
//                        $headermapper = new Atlas_Model_ShipmentsheaderMapper();
//                        $header = New Atlas_Model_Shipmentsheader($data);
//                        $headermapper->save($header);
//                        
//                    }
//                }
//
//            }
//        }
        
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


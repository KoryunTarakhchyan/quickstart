<?php

class Atlas_Model_inform3Mapper{
    protected $_settings;
    protected $_dbLink;
    protected $_dbTable;

    public function __construct()
    { 
        $settings = Zend_Registry::get("m3");
        $params   = $settings->db->params;
        $serverName ="$params->host"; //serverName\instanceName
        $connectionInfo = array( "Database"=>"$params->dbname", "UID"=>"$params->username", "PWD"=>"$params->password");
        $this->_dbLink = sqlsrv_connect( $serverName, $connectionInfo);
        if( !$this->_dbLink ) {throw new Exception("Unable to connect to InforM3");}
    }

    public function __destruct()
    {   
        sqlsrv_close($this->_dbLink);   
    }

    public function fetch($select)
    {
        $results = array();
        $query   = sqlsrv_query( $this->_dbLink,$select);
        while( $result = sqlsrv_fetch_array($query) ) {   $results[] = $result;  }
        return $results;
    }

    public function query($select) {
            $query = sqlsrv_query($this->_dbLink, $select);
    }

    public function saveProceed($id)    {       
        $select     = "SELECT UAORNO FROM RAAFAT.dbo.PRTORD WHERE UAORNO = '".$id['orderNumber']."'";
        $results    = $this->fetch($select);
        if (count($results)==0){
            $select = "INSERT INTO RAAFAT.dbo.PRTORD values('".$id['orderNumber']."','KORYUN','".date('Ymd')."',null,null,null,null,TRUE)";        
        } else {
            $select = "UPDATE RAAFAT.dbo.PRTORD SET UAUSER  = 'KORYUN', UADATE = '".date('Ymd')."', UACHEK = 1 WHERE UAORNO = '".$id['orderNumber']."'";        
        }
        $results    = $this->query($select);
    }

    public function updateProceedUser($id,$printType) 
    {
        $select     = "SELECT UAORNO FROM RAAFAT.dbo.PRTORD WHERE UAORNO = '".$id."'";        
        $results    = $this->fetch($select);
        if (count($results)==0){
            $select = "INSERT INTO RAAFAT.dbo.PRTORD values('".$id."',null,null,'KORYUN','".date('Ymd')."',null,null,null)";  
            $this->query($select);
        }
        $type='';
        if($printType ==0){
            $type = ', UARPRN=1';
        } else  {
            $type = ', UAPPRN=1';
        }       
        $select = "UPDATE RAAFAT.dbo.PRTORD SET UAUSR2 = 'KORYUN', UADAT2 = '".date('Ymd')."' ".$type."  WHERE UAORNO = '".$id."'";        
        $result    =   $this->query($select);
        
    } 
 

    public function buildMonthlySales($search){
        $select = '' ;
        If ($search['report_type'] == 'product_group') {            
            $select = "
            SELECT
                MMITCL AS Product_Group,
                 UBITNO AS Item_No,
                 CASE
                  WHEN MMFUDS != ''
                  THEN MMFUDS
                  ELSE MMITDS
                 END AS Description,
                 SUM(UBIVQT) AS Units,
                 SUM(UBLNAM) AS Sales
                FROM RAAFAT.dbo.ODLINE
                JOIN RAAFAT.dbo.ODHEAD ON (
                        UADLIX=UBDLIX
                        AND UBCONO=UACONO
                        AND UBDIVI=UADIVI )
                JOIN RAAFAT.dbo.MITMAS ON (
                        UBITNO=MMITNO
                        AND UBCONO=MMCONO )
                WHERE
                        UAIVDT BETWEEN '".date('Ymd',strtotime($search['start_date']))."' AND '".date('Ymd',strtotime($search['end_date']))."'
                        AND UBCONO=780
                        AND UBDIVI = 'BBB'
                GROUP BY MMITDS,UBITNO, MMITCL, MMFUDS
                ORDER BY MMITCL;";
        } else if ($search['report_type'] == 'sales_person') {
            $select = "
            SELECT
            OKSMCD AS Sales_Person,
            COUNT(UAEXIN) AS Invoice_Total,
           SUM(UANTAM) AS Net_Invoice
           FROM RAAFAT.dbo.ODHEAD
           LEFT JOIN RAAFAT.dbo.OCUSMA ON (
                   UACUNO=OKCUNO
                   AND UACONO=OKCONO )
           WHERE
                   UACONO=780
                   AND UADIVI='BBB'
                   AND UAIVDT BETWEEN '".date('Ymd',strtotime($search['start_date']))."' AND '".date('Ymd',strtotime($search['end_date']))."' GROUP BY OKSMCD ORDER BY SUM(UANTAM) DESC;";
        }  
        $results        =   $this->fetch($select);          
       
        return $results  ;
    }
    
    public function por($search) {
        
        $select = "        
            SELECT DISTINCT 
            FGRECL.F2PUNO As PO_Number
            ,FGRECL.F2ORTY As Order_Type
            ,MPLINE.IBPUST AS High_Status     
            ,MPLINE.IBPUSL  AS Low_Status
            ,FGRECL.F2SUNO As Vendor
            ,CIDMAS.IDSUNM As Vendor_Name
            ,FGRECL.F2BUYE As Buyer
            ,FGRECL.F2WHLO AS WHS
            ,FGRECL.F2ITNO AS Item
            ,MPLINE.IBPITD AS Descr   
            ,FGRECL.F2SCOC AS PPrice
            ,MPLINE.IBPPUN AS UOM
            ,FGRECL.F2CUCD AS Currency
            ,FGRECL.F2REPN AS Receiving_Number
            ,FGRECL.F2RPQT AS Reported_Qty
            --,FGRECL.F2BANO
            --,MITTRA.MTBANO
            ,CASE WHEN FGRECL.F2BANO = '' THEN MITTRA.MTBANO ELSE FGRECL.F2BANO END AS 'LOT_Number'
            ,FGRECL.F2RGDT AS RecDate
            ,FGRECL.F2RCAC AS Cost
            ,FGRECL.F2IASR AS Acc_Status
            ,FGRECL.F2ANBR AS Acc_Number
            ,FGRECL.F2CHID AS Changed_by
            ,FGRECL.F2TRTM AS Change_Time
            ,MPLINE.IBRORN As Ref_Number
            --,MPLINE.IBRORL
            --,MPLINE.IBRORC
            FROM RAAFAT.dbo.FGRECL 
            LEFT JOIN  RAAFAT.dbo.MPLINE 
            ON MPLINE.IBPUNO = FGRECL.F2PUNO
            AND MPLINE.IBITNO = FGRECL.F2ITNO
            AND MPLINE.IBCONO = FGRECL.F2CONO 
            AND MPLINE.IBPNLS = FGRECL.F2PNLS
            AND (IBPUST >= 50 AND IBPUST <= 85)
            AND (IBPUSL >= 20 AND IBPUSL <= 85)
            LEFT JOIN RAAFAT.dbo.MITTRA
            ON MITTRA.MTCONO = FGRECL.F2CONO
            AND MPLINE.IBRORN = MITTRA.MTRIDN
            AND MITTRA.MTITNO = FGRECL.F2ITNO
            AND MITTRA.MTTRQT = FGRECL.F2RPQT
            AND MTTTID = 'WMP'
            LEFT JOIN RAAFAT.dbo.CIDMAS
            ON CIDMAS.IDCONO = FGRECL.F2CONO
            AND CIDMAS.IDSUNO = FGRECL.F2SUNO
            WHERE F2CONO = 780
            AND F2FACI = 'B01'
            AND FGRECL.F2RGDT >= '".date('Ymd',strtotime($search['receipt_date']))."'
            AND (IBPUST <> 85 AND IBPUSL <> 85)
            --AND F2PUNO = '2002935'
            ORDER BY FGRECL.F2PUNO";        
      
        $results        =   $this->fetch($select);          
       
        return $results  ;
    }    
    
    
    public function buildReleasedOrdersCount(){
        $select = "SELECT
                    COUNT (OAORNO) AS rowscount
                    FROM
                    RAAFAT.dbo.OOHEAD
                    WHERE
                    OACONO = 780
                    AND OADIVI = 'BBB'
                    AND OAORST = 44
                    AND OAORSL = OAORST";
        
        $results        =   $this->fetch($select);
        return $results[0]['rowscount']  ;
                
    }
    public function buildReleasedOrders($search, $stage, $page =0){
        $cond = '';
        if($stage == 1){
            $cond  .=" AND UACHEK  IS NULL ";
        }else if($stage == 0){
            $cond  .=" AND UACHEK = 1 ";
        }
        if ($search != null){
            if($search['order_number']!= null){
                $cond  .=" AND OAORNO = '".strtoupper ($search['order_number'])."'";
            }
            if($search['customer_number']!= null){
                $cond  .=" AND OKCUNO = '".strtoupper ($search['customer_number'])."'";
            }
            if($search['Ship_Via']!= null){
                $cond  .=" AND OAMODL = '".strtoupper ($search['Ship_Via'])."'";
            }
            if($search['Order_Category']!= null){
                $cond  .=" AND OATEDL = '".strtoupper ($search['Order_Category'])."'";
            }
            if($search['Has_Probo']!= null){
                $cond  .=" AND PROBT = ".$search['Has_Probo'];
            }
            if($search['Order_date']!= null){
                $cond  .=" AND OARLDT <= ".date('Ymd', strtotime($search['Order_date']));
            }
            $page =0;
        }
        $limit = '';
//        if ($page != 0) {
            $limit = "OFFSET ".($page*20)." ROWS  FETCH NEXT 20 ROWS ONLY";
//        }
        
        $select = "
            SELECT
                OAORNO AS Order_no,
                OKCUNO AS customer_number,
                OKCUNM AS customer_name,
                CASE
                                WHEN OARLDT != 0 THEN CONVERT ( VARCHAR (10), CAST ( CAST (OARLDT AS CHAR(8)) AS DATE ), 101 )
                                ELSE '0'
                END AS Order_date,
                OATEPY AS Payment_terms,
                OAMODL AS Ship_Via,
                OATEDL AS Order_Category,
                OAORST AS Stat,
                OARESP AS CSA,
                OKECAR AS State,
                OKPONO AS Zip,
                (SELECT count(*) FROM RAAFAT.dbo.OOLINE ol WHERE ol.OBORNO = head.OAORNO AND ol.OBCONO=780 AND ol.OBDIVI='BBB') AS No_Items,
                CAST((SELECT SUM(ol.OBORQT) FROM RAAFAT.dbo.OOLINE ol WHERE ol.OBORNO = head.OAORNO AND ol.OBCONO=780 AND ol.OBDIVI='BBB') AS FLOAT) AS Total_Qty,
                (              SELECT MAX(CASE WHEN MMITCL = 'PROBT' THEN 1  ELSE 0 END)
                                FROM RAAFAT.dbo.OOLINE ol2
                                JOIN RAAFAT.dbo.MITMAS ON ( ol2.OBITNO = MMITNO AND  MMCONO=780)
                                WHERE ol2.OBORNO=head.OAORNO AND ol2.OBCONO=780 AND ol2.OBDIVI='BBB'
                ) AS Has_Probo,
                OKMODL AS Customer_Ship,
                OKTEDL AS Customer_Cat,
                OKTEPY AS Customer_Pay
            FROM
                    RAAFAT.dbo.OOHEAD head
            JOIN RAAFAT.dbo.OCUSMA ON ( OACUNO = OKCUNO AND OACONO = OKCONO )
            LEFT JOIN RAAFAT.dbo.PRTORD ord ON (OAORNO = UAORNO)
            WHERE
                OACONO = 780
                AND OADIVI = 'BBB'
                AND OAORST = 44
                AND OAORSL = OAORST
                $cond 
            ORDER BY OARLDT ASC ".$limit;

    //         print_r('<pre>')  ;
    //         print_r($select)  ;
    //         exit;
        $results        =   $this->fetch($select);
        return $results  ;
    }
    
    public function buildPicklistheader ($orderNumber) {
        $select = "
            SELECT 
                OAORNO AS ordno,
                OACUNO AS custkey,
                REPLACE(REPLACE(REPLACE(a.OKCUNM, '-', ' '),'(','_'),')','_') AS custname,
                a.OKCUA1 AS Shipto_Addr1,
                a.OKCUA2 AS Shipto_Addr2,
                a.OKCUA3 AS Shipto_Addr3,                
                a.OKCUA4 AS Country,
                a.OKTOWN AS CustTown,
                a.OKECAR AS CustState,
                OACUOR AS po_number,
                OATEPY AS Terms,
                OARESP AS CSA,
                OATEDL AS Ship_Via,
                CONVERT (VARCHAR (10), CAST (CAST (OAORDT AS CHAR(12)) AS DATE), 101) AS ord_date,
                OATEDL AS category,
                OAPYNO AS Sold_To,
                REPLACE(REPLACE(REPLACE(b.OKCUNM, '-', ' '),'(','_'),')','_') AS soldname,
                b.OKCUA1 sold_Shipto_Addr1,
                b.OKCUA2 sold_Shipto_Addr2,
                b.OKCUA3 sold_Shipto_Addr3,
                b.OKCUA4 sold_Country,
                b.OKTOWN AS sold_CustTown,
                b.OKECAR AS sold_CustState
            FROM
                RAAFAT.dbo.OOHEAD
                JOIN RAAFAT.dbo.OCUSMA a ON (
                    OACUNO = a.OKCUNO
                    AND a.OKCONO = 780)
                LEFT JOIN RAAFAT.dbo.OCUSMA b ON (
                    OAPYNO = b.OKCUNO
                    AND b.OKCONO = 780)
            WHERE
                OACONO = 780
                AND OADIVI = 'BBB'
                AND OAORST >= 44
                AND OAORSL >= 44
                AND OAORNO = '".$orderNumber."'
            ORDER BY OAORNO DESC ";
        
        $results        =   $this->fetch($select);
        return $results  ;
    }
    
    
    public function buildPicklist ($orderNumber, $ordertype = 0) 
    {
        if ($ordertype == 0){
            $orderCon = "ORDER BY MQITNO ";
        } else if ($ordertype == 1) {
            $orderCon = "ORDER BY MQWHSL, MQITNO";
        }
        $select = "
            SELECT
                MQRIDN AS ordno,
                LTRIM(RTRIM(MQITNO)) AS ItemKey,
                CASE
                    WHEN MMFUDS != '' THEN MMFUDS
                    ELSE MMITDS
                END AS Description,
                 (SELECT
                    TOP 1 SUM (CAST(OBORQT AS FLOAT))
                FROM
                    RAAFAT.dbo.OOLINE
                WHERE
                    OBCONO = 780
                AND OBDIVI = 'BBB'
                AND OBORNO = MQRIDN
                AND OBITNO = MQITNO ) AS QtyOrd,
                 SUM (CAST(MQALQT AS FLOAT)) AS QtyIssued,
                 MQWHSL AS binno,
                 MQBANO AS lotno,
                 CAST (MMNEWE AS FLOAT) AS weight,
                 MMITCL AS Category,
                 MPPOPN AS UPC,
                CASE
                    WHEN LMEXPI != 0 THEN CONVERT (VARCHAR (10),CAST (CAST (LMEXPI AS CHAR(8)) AS DATE),101)
                    ELSE '0'
                END AS exp_date                 
            FROM
                RAAFAT.dbo.MITALO
                LEFT JOIN RAAFAT.dbo.MITPOP ON (
                    MQCONO = MPCONO
                    AND MQITNO = MPITNO
                    AND MPALWQ = 'UPC')
                LEFT JOIN RAAFAT.dbo.MILOMA ON (
                    LMCONO = 780
                    AND LMITNO = MQITNO
                    AND LMBANO = MQBANO)
                LEFT JOIN RAAFAT.dbo.MITMAS ON (MMCONO = 780 AND MMITNO = MQITNO)
            WHERE
                MQCONO = 780
                AND MQTTYP = 31
                AND MQWHLO = 'JFI'
                AND MQRIDN = '".$orderNumber."'
            GROUP BY
                MQRIDN,
                MQITNO,
                MQWHSL,
                MQBANO,
                MMNEWE,
                MPPOPN,
                LMEXPI,
                MMITCL,
                MMFUDS,
                MMITDS
                $orderCon";
        
        $results        =   $this->fetch($select);
        return $results  ;
    }
    
    public function createPagination( $total ,  $page = 1  ) {
        $limit = 20;     
        $last       = ceil( $total / $limit );
        $start      =  1;
        $end        =  $last;
        $html       = '<ul class="pagination">';
        $class      = ( $page == 1 ) ? "disabled" : "";
        $html       .= '<li ' . $class . '><a href="?page=' . ( $page  ) . '">&laquo;</a></li>';
        if ( $start > 1 ) {
            $html   .= '<li><a href="?page=1">1</a></li>';
            $html   .= '<li disabled><span>...</span></li>';
        }
        for ( $i = $start ; $i <= $end; $i++ ) {
            $class  = ( $page == $i ) ? "active" : "";
            $html   .= '<li class="' . $class . '"><a href="?page=' . $i . '">' . $i . '</a></li>';
        }
        if ( $end < $last ) {
            $html   .= '<li disabled><span>...</span></li>';
            $html   .= '<li><a href="?page=' . $last . '">' . $last . '</a></li>';
        }
        $class      = ( $page == $last ) ? "disabled" : "";
        $html       .= '<li ' . $class . '><a href="?page=' . ( $page + 1 ) . '">&raquo;</a></li>';
        $html       .= '</ul>';

        return $html;
    }
    
    public function agingReport($search) 
    {
        $cond = '';
        
        if ($search != null){
            if($search['item_number']!= null){
                $cond  .=" AND IBITNO = '".strtoupper ($search['item_number'])."'";
            }
            if($search['buyer']!= null){
                $cond  .=" AND IBBUYE = '".strtoupper ($search['buyer'])."'";
            }
            if($search['status']== "within_QC_window" ){
                $cond  .=" AND DATEDIFF (day,GETDATE(),convert(date, CONVERT(varchar(10),IBRCDT,101))) >= -21 ";
            }
            if($search['status']== "outside_QC_window" ){
                $cond  .=" AND DATEDIFF (day,GETDATE(),convert(date, CONVERT(varchar(10),IBRCDT,101))) < -21 ";
            }
            if($search['start_date']!= null){
                $cond  .=" AND IBRCDT >= ".date('Ymd', strtotime($search['start_date']));
            }
            if($search['end_date']!= null){
                $cond  .=" AND IBRCDT <= ".date('Ymd', strtotime($search['end_date']));
            }
        }
        $select = "SELECT 
                    IBITNO AS ITNO
                    ,IBPITD AS ITDesc
                    ,IBPUNO AS PONum
                    ,IBWHLO AS WHSE
                    ,IBSUNO AS Supplier
                    ,IBPUSL AS LStatus
                    ,CAST(IBORQA AS FLOAT) AS OrdQty
                    ,CAST(IBCFQA AS FLOAT) AS ConfQty
                    ,CAST(IBRVQA AS FLOAT) AS ReceivedQty
                    ,CASE
                       WHEN IBRCDT != 0 THEN CONVERT ( VARCHAR (10), CAST ( CAST (IBRCDT AS CHAR(8)) AS DATE ), 101 )
                       ELSE '0' END AS ReceivedDate
                    ,CAST(CASE
                           WHEN IBCFQA = 0 THEN IBORQA-IBRVQA
                           ELSE IBCFQA-IBRVQA
                           END AS FLOAT) AS QtyBalance
                    ,IBPUUN AS UOM
                    ,CAST(IBCPPR AS FLOAT) AS PPrice
                    ,CASE
                       WHEN IBDWDT != 0 THEN CONVERT ( VARCHAR (10), CAST ( CAST (IBDWDT AS CHAR(8)) AS DATE ), 101 )
                       ELSE '0' END AS ReqDelDate
                    ,CASE
                       WHEN IBCODT != 0 THEN CONVERT ( VARCHAR (10), CAST ( CAST (IBCODT AS CHAR(8)) AS DATE ), 101 )
                       ELSE '0' END AS ConfirmedDate
                    ,CASE
                       WHEN IBECDD != 0 THEN CONVERT ( VARCHAR (10), CAST ( CAST (IBECDD AS CHAR(8)) AS DATE ), 101 )
                       ELSE '0' END AS PrevConfDate
                    ,CASE
                       WHEN IBRGDT != 0 THEN CONVERT ( VARCHAR (10), CAST ( CAST (IBRGDT AS CHAR(8)) AS DATE ), 101 )
                       ELSE '0' END AS EntryDate
                    ,CASE
                       WHEN IBLMDT != 0 THEN CONVERT ( VARCHAR (10), CAST ( CAST (IBLMDT AS CHAR(8)) AS DATE ), 101 )
                       ELSE '0' END AS ChangDate
                    ,IBBUYE AS Buyer
                    ,(DATEDIFF (day,GETDATE(),convert(date, CONVERT(varchar(10),IBRCDT,101)))) AS QCAging
                    ,CASE 
                           WHEN DATEDIFF (day,GETDATE(),convert(date, CONVERT(varchar(10),IBRCDT,101))) >= -21 THEN 'Within_QC_window'
                           ELSE 'Outside_QC_window' 
                           END AS STATUS
                    FROM RAAFAT.dbo.MPLINE
                    WHERE IBCONO = 780
                    AND IBFACI = 'B01'
                    AND (IBPUSL >= 50 AND IBPUSL <= 70)
                    $cond 
                    ORDER BY IBRCDT
                    ";
        
        $results        =   $this->fetch($select);
        return $results  ;

    }
}


?>
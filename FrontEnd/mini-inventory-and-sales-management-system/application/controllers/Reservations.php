<?php
defined('BASEPATH') OR exit('');
require_once 'functions.php';
/**
 * Description of Reservations
 *
 */
class Reservations extends CI_Controller{
    private $total_before_discount = 0, $discount_amount = 0, $vat_amount = 0, $eventual_total = 0;
    
    public function __construct(){
        parent::__construct();
        
        $this->genlib->checkLogin();
        
        $this->load->model(['reservation', 'item']);
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    public function index(){
        $resData['items'] = $this->item->getActiveItems('name', 'ASC');//get items with at least one qty left, to be used when doing a new reservation
        
        $data['pageContent'] = $this->load->view('reservations/reservations', $resData, TRUE);
        $data['pageTitle'] = "Reservations";
        
        $this->load->view('main', $data);
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * lars_ = "Load All Reservations"
     */
    public function lars_(){
        //set the sort order
        $orderBy = $this->input->get('orderBy', TRUE) ? $this->input->get('orderBy', TRUE) : "resId";
        $orderFormat = $this->input->get('orderFormat', TRUE) ? $this->input->get('orderFormat', TRUE) : "DESC";
        
        //count the total number of reservation group (grouping by the ref) in db
        $totalReservations = $this->reservation->totalReservations();
        
        $this->load->library('pagination');
        
        $pageNumber = $this->uri->segment(3, 0);//set page number to zero if the page number is not set in the third segment of uri
	
        $limit = $this->input->get('limit', TRUE) ? $this->input->get('limit', TRUE) : 10;//show $limit per page
        $start = $pageNumber == 0 ? 0 : ($pageNumber - 1) * $limit;//start from 0 if pageNumber is 0, else start from the next iteration
        
        //call setPaginationConfig($totalRows, $urlToCall, $limit, $attributes) in genlib to configure pagination
        $config = $this->genlib->setPaginationConfig($totalReservations, "reservations/lars_", $limit, ['onclick'=>'return lars_(this.href);']);
        
        $this->pagination->initialize($config);//initialize the library class
        
        //get all reservations from db
        $data['allReservations'] = $this->reservation->getAll($orderBy, $orderFormat, $start, $limit);
        $data['range'] = $totalReservations > 0 ? "Mostrando " .($start+1) . "-" . ($start + count($data['allReservations'])) . " de " . $totalReservations : "";
        $data['links'] = $this->pagination->create_links();//page links
        $data['sn'] = $start+1;
        
        $json['resTable'] = $this->load->view('reservations/restable', $data, TRUE);//get view with populated reservations table

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    
    /**
     * nso_ = "New Sales Order"
     */
    public function nso_(){
        $this->genlib->ajaxOnly();
        
        $arrOfItemsDetails = json_decode($this->input->post('_aoi', TRUE));
        $_mop = $this->input->post('_mop', TRUE);//mode of payment
        $_at = round($this->input->post('_at', TRUE), 2);//amount tendered
        $_cd = $this->input->post('_cd', TRUE);//change due
        $cumAmount = $this->input->post('_ca', TRUE);//cumulative amount
        $vatPercentage = $this->input->post('vat', TRUE);//vat percentage
        $discount_percentage = $this->input->post('discount', TRUE);//discount percentage
        $cust_name = $this->input->post('cn', TRUE);
        $cust_phone = $this->input->post('cp', TRUE);
        $cust_email = $this->input->post('ce', TRUE);
        
        /*
         * Loop through the arrOfItemsDetails and ensure each item's details has not been manipulated
         * The unitPrice must match the item's unit price in db, the totPrice must match the unitPrice*qty
         * The cumAmount must also match the total of all totPrice in the arr in addition to the amount of 
         * VAT (based on the vat percentage) and minus the $discount_percentage (if available)
         */
        
        $allIsWell = $this->validateItemsDet($arrOfItemsDetails, $cumAmount, $_at, $vatPercentage, $discount_percentage);
        
        if($allIsWell){//insert each sales order into db, generate receipt and return info to client
            
            //will insert info into db and return reservation's receipt
            $returnedData = $this->insertTrToDb($arrOfItemsDetails, $_mop, $_at, $cumAmount, $_cd, $this->vat_amount, $vatPercentage, $this->discount_amount, 
                    $discount_percentage, $cust_name, $cust_phone, $cust_email);
            
            $json['status'] = $returnedData ? 1 : 0;
            $json['msg'] = $returnedData ? "Reservation successfully processed" :
                    "Unable to process your request at this time. Pls try again later "
                    . "or contact technical department for assistance";
            $json['resReceipt'] = $returnedData['resReceipt'];
            
            $json['totalEarnedToday'] = number_format($this->reservation->totalEarnedToday());
            
            //add into eventlog
            //function header: addevent($event, $eventRowIdOrRef, $eventDesc, $eventTable, $staffId) in 'genmod'
            $eventDesc = count($arrOfItemsDetails). " items totalling &#8369;". number_format($cumAmount, 2)
                    ." with reference number {$returnedData['resRef']} was purchased";
            
            $this->genmod->addevent("New Reservation", $returnedData['resRef'], $eventDesc, 'reservations', $this->session->admin_id);
       }
        
        else{//return error msg
            $json['status'] = 0;
            $json['msg'] = "Reservation could not be processed. Please ensure there are no errors. Thanks";
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }
    
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * Validates the details of items sent from client to prevent manipulation
     * @param type $arrOfItemsInfo
     * @param type $cumAmountFromClient
     * @param type $amountTendered
     * @param type $vatPercentage
     * @param type $discount_percentage
     * @return boolean
     */
    private function validateItemsDet($arrOfItemsInfo, $cumAmountFromClient, $amountTendered, $vatPercentage, $discount_percentage){
        $error = 0;
        
        //loop through the item's info and validate each
        //return error if at least one seems suspicious (i.e. fails validation)
        foreach ($arrOfItemsInfo as $get){
            $itemCode = $get->_iC;//use this to get the item's unit price, then multiply it with the qty sent from client
            $qtyToBuy = $get->qty;
            $unitPriceFromClient = $get->unitPrice;
            $unitPriceInDb = $this->genmod->gettablecol('items', 'unitPrice', 'code', $itemCode);
            $totPriceFromClient = $get->totalPrice;
            
            //ensure both unit price matches
            $unitPriceInDb == $unitPriceFromClient ? "" : $error++;
            
            $expectedTotPrice = round($qtyToBuy*$unitPriceInDb, 2);//calculate expected totPrice
            
            //ensure both matches
            $expectedTotPrice == $totPriceFromClient ? "" : $error++;
            
            //no need to validate others, just break out of the loop if one fails validation
            if($error > 0){
                return FALSE;
            }
            
            $this->total_before_discount += $expectedTotPrice;
        }
        
        /**
         * We need to save the total price before tax, tax amount, total price after tax, discount amount, eventual total
         */
        
        $expectedCumAmount = $this->total_before_discount;
        
        //now calculate the discount amount (if there is discount) based on the discount percentage and subtract it(discount amount) 
        //from $total_before_discount
        if($discount_percentage){
            $this->discount_amount = $this->getDiscountAmount($expectedCumAmount, $discount_percentage);

            $expectedCumAmount = round($expectedCumAmount - $this->discount_amount, 2);
        }
        
        //add VAT amount to $expectedCumAmount is VAT percentage is set
        if($vatPercentage){
            //calculate vat amount using $vatPercentage and add it to $expectedTotPrice
            $this->vat_amount = $this->getVatAmount($expectedCumAmount, $vatPercentage);

            //now add the vat amount to expected total price
            $expectedCumAmount = round($expectedCumAmount + $this->vat_amount, 2);
        }        
        
        //check if cum amount also matches and ensure amount tendered is not less than $expectedCumAmount
        if(($expectedCumAmount != $cumAmountFromClient) || ($expectedCumAmount > $amountTendered)){
            return FALSE;
        }
        
        //if code execution reaches here, it means all is well
        $this->eventual_total = $expectedCumAmount;
        return TRUE;
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * 
     * @param type $arrOfItemsDetails
     * @param type $_mop
     * @param type $_at
     * @param type $cumAmount
     * @param type $_cd
     * @param type $vatAmount
     * @param type $vatPercentage
     * @param type $discount_amount
     * @param type $discount_percentage
     * @param type $cust_name
     * @param type $cust_phone
     * @param type $cust_email
     * @return boolean
     */
    private function insertTrToDb($arrOfItemsDetails, $_mop, $_at, $cumAmount, $_cd, $vatAmount, $vatPercentage, $discount_amount, $discount_percentage, $cust_name, $cust_phone, $cust_email){
        $allResInfo = [];//to hold info of all items' in reservation

        //generate random string to use as reservation ref
        //keep regeneration the ref if generated ref exist in db
        do{
            $ref = strtoupper(generateRandomCode('numeric', 6, 10, ""));
        }
        
        while($this->reservation->isRefExist($ref));
        
        //loop through the items' details and insert them one by one
        //start reservation
        $this->db->res_start();

        foreach($arrOfItemsDetails as $get){
            $itemCode = $get->_iC;
            $itemName = $this->genmod->getTableCol('items', 'name', 'code', $itemCode);
            $qtySold = $get->qty;//qty selected for item in loop
            $unitPrice = $get->unitPrice;//unit price of item in loop
            $totalPrice = $get->totalPrice;//total price for item in loop

            /*
             * add reservation to db
             * function header: add($_iN, $_iC, $desc, $q, $_up, $_tp, $_tas, $_at, $_cd, $_mop, $_tt, $ref, $_va, $_vp, $da, $dp, $cn, $cp, $ce)
             */
            $resId = $this->reservation->add($itemName, $itemCode, "", $qtySold, $unitPrice, $totalPrice, $cumAmount, $_at, $_cd,
                    $_mop, 1, $ref, $vatAmount, $vatPercentage, $discount_amount, $discount_percentage, $cust_name, $cust_phone, 
                    $cust_email);
            
            $allResInfo[$resId] = ['itemName'=>$itemName, 'quantity'=>$qtySold, 'unitPrice'=>$unitPrice, 'totalPrice'=>$totalPrice];
            
            //update item quantity in db by removing the quantity bought
            //function header: decrementItem($itemId, $numberToRemove)
            $this->item->decrementItem($itemCode, $qtySold);
        }

        $this->db->res_complete();//end reservation

        //ensure there was no error
        //works in production since db_debug would have been turned off
        if($this->db->res_status() === FALSE){
            return false;
        }
        
        else{
            $dataToReturn = [];
            
            //get reservation date in db, to be used on the receipt. It is necessary since date and time must matc
            $dateInDb = $this->genmod->getTableCol('reservations', 'resDate', 'resId', $resId);
            
            //generate receipt to return
            $dataToReturn['resReceipt'] = $this->genResReceipt($allResInfo, $cumAmount, $_at, $_cd, $ref, $dateInDb, $_mop, $vatAmount, $vatPercentage, $discount_amount, $discount_percentage, $cust_name, $cust_phone, $cust_email);
            $dataToReturn['resRef'] = $ref;
            
            return $dataToReturn;
        }
    }

    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

    /**
     * 
     * @param type $allResInfo
     * @param type $cumAmount
     * @param type $_at
     * @param type $_cd
     * @param type $ref
     * @param type $resDate
     * @param type $_mop
     * @param type $vatAmount
     * @param type $vatPercentage
     * @param type $discount_amount
     * @param type $discount_percentage
     * @param type $cust_name
     * @param type $cust_phone
     * @param type $cust_email
     * @return type
     */
    private function genResReceipt($allResInfo, $cumAmount, $_at, $_cd, $ref, $resDate, $_mop, $vatAmount, $vatPercentage,
        $discount_amount, $discount_percentage, $cust_name, $cust_phone, $cust_email){
        $data['allResInfo'] = $allResInfo;
        $data['cumAmount'] = $cumAmount;
        $data['amountTendered'] = $_at;
        $data['changeDue'] = $_cd;
        $data['ref'] = $ref;
        $data['resDate'] = $resDate;
        $data['_mop'] = $_mop;
        $data['vatAmount'] = $vatAmount;
        $data['vatPercentage'] = $vatPercentage;
        $data['discountAmount'] = $discount_amount;
        $data['discountPercentage'] = $discount_percentage;
        $data['cust_name'] = $cust_name;
        $data['cust_phone'] = $cust_phone;
        $data['cust_email'] = $cust_email;
        
        //generate and return receipt
        $resReceipt = $this->load->view('reservations/resreceipt', $data, TRUE);
        
        return $resReceipt;
    }
    
    
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * vtr_ = "View reservation's receipt"
     * Used when a reservation's ref is clicked
     */
    public function vtr_(){
        $this->genlib->ajaxOnly();
        
        $ref = $this->input->post('ref');
        
        $resInfo = $this->reservation->getResInfo($ref);
        
        //loop through the resInfo to get needed info
        if($resInfo){
            $json['status'] = 1;
            
            $cumAmount = $resInfo[0]['totalMoneySpent'];
            $amountTendered = $resInfo[0]['amountTendered'];
            $changeDue = $resInfo[0]['changeDue'];
            $resDate = $resInfo[0]['resDate'];
            $modeOfPayment = $resInfo[0]['modeOfPayment'];
            $vatAmount = $resInfo[0]['vatAmount'];
            $vatPercentage = $resInfo[0]['vatPercentage'];
            $discountAmount = $resInfo[0]['discount_amount'];
            $discountPercentage = $resInfo[0]['discount_percentage'];
            $cust_name = $resInfo[0]['cust_name'];
            $cust_phone = $resInfo[0]['cust_phone'];
            $cust_email = $resInfo[0]['cust_email'];
            
            $json['resReceipt'] = $this->genResReceipt($resInfo, $cumAmount, $amountTendered, $changeDue, $ref,
                $resDate, $modeOfPayment, $vatAmount, $vatPercentage, $discountAmount, $discountPercentage, $cust_name,
                $cust_phone, $cust_email);
        }
        
        else{
            $json['status'] = 0;
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * Calculates the amount of VAT
     * @param type $cumAmount the total amount to calculate the VAT from
     * @param type $vatPercentage the percentage of VAT
     * @return type
     */
    private function getVatAmount($cumAmount, $vatPercentage){
        $vatAmount = ($vatPercentage/100) * $cumAmount;

        return $vatAmount;
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * Calculates the amount of Discount
     * @param type $cum_amount the total amount to calculate the discount from
     * @param type $discount_percentage the percentage of discount
     * @return type
     */
    private function getDiscountAmount($cum_amount, $discount_percentage){
        $discount_amount = ($discount_percentage/100) * $cum_amount;

        return $discount_amount;
    }
    
    /*
    ****************************************************************************************************************************
    ****************************************************************************************************************************
    ****************************************************************************************************************************
    ****************************************************************************************************************************
    ****************************************************************************************************************************
    */
    
    public function report($from_date, $to_date=''){        
        //get all reservations from db ranging from $from_date to $to_date
        $data['from'] = $from_date;
        $data['to'] = $to_date ? $to_date : date('Y-m-d');
        
        $data['allReservations'] = $this->reservation->getDateRange($from_date, $to_date);
        
        $this->load->view('reservations/resReport', $data);
    }

        public function delete(){
            $this->genlib->ajaxOnly();

            $supplier_id = $this->input->post('_aId');
            $new_value = $this->genmod->gettablecol('reservation', 'deleted', 'id', $res_id) == 1 ? 0 : 1;

            $done = $this->reservation->delete($res_id, $new_value);

            $json['status'] = $done ? 1 : 0;
            $json['_nv'] = $new_value;
            $json['_aId'] = $res_id;

            $this->output->set_content_type('application/json')->set_output(json_encode($json));
        }
}

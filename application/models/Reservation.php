<?php

defined('BASEPATH') OR exit('');

/**
 * Description of Transaction
 *
 */
class Reservation extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /*
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     */

    /**
     * Get all transactions
     * @param type $orderBy
     * @param type $orderFormat
     * @param type $start
     * @param type $limit
     * @return boolean
     */
    public function getAll($orderBy, $orderFormat, $start, $limit) {
        if ($this->db->platform() == "sqlite3") {
            $q = "SELECT reservations.ref, reservations.totalMoneySpent, reservations.modeOfPayment, reservations.staffId,
                reservations.resDate, reservations.lastUpdated, reservations.amountTendered, reservations.changeDue,
                admin.first_name || ' ' || admin.last_name AS 'staffName', SUM(reservations.quantity) AS 'quantity',
                reservations.cust_name, reservations.cust_phone, reservations.cust_email
                FROM reservations
                LEFT OUTER JOIN admin ON reservations.staffId = admin.id
                GROUP BY ref
                ORDER BY {$orderBy} {$orderFormat}
                LIMIT {$limit} OFFSET {$start}";

            $run_q = $this->db->query($q);
        }
        else {
            $this->db->select('reservations.ref, reservations.totalMoneySpent, reservations.modeOfPayment, reservations.staffId,
            reservations.resDate, reservations.lastUpdated, reservations.amountTendered, reservations.changeDue,
                CONCAT_WS(" ", admin.first_name, admin.last_name) as "staffName",
                reservations.cust_name, reservations.cust_phone, reservations.cust_email');
            
            $this->db->select_sum('reservations.quantity');
            
            $this->db->join('admin', 'reservations.staffId = admin.id', 'LEFT');
            $this->db->limit($limit, $start);
            $this->db->group_by('ref');
            $this->db->order_by($orderBy, $orderFormat);

            $run_q = $this->db->get('reservations');
        }

        if ($run_q->num_rows() > 0) {
            return $run_q->result();
        }
        else {
            return FALSE;
        }
    }

    /*
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     */

    /**
     * 
     * @param type $_iN item Name
     * @param type $_iC item Code
     * @param type $desc Desc
     * @param type $q quantity bought
     * @param type $_up unit price
     * @param type $_tp total price
     * @param type $_tas total amount spent
     * @param type $_at amount tendered
     * @param type $_cd change due
     * @param type $_mop mode of payment
     * @param type $_tt transaction type whether (sale{1} or return{2})
     * @param type $ref
     * @param float $_va VAT Amount
     * @param float $_vp VAT Percentage
     * @param float $da Discount Amount
     * @param float $dp Discount Percentage
     * @param {string} $cn Customer Name
     * @param {string} $cp Customer Phone
     * @param {string} $ce Customer Email
     * @return boolean
     */
    public function add($_iN, $_iC, $desc, $q, $_up, $_tp, $_tas, $_at, $_cd, $_mop, $_tt, $ref, $_va, $_vp, $da, $dp, $cn, $cp, $ce) {
        $data = ['itemName' => $_iN, 'itemCode' => $_iC, 'description' => $desc, 'quantity' => $q, 'unitPrice' => $_up, 'totalPrice' => $_tp,
            'amountTendered' => $_at, 'changeDue' => $_cd, 'modeOfPayment' => $_mop, 'resType' => $_tt,
            'staffId' => $this->session->admin_id, 'totalMoneySpent' => $_tas, 'ref' => $ref, 'vatAmount' => $_va,
            'vatPercentage' => $_vp, 'discount_amount'=>$da, 'discount_percentage'=>$dp, 'cust_name'=>$cn, 'cust_phone'=>$cp,
            'cust_email'=>$ce];

        //set the datetime based on the db driver in use
        $this->db->platform() == "sqlite3" ?
            $this->db->set('resDate', "datetime('now')", FALSE) :
            $this->db->set('resDate', "NOW()", FALSE);

        $this->db->insert('reservations', $data);

        if ($this->db->affected_rows()) {
            return $this->db->insert_id();
        }
        else {
            return FALSE;
        }
    }

    /*
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     */

    /**
     * Primarily used t check whether a prticular ref exists in db
     * @param type $ref
     * @return boolean
     */
    public function isRefExist($ref) {
        $q = "SELECT DISTINCT ref FROM reservations WHERE ref = ?";

        $run_q = $this->db->query($q, [$ref]);

        if ($run_q->num_rows() > 0) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    /*
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     */

    public function resSearch($value) {
        $this->db->select('reservations.ref, reservations.totalMoneySpent, reservations.modeOfPayment, reservations.staffId,
                reservations.resDate, reservations.lastUpdated, reservations.amountTendered, reservations.changeDue,
                CONCAT_WS(" ", admin.first_name, admin.last_name) as "staffName",
                reservations.cust_name, reservations.cust_phone, reservations.cust_email');
        $this->db->select_sum('reservations.quantity');
        $this->db->join('admin', 'reservations.staffId = admin.id', 'LEFT');
        $this->db->like('ref', $value);
        $this->db->or_like('itemName', $value);
        $this->db->or_like('itemCode', $value);
        $this->db->group_by('ref');

        $run_q = $this->db->get('reservations');

        if ($run_q->num_rows() > 0) {
            return $run_q->result();
        }
        else {
            return FALSE;
        }
    }

    /*
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     */

    /**
     * Get all transactions with a particular ref
     * @param type $ref
     * @return boolean
     */
    public function getresinfo($id) {
        $q = "SELECT * FROM reservations WHERE ref = ?";

        $run_q = $this->db->query($q, [$ref]);

        if ($run_q->num_rows() > 0) {
            return $run_q->result_array();
        }
        else {
            return FALSE;
        }
    }

    /*
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     */

    /**
     * selects the total number of transactions done so far
     * @return boolean
     */
    public function totalResactions() {
        $q = "SELECT count(DISTINCT REF) as 'totalRes' FROM reservations";

        $run_q = $this->db->query($q);

        if ($run_q->num_rows() > 0) {
            foreach ($run_q->result() as $get) {
                return $get->totalRes;
            }
        }
        else {
            return FALSE;
        }
    }

    /*
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     */

    /**
     * Calculates the total amount earned today
     * @return boolean
     */
    public function totalEarnedToday() {
        $q = "SELECT totalMoneySpent FROM reservations WHERE DATE(resDate) = CURRENT_DATE GROUP BY ref";

        $run_q = $this->db->query($q);

        if ($run_q->num_rows()) {
            $totalEarnedToday = 0;

            foreach ($run_q->result() as $get) {
                $totalEarnedToday += $get->totalMoneySpent;
            }

            return $totalEarnedToday;
        }
        else {
            return FALSE;
        }
    }

    /*
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     */

    //Not in use yet
    public function totalEarnedOnDay($date) {
        $q = "SELECT SUM(totalPrice) as 'totalEarnedToday' FROM reservations WHERE DATE(resDate) = {$date}";

        $run_q = $this->db->query($q);

        if ($run_q->num_rows() > 0) {
            foreach ($run_q->result() as $get) {
                return $get->totalEarnedToday;
            }
        }
        else {
            return FALSE;
        }
    }

    /*
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     * *******************************************************************************************************************************
     */
    
    public function getDateRange($from_date, $to_date){
        if ($this->db->platform() == "sqlite3") {
            $q = "SELECT reservations.ref, reservations.totalMoneySpent, reservations.modeOfPayment, reservations.staffId,
                reservations.resDate, reservations.lastUpdated, reservations.amountTendered, reservations.changeDue,
                admin.first_name || ' ' || admin.last_name AS 'staffName', SUM(reservations.quantity) AS 'quantity',
                reservations.cust_name, reservations.cust_phone, reservations.cust_email
                FROM reservations
                LEFT OUTER JOIN admin ON reservations.staffId = admin.id
                WHERE 
                date(reservations.resDate) >= {$from_date} AND date(reservations.resDate) <= {$to_date}
                GROUP BY ref
                ORDER BY reservations.resId DESC";

            $run_q = $this->db->query($q);
        }
        
        else {
            $this->db->select('reservations.ref, reservations.totalMoneySpent, reservations.modeOfPayment, reservations.staffId,
                    reservations.resDate, reservations.lastUpdated, reservations.amountTendered, reservations.changeDue,
                    CONCAT_WS(" ", admin.first_name, admin.last_name) AS "staffName",
                    reservations.cust_name, reservations.cust_phone, reservations.cust_email');

            $this->db->select_sum('reservations.quantity');

            $this->db->join('admin', 'reservations.staffId = admin.id', 'LEFT');

            $this->db->where("DATE(reservations.resDate) >= ", $from_date);
            $this->db->where("DATE(reservations.resDate) <= ", $to_date);

            $this->db->order_by('reservations.resId', 'DESC');

            $this->db->group_by('ref');

            $run_q = $this->db->get('reservations');
        }
        
        return $run_q->num_rows() ? $run_q->result() : FALSE;
    }
}

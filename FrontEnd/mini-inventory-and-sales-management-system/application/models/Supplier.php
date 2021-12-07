<?php
defined('BASEPATH') OR exit('');

/**
 * Description of Supplier
 *
 */
class Supplier extends CI_Model{
    public function __construct(){
        parent::__construct();
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
     * @param type $f_name
     * @param type $l_name
     * @param type $email
     * @param type $password
     * @param type $role
     * @param type $mobile1
     * @param type $mobile2
     * @return boolean
     */
    public function add($f_name, $l_name, $email, $password, $role, $mobile1, $mobile2){
        $data = ['first_name'=>$f_name, 'last_name'=>$l_name, 'email'=>$email, 'password'=>$password, 'role'=>$role,
            'mobile1'=>$mobile1, 'mobile2'=>$mobile2];
        
        //set the datetime based on the db driver in use
        $this->db->platform() == "sqlite3" 
                ? 
        $this->db->set('created_on', "datetime('now')", FALSE) 
                : 
        $this->db->set('created_on', "NOW()", FALSE);
        
        $this->db->insert('supplier', $data);
        
        if($this->db->affected_rows() > 0){
            return $this->db->insert_id();
        }
        
        else{
            return FALSE;
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
     * @param type $supplier_id
     * @return boolean
     */
    public function update_last_login($supplier_id){
        $this->db->where('id', $supplier_id);
        
        //set the datetime based on the db driver in use
        $this->db->platform() == "sqlite3" 
                ? 
        $this->db->set('last_login', "datetime('now')", FALSE) 
                : 
        $this->db->set('last_login', "NOW()", FALSE);
        
        $this->db->update('supplier');
        
        if(!$this->db->error()){
            return TRUE;
        }
        
        else{
            return FALSE;
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
     * Get some details about a supplier (stored in session)
     * @param type $email
     * @return boolean
     */
    public function get_supplier_info($email){
        $this->db->select('id, first_name, last_name, role');
        $this->db->where('email', $email);

        $run_q = $this->db->get('supplier');

        if($run_q->num_rows() > 0){
            return $run_q->result();
        }

        else{
            return FALSE;
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
     * @param type $orderBy
     * @param type $orderFormat
     * @param type $start
     * @param type $limit
     * @return boolean
     */
    public function getAll($orderBy = "first_name", $orderFormat = "ASC", $start = 0, $limit = ""){
        $this->db->select('id, first_name, last_name, email, role, mobile1, mobile2, created_on, last_login, account_status, deleted');
        $this->db->order_by($orderBy, $orderFormat);
        $run_q = $this->db->get('supplier');
        
        if($run_q->num_rows() > 0){
            return $run_q->result();
        }
        
        else{
            return FALSE;
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
    * @param type $supplier_id
    * @param type $new_status New account status
    * @return boolean
    */ 
    public function suspend($supplier_id, $new_status){
        $this->db->where('id', $supplier_id);
        $this->db->update('supplier', ['account_status'=>$new_status]);

        if($this->db->affected_rows()){
            return TRUE;
        }

        else{
            return FALSE;
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
    * @param type $supplier_id
    * @param type $new_value
    * @return boolean
    */
    public function delete($supplier_id, $new_value){
        $this->db->where('id', $supplier_id);
        $this->db->update('supplier', ['deleted'=>$new_value]);
       
        if($this->db->affected_rows()){
            return TRUE;
        }

        else{
            return FALSE;
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
     * @param type $value
     * @return boolean
     */
    public function supplierSearch($value){
        $q = "SELECT * FROM supplier WHERE 
                id != {$_SESSION['supplier_id']}
                    AND
                (
                MATCH(first_name) AGAINST(?)
                || MATCH(last_name) AGAINST(?)
                || MATCH(first_name, last_name) AGAINST(?)
                || MATCH(email) AGAINST(?)
                || MATCH(mobile1) AGAINST(?)
                || MATCH(mobile2) AGAINST(?)
                || first_name LIKE '%".$this->db->escape_like_str($value)."%'
                || last_name LIKE '%".$this->db->escape_like_str($value)."%' 
                || email LIKE '%".$this->db->escape_like_str($value)."%'
                || mobile1 LIKE '%".$this->db->escape_like_str($value)."%'
                || mobile2 LIKE '%".$this->db->escape_like_str($value)."%'
                )";

        $run_q = $this->db->query($q, [$value, $value, $value, $value, $value, $value]);

        if($run_q->num_rows() > 0){
            return $run_q->result();
        }

        else{
            return FALSE;
        }
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    public function update($supplier_id, $first_name, $last_name, $email, $mobile1, $mobile2, $role){
        $data = ['first_name'=>$first_name, 'last_name'=>$last_name, 'mobile1'=>$mobile1, 'mobile2'=>$mobile2, 'email'=>$email, 
            'role'=>$role];
        
        $this->db->where('id', $supplier_id);
        
        $this->db->update('supplier', $data);
        
        return TRUE;
    }
    
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    
   
}

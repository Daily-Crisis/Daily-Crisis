<?php
defined('BASEPATH') OR exit('');

/**
 * Description of Supplier
 */
class Suppliers extends CI_Controller{
    
    public function __construct(){
        parent::__construct();
        
        $this->genlib->checkLogin();
        
        $this->genlib->superOnly();
        
        $this->load->model(['supplier']);
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    public function index(){
        $data['pageContent'] = $this->load->view('supplier/supplier', '', TRUE);
        $data['pageTitle'] = "Suppliers";
        
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
     * lasp_ = "Load all suppliers"
     */
    public function lasp_(){
        //set the sort order
        $orderBy = $this->input->get('orderBy', TRUE) ? $this->input->get('orderBy', TRUE) : "first_name";
        $orderFormat = $this->input->get('orderFormat', TRUE) ? $this->input->get('orderFormat', TRUE) : "ASC";
        
        //count the total suppliers in db (excluding the currently logged in supplier)
        $totalSuppliers = count($this->supplier->getAll());
        
        $this->load->library('pagination');
        
        $pageNumber = $this->uri->segment(3, 0);//set page number to zero if the page number is not set in the third segment of uri
	
        $limit = $this->input->get('limit', TRUE) ? $this->input->get('limit', TRUE) : 10;//show $limit per page
        $start = $pageNumber == 0 ? 0 : ($pageNumber - 1) * $limit;//start from 0 if pageNumber is 0, else start from the next iteration
        
        //call setPaginationConfig($totalRows, $urlToCall, $limit, $attributes) in genlib to configure pagination
        $config = $this->genlib->setPaginationConfig($totalSuppliers, "suppliers/lasp_", $limit, ['class'=>'lnp']);
        
        $this->pagination->initialize($config);//initialize the library class
        
        //get all customers from db
        $data['allSuppliers'] = $this->supplier->getAll($orderBy, $orderFormat, $start, $limit);
        $data['range'] = $totalSuppliers > 0 ? ($start+1) . "-" . ($start + count($data['allSuppliers'])) . " of " . $totalSuppliers : "";
        $data['links'] = $this->pagination->create_links();//page links
        $data['sn'] = $start+1;
        
        $json['supplierTable'] = $this->load->view('supplier/supplierlist', $data, TRUE);//get view with populated customers table

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
     * To add new supplier
     */
    public function add(){
        $this->genlib->ajaxOnly();
        
        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('', '');
        
        $this->form_validation->set_rules('firstName', 'First name', ['required', 'trim', 'max_length[20]', 'strtolower', 'ucfirst'], ['required'=>"required"]);
        $this->form_validation->set_rules('lastName', 'Last name', ['required', 'trim', 'max_length[20]', 'strtolower', 'ucfirst'], ['required'=>"required"]);
        $this->form_validation->set_rules('email', 'E-mail', ['trim', 'required', 'valid_email', 'is_unique[supplier.email]', 'strtolower'],
                ['required'=>"required", 'is_unique'=>'E-mail exists']);
        $this->form_validation->set_rules('role', 'Role', ['required'], ['required'=>"required"]);
        $this->form_validation->set_rules('mobile1', 'Phone number', ['required', 'trim', 'numeric', 'max_length[15]', 'min_length[11]', 'is_unique[supplier.mobile1]'],
                ['required'=>"required", 'is_unique'=>"This number is already attached to an supplier"]);
        $this->form_validation->set_rules('mobile2', 'Other number', ['trim', 'numeric', 'max_length[15]', 'min_length[11]']);
        $this->form_validation->set_rules('passwordOrig', 'Password', ['required', 'min_length[8]'], ['required'=>"Enter password"]);
        $this->form_validation->set_rules('passwordDup', 'Password Confirmation', ['required', 'matches[passwordOrig]'], ['required'=>"Please retype password"]);
        
        if($this->form_validation->run() !== FALSE){
            /**
             * insert info into db
             * function header: add($f_name, $l_name, $email, $password, $role, $mobile1, $mobile2)
             */
            $hashedPassword = password_hash(set_value('passwordOrig'), PASSWORD_BCRYPT);
            
            $inserted = $this->supplier->add(set_value('firstName'), set_value('lastName'), set_value('email'), $hashedPassword,
                set_value('role'), set_value('mobile1'), set_value('mobile2'));
            
            
            $json = $inserted ? 
                ['status'=>1, 'msg'=>"Supplier successfully created"]
                : 
                ['status'=>0, 'msg'=>"Oops! Unexpected server error! Pls contact administrator for help. Sorry for the embarrassment"];
        }
        
        else{
            //return all error messages
            $json = $this->form_validation->error_array();//get an array of all errors
            
            $json['msg'] = "One or more required fields are empty or not correctly filled";
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
     * 
     */
    public function update(){
        $this->genlib->ajaxOnly();
        
        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('', '');
        
        $this->form_validation->set_rules('firstName', 'First name', ['required', 'trim', 'max_length[20]'], ['required'=>"required"]);
        $this->form_validation->set_rules('lastName', 'Last name', ['required', 'trim', 'max_length[20]'], ['required'=>"required"]);
        $this->form_validation->set_rules('mobile1', 'Phone number', ['required', 'trim', 'numeric', 'max_length[15]', 
            'min_length[11]', 'callback_crosscheckMobile['. $this->input->post('supplierId', TRUE).']'], ['required'=>"required"]);
        $this->form_validation->set_rules('mobile2', 'Other number', ['trim', 'numeric', 'max_length[15]', 'min_length[11]']);
        $this->form_validation->set_rules('email', 'E-mail', ['required', 'trim', 'valid_email', 'callback_crosscheckEmail['. $this->input->post('supplierId', TRUE).']']);
        $this->form_validation->set_rules('role', 'Role', ['required', 'trim'], ['required'=>"required"]);
        
        if($this->form_validation->run() !== FALSE){
            /**
             * update info in db
             * function header: update($supplier_id, $first_name, $last_name, $email, $mobile1, $mobile2, $role)
             */
				
            $supplier_id = $this->input->post('supplierId', TRUE);

            $updated = $this->supplier->update($supplier_id, set_value('firstName'), set_value('lastName'), set_value('email'),
                    set_value('mobile1'), set_value('mobile2'), set_value('role'));
            
            
            $json = $updated ? 
                    ['status'=>1, 'msg'=>"Supplier info successfully updated"]
                    : 
                    ['status'=>0, 'msg'=>"Oops! Unexpected server error! Pls contact administrator for help. Sorry for the embarrassment"];
        }
        
        else{
            //return all error messages
            $json = $this->form_validation->error_array();//get an array of all errors
            
            $json['msg'] = "One or more required fields are empty or not correctly filled";
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
    
    
    public function suspend(){
        $this->genlib->ajaxOnly();
        
        $supplier_id = $this->input->post('_aId');
        $new_status = $this->genmod->gettablecol('supplier', 'account_status', 'id', $supplier_id) == 1 ? 0 : 1;
        
        $done = $this->supplier->suspend($supplier_id, $new_status);
        
        $json['status'] = $done ? 1 : 0;
        $json['_ns'] = $new_status;
        $json['_aId'] = $supplier_id;
        
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }
    
    
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    public function delete(){
        $this->genlib->ajaxOnly();
        
        $supplier_id = $this->input->post('_aId');
        $new_value = $this->genmod->gettablecol('supplier', 'deleted', 'id', $supplier_id) == 1 ? 0 : 1;
        
        $done = $this->supplier->delete($supplier_id, $new_value);
        
        $json['status'] = $done ? 1 : 0;
        $json['_nv'] = $new_value;
        $json['_aId'] = $supplier_id;
        
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
     * Used as a callback while updating supplier info to ensure 'mobile1' field does not contain a number already used by another supplier
     * @param type $mobile_number
     * @param type $supplier_id
     */
    public function crosscheckMobile($mobile_number, $supplier_id){
        //check db to ensure number was previously used for supplier with $supplier_id i.e. the same supplier we're updating his details
        $supplierWithNum = $this->genmod->getTableCol('supplier', 'id', 'mobile1', $mobile_number);
        
        if($supplierWithNum == $supplier_id){
            //used for same supplier. All is well.
            return TRUE;
        }
        
        else{
            $this->form_validation->set_message('crosscheckMobile', 'This number is already attached to an Supplier');
                
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
     * Used as a callback while updating supplier info to ensure 'email' field does not contain an email already used by another supplier
     * @param type $email
     * @param type $supplier_id
     */
    public function crosscheckEmail($email, $supplier_id){
        //check db to ensure email was previously used for suppliert with supplier_id i.e. the same supplier we're updating his details
        $supplierWithEmail = $this->genmod->getTableCol('supplier', 'id', 'email', $email);
        
        if($supplierWithEmail == $supplier_id){
            //used for same supplier. All is well.
            return TRUE;
        }
        
        else{
            $this->form_validation->set_message('crosscheckEmail', 'This email is already attached to an administrator');
                
            return FALSE;
        }
    }
    
}
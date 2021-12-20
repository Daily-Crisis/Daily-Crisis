<?php
defined('BASEPATH') OR exit('');

/**
 * Description of Processes
 *
 */
class Processes extends CI_Controller{

    public function __construct(){
        parent::__construct();

        $this->genlib->checkLogin();

        $this->load->model(['process']);
    }

    /**
     *
     */
    public function index(){
        $data['pageContent'] = $this->load->view('processes/processes', '', TRUE);
        $data['pageTitle'] = "Processes";

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
     * "lapr" = "load Processes List Table"
     */
    public function lapr(){
        $this->genlib->ajaxOnly();

        $this->load->helper('text');

        //set the sort order
        $orderBy = $this->input->get('orderBy', TRUE) ? $this->input->get('orderBy', TRUE) : "name";
        $orderFormat = $this->input->get('orderFormat', TRUE) ? $this->input->get('orderFormat', TRUE) : "ASC";

        //count the total number of processes in db
        $totalProcesses = $this->db->count_all('processes');

        $this->load->library('pagination');

        $pageNumber = $this->uri->segment(3, 0);//set page number to zero if the page number is not set in the third segment of uri

        $limit = $this->input->get('limit', TRUE) ? $this->input->get('limit', TRUE) : 10;//show $limit per page
        $start = $pageNumber == 0 ? 0 : ($pageNumber - 1) * $limit;//start from 0 if pageNumber is 0, else start from the next iteration

        //call setPaginationConfig($totalRows, $urlToCall, $limit, $attributes) in genlib to configure pagination
        $config = $this->genlib->setPaginationConfig($totalProcesses, "processes/lapr", $limit, ['onclick'=>'return lapr(this.href);']);

        $this->pagination->initialize($config);//initialize the library class

        //get all processes from db
        $data['allProcesses'] = $this->process->getAll($orderBy, $orderFormat, $start, $limit);
        $data['range'] = $totalProcesses > 0 ? "Mostrando " . ($start+1) . "-" . ($start + count($data['allProcesses'])) . " de " . $totalProcesses : "";
        $data['links'] = $this->pagination->create_links();//page links
        $data['sn'] = $start+1;
        $data['cum_total'] = $this->process->getProcessesCumTotal();

        $json['processesListTable'] = $this->load->view('processes/processeslisttable', $data, TRUE);//get view with populated processes table

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */



    public function add(){
        $this->genlib->ajaxOnly();

        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('', '');

        $this->form_validation->set_rules('processName', 'Process name', ['required', 'trim', 'max_length[80]', 'is_unique[processes.name]'],
            ['required'=>"required"]);
        $this->form_validation->set_rules('processQuantity', 'Process quantity', ['required', 'trim', 'numeric'], ['required'=>"required"]);
        $this->form_validation->set_rules('processPrice', 'Process Price', ['required', 'trim', 'numeric'], ['required'=>"required"]);
        $this->form_validation->set_rules('processCode', 'Process Code', ['required', 'trim', 'max_length[20]', 'is_unique[processes.code]'],
            ['required'=>"required", 'is_unique'=>"There is already an process with this code"]);

        if($this->form_validation->run() !== FALSE){
            $this->db->trans_start();//start transaction

            /**
             * insert info into db
             * function header: add($processName, $processQuantity, $processPrice, $processDescription, $processCode)
             */
            $insertedId = $this->process->add(set_value('processName'), set_value('processQuantity'), set_value('processPrice'),
                set_value('processDescription'), set_value('processCode'));

            $processName = set_value('processName');
            $processQty = set_value('processQuantity');
            $processPrice = "&#8369;".number_format(set_value('processPrice'), 2);

            //insert into eventlog
            //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
            $desc = "Addition of {$processQty} quantities of a new process '{$processName}' with a unit price of {$processPrice} to stock";

            $insertedId ? $this->genmod->addevent("Creation of new process", $insertedId, $desc, "processes", $this->session->admin_id) : "";

            $this->db->trans_complete();

            $json = $this->db->trans_status() !== FALSE ?
                ['status'=>1, 'msg'=>"Process successfully added"]
                :
                ['status'=>0, 'msg'=>"Oops! Unexpected server error! Please contact administrator for help. Sorry for the embarrassment"];
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
     * Primarily used to check whether an process already has a particular random code being generated for a new process
     * @param type $selColName
     * @param type $whereColName
     * @param type $colValue
     */
    public function gettablecol($selColName, $whereColName, $colValue){
        $a = $this->genmod->gettablecol('processes', $selColName, $whereColName, $colValue);

        $json['status'] = $a ? 1 : 0;
        $json['colVal'] = $a;

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
    public function gcoandqty(){
        $json['status'] = 0;

        $processCode = $this->input->get('_iC', TRUE);

        if($processCode){
            $process_info = $this->process->getProcessInfo(['code'=>$processCode], ['quantity', 'unitPrice', 'description']);

            if($process_info){
                $json['availQty'] = (int)$process_info->quantity;
                $json['unitPrice'] = $process_info->unitPrice;
                $json['description'] = $process_info->description;
                $json['status'] = 1;
            }
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


    public function updatestock(){
        $this->genlib->ajaxOnly();

        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('', '');

        $this->form_validation->set_rules('_iId', 'Process ID', ['required', 'trim', 'numeric'], ['required'=>"required"]);
        $this->form_validation->set_rules('_upType', 'Update type', ['required', 'trim', 'in_list[newStock,deficit]'], ['required'=>"required"]);
        $this->form_validation->set_rules('qty', 'Quantity', ['required', 'trim', 'numeric'], ['required'=>"required"]);
        $this->form_validation->set_rules('desc', 'Update Description', ['required', 'trim'], ['required'=>"required"]);

        if($this->form_validation->run() !== FALSE){
            //update stock based on the update type
            $updateType = set_value('_upType');
            $processId = set_value('_iId');
            $qty = set_value('qty');
            $desc = set_value('desc');

            $this->db->trans_start();

            $updated = $updateType === "deficit"
                ?
                $this->process->deficit($processId, $qty, $desc)
                :
                $this->process->newstock($processId, $qty, $desc);

            //add event to log if successful
            $stockUpdateType = $updateType === "deficit" ? "Deficit" : "New Stock";

            $event = "Stock Update ($stockUpdateType)";

            $action = $updateType === "deficit" ? "removed from" : "added to";//action that happened

            $eventDesc = "<p>{$qty} quantities of {$this->genmod->gettablecol('processes', 'name', 'id', $processId)} was {$action} stock</p>
                Reason: <p>{$desc}</p>";

            //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
            $updated ? $this->genmod->addevent($event, $processId, $eventDesc, "processes", $this->session->admin_id) : "";

            $this->db->trans_complete();//end transaction

            $json['status'] = $this->db->trans_status() !== FALSE ? 1 : 0;
            $json['msg'] = $updated ? "Stock successfully updated" : "Unable to update stock at this time. Please try again later";
        }

        else{
            $json['status'] = 0;
            $json['msg'] = "One or more required fields are empty or not correctly filled";
            $json = $this->form_validation->error_array();
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

    public function edit(){
        $this->genlib->ajaxOnly();

        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('', '');

        $this->form_validation->set_rules('_iId', 'Process ID', ['required', 'trim', 'numeric']);
        $this->form_validation->set_rules('processName', 'Process Name', ['required', 'trim',
            'callback_crosscheckName['.$this->input->post('_iId', TRUE).']'], ['required'=>'required']);
        $this->form_validation->set_rules('processCode', 'Process Code', ['required', 'trim',
            'callback_crosscheckCode['.$this->input->post('_iId', TRUE).']'], ['required'=>'required']);
        $this->form_validation->set_rules('processPrice', 'Process Unit Price', ['required', 'trim', 'numeric']);
        $this->form_validation->set_rules('processDesc', 'Process Description', ['trim']);

        if($this->form_validation->run() !== FALSE){
            $processId = set_value('_iId');
            $processDesc = set_value('processDesc');
            $processPrice = set_value('processPrice');
            $processName = set_value('processName');
            $processCode = $this->input->post('processCode', TRUE);

            //update process in db
            $updated = $this->process->edit($processId, $processName, $processDesc, $processPrice);

            $json['status'] = $updated ? 1 : 0;

            //add event to log
            //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
            $desc = "Details of process with code '$processCode' was updated";

            $this->genmod->addevent("Process Update", $processId, $desc, 'processes', $this->session->admin_id);
        }

        else{
            $json['status'] = 0;
            $json = $this->form_validation->error_array();
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

    public function crosscheckName($processName, $processId){
        //check db to ensure name was previously used for the process we are updating
        $processWithName = $this->genmod->getTableCol('processes', 'id', 'name', $processName);

        //if process name does not exist or it exist but it's the name of current process
        if(!$processWithName || ($processWithName == $processId)){
            return TRUE;
        }

        else{//if it exist
            $this->form_validation->set_message('crosscheckName', 'There is an process with this name');

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
     * @param type $process_code
     * @param type $process_id
     * @return boolean
     */
    public function crosscheckCode($process_code, $process_id){
        //check db to ensure process code was previously used for the process we are updating
        $process_with_code = $this->genmod->getTableCol('processes', 'id', 'code', $process_code);

        //if process code does not exist or it exist but it's the code of current process
        if(!$process_with_code || ($process_with_code == $process_id)){
            return TRUE;
        }

        else{//if it exist
            $this->form_validation->set_message('crosscheckCode', 'There is an process with this code');

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


    public function delete(){
        $this->genlib->ajaxOnly();

        $json['status'] = 0;
        $process_id = $this->input->post('i', TRUE);

        if($process_id){
            $this->db->where('id', $process_id)->delete('processes');

            $json['status'] = 1;
        }

        //set final output
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }
}
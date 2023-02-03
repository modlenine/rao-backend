<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

    
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Bangkok");

        $this->load->model("api_model" , "api");
    }
    

    public function index()
    {
        echo "test";
    }

    public function testcode()
    {
        $deptcode = '1002';
        print_r (getManagerEmail($deptcode)->result_array());
    }

    public function checklogin()
    {
        $this->api->checklogin();
    }

    public function getAccidentType()
    {
        $this->api->getAccidentType();
    }

    public function getEmployeeData()
    {
        $this->api->getEmployeeData();
    }

    public function api_saveDataMain()
    {
        $this->api->api_saveDataMain();
    }

    public function api_saveDataMainEdit()
    {
        $this->api->api_saveDataMainEdit();
    }

    public function api_loadAcciList($startDate , $endDate , $acciType , $userInform , $userType , $status)
    {
        $this->api->api_loadAcciList($startDate , $endDate , $acciType , $userInform , $userType , $status);
    }

    public function api_getFilterAcciType()
    {
        $this->api->api_getFilterAcciType();
    }

    public function api_getFilterUserInform()
    {
        $this->api->api_getFilterUserInform();
    }

    public function api_getFilterUserType()
    {
        $this->api->api_getFilterUserType();
    }

    public function api_getViewData()
    {
        $this->api->api_getViewData();
    }

    public function api_deleteFile()
    {
        $this->api->api_deleteFile();
    }

    public function api_canCelDoc()
    {
        $this->api->api_canCelDoc();
    }

    public function api_sendData()
    {
        $this->api->api_sendData();
    }

    public function api_saveMgr()
    {
        $this->api->api_saveMgr();
    }

    public function api_checkMgrData()
    {
        $this->api->api_checkMgrData();
    }

    public function api_checkOhsData()
    {
        $this->api->api_checkOhsData();
    }

    public function api_saveOhs()
    {
        $this->api->api_saveOhs();
    }

    public function api_forcomplaint($formno)
    {
        $this->api->api_forcomplaint($formno);
    }


    public function api_printDocument($formno)
    {
        if($formno != ""){
            require_once('TCPDF/tcpdf.php');

            $data = array(
                "dataMain" => getViewFullData($formno),
                "dataFile" => getFile($formno)
            );

            $this->load->view("printDocument" , $data);
        }
    }


    public function api_getGraph1()
    {
        $this->api->api_getGraph1();
    }




}/* End of file Controllername.php */

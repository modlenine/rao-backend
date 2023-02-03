<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Api_mobile_model extends CI_Model {

public function __construct()
{
    parent::__construct();
    //Do your magic here
    date_default_timezone_set("Asia/Bangkok");
}


public function loadOnsitelist()
{
    $sql=$this->db->query("SELECT * FROM onsitelist LIMIT 100");
    $output = array(
        "msg" => "ดึงข้อมูลสำเร็จ",
        "status" => "Select Data Success",
        "result" => $sql->result()
    );

    echo json_encode($output);
}

    

}

/* End of file ModelName.php */




?>
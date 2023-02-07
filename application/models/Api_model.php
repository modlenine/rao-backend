<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Api_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->db2 = $this->load->database('saleecolour', TRUE);
        date_default_timezone_set("Asia/Bangkok");
        $this->load->model("email_model");
    }


    public function calltest()
    {
        echo "test call";
    }

    public function testcode()
    {
        echo "test call";
    }

    public function escape_string()
    {
        return mysqli_connect("localhost", "ant", "Ant1234", "saleecolour");
    }

    public function checklogin()
    {
        $username = "";
        $password = "";

        if ($this->input->post("username") != "" && $this->input->post("password") != "") {
            $username = $this->input->post("username");
            $password = $this->input->post("password");

            $user = mysqli_real_escape_string($this->escape_string(), $username);
            $pass = mysqli_real_escape_string($this->escape_string(), md5($password));

            $sql = $this->db2->query(sprintf("SELECT * FROM member WHERE username='%s' AND password='%s' ", $user, $pass));

            if ($sql->num_rows() == 0) {

                $output = array(
                    "msg" => "ไม่พบข้อมูลผู้ใช้งานในระบบ",
                    "status" => "Login failed"
                );
            } else {
                
                foreach ($sql->result_array() as $r) {
                    $_SESSION['username'] = $r['username'];
                    $_SESSION['password'] = $r['password'];
                    $_SESSION['Fname'] = $r['Fname'];
                    $_SESSION['Lname'] = $r['Lname'];
                    $_SESSION['Dept'] = $r['Dept'];
                    $_SESSION['ecode'] = $r['ecode'];
                    $_SESSION['DeptCode'] = $r['DeptCode'];
                    $_SESSION['memberemail'] = $r['memberemail'];
                    $_SESSION['file_img'] = $r['file_img'];
                    $_SESSION['posi'] = $r['posi'];

                }

                $uri = isset($_SESSION['RedirectKe']) ? $_SESSION['RedirectKe'] : '/intsys/rao/';
                // header('location:' . $uri);

                // Check IT
                $output = array(
                    "msg" => "ลงชื่อเข้าใช้สำเร็จ",
                    "status" => "Login Successfully",
                    "uri" => $uri,
                    "session_data" => $sql->row(),
                    "dateExpire" => strtotime(date("Y-m-d H:i:s")."+7 hours"),
                );

            }
            
        }else{
            $output = array(
                "msg" => "กรุณากรอก Username & Password",
                "status" => "Login failed please fill username and password"
            );
        }

        echo json_encode($output);
    }


    public function getAccidentType()
    {
        $received_data = json_decode(file_get_contents("php://input"));
        if($received_data->action == "getAccidentType"){
            $sql = $this->db->query("SELECT * FROM acci_type ORDER BY type_name ASC");
            $output = array(
                "msg" => "ดึงข้อมูลประเภทอุบัติเหตุสำเร็จ",
                "status" => "Select Data Success",
                "result" => $sql->result()
            );

        }else{
            $output = array(
                "msg" => "ดึงข้อมูลประเภทอุบัติเหตุไม่สำเร็จ",
                "status" => "Select Data Not Success",
            );
        }

        echo json_encode($output);

    }

    public function getEmployeeData()
    {
        $received_data = json_decode(file_get_contents("php://input"));
        if($received_data->action == "getEmployeeData"){
            $searchText = $received_data->inputSearch;

            $idArr = explode(" ",$searchText);
            $context = " CONCAT(a.FnameT,' ',
            a.LnameT,' ',
            a.FnameE,' ',
            a.LnameE,' ',
            a.emcode)";

            $condition = " $context LIKE '%" . implode("%' OR $context LIKE '%" , $idArr) . "%' ";



            $sql = $this->db2->query("SELECT
            a.FnameT,
            a.LnameT,
            a.FnameE,
            a.LnameE,
            a.emcode,
            a.PositionName,
            a.DeptCode
            FROM employee a
            WHERE $condition
            ORDER BY a.emcode ASC
            LIMIT 50
            ");

            $output = array(
                "msg" => "ดึงข้อมูลรายชื่อพนักงานสำเร็จ",
                "status" => "Select Data Success",
                "result" => $sql->result()
            );
        }else{
            $output = array(
                "msg" => "ดึงข้อมูลรายชื่อพนักงานไม่สำเร็จ",
                "status" => "Select Data Not Success",
            );
        }

        echo json_encode($output);
    }


    public function api_saveDataMain()
    {
        if($this->input->post("ip-acci_datetime") != ""){
            $getFormno = getFormNo();

            if($_FILES["ip-file_name"]["name"] != ""){
                $fileStatus = "yes";
            }else{
                $fileStatus = "no";
            }

            $arInsertData = array(
                "m_formno" => $getFormno,
                "m_acci_datetime" => $this->input->post("ip-acci_datetime"),
                "m_acci_location" => $this->input->post("ip-acci_location"),
                "m_type1" => $this->input->post("ip-type1"),
                "m_type2" => $this->input->post("ip-type2"),
                "m_type3" => $this->input->post("ip-type3"),
                "m_acci_name" => $this->input->post("ip-acci_name"),
                "m_acci_ecode" => $this->input->post("ip-acci_ecode"),
                "m_acci_dept" => $this->input->post("ip-acci_dept"),
                "m_acci_deptcode" => $this->input->post("ip-acci_deptcode"),
                "m_acci_cardno" => $this->input->post("ip-acci_cardno"),
                "m_acci_carno" => $this->input->post("ip-acci_carno"),
                "m_acci_res" => $this->input->post("ip-acci_res"),
                "m_acci_detail" => $this->input->post("ip-acci_detail"),
                "m_acci_filestatus" => $fileStatus,
                "m_user_inform" => $this->input->post("ip-user_inform"),
                "m_ecode_inform" => $this->input->post("ip-ecode_inform"),
                "m_dept_inform" => $this->input->post("ip-dept_inform"),
                "m_deptcode_inform" => $this->input->post("ip-deptcode_inform"),
                "m_datetime_inform" => date("Y-m-d H:i:s"),
                "m_status" => "Waiting Send Data"
            );

            $this->db->insert("acci_main" , $arInsertData);

            $filename = "ip-file_name";
            $formno = $getFormno;
            $userInform = $this->input->post("ip-user_inform");
            $ecodeInform = $this->input->post("ip-ecode_inform");
            uploadImage($filename , $formno , $userInform , $ecodeInform);

            $output = array(
                "msg" => "บันทึกข้อมูลสำเร็จ",
                "status" => "Insert Data Success",
                "formno" => $getFormno
            );
        }else{
            $output = array(
                "msg" => "บันทึกข้อมูลไม่สำเร็จ",
                "status" => "Insert Data Not Success"
            );
        }

        echo json_encode($output);
    }

    public function api_saveDataMainEdit()
    {
        if($this->input->post("ipe-acci_datetime") != ""){
            $getFormno = $this->input->post("formno-edit");

            if($_FILES["ipe-file_name"]["name"] != ""){
                $fileStatus = "yes";
            }else{
                $fileStatus = "no";
            }

            $arUpdateData = array(
                "m_acci_datetime" => $this->input->post("ipe-acci_datetime"),
                "m_acci_location" => $this->input->post("ipe-acci_location"),
                "m_type1" => $this->input->post("ipe-type1"),
                "m_type2" => $this->input->post("ipe-type2"),
                "m_type3" => $this->input->post("ipe-type3"),
                "m_acci_name" => $this->input->post("ipe-acci_name"),
                "m_acci_ecode" => $this->input->post("ipe-acci_ecode"),
                "m_acci_dept" => $this->input->post("ipe-acci_dept"),
                "m_acci_deptcode" => $this->input->post("ipe-acci_deptcode"),
                "m_acci_cardno" => $this->input->post("ipe-acci_cardno"),
                "m_acci_carno" => $this->input->post("ipe-acci_carno"),
                "m_acci_res" => $this->input->post("ipe-acci_res"),
                "m_acci_detail" => $this->input->post("ipe-acci_detail"),
                "m_acci_filestatus" => $fileStatus,
                "m_user_modify" => $this->input->post("ipe-user_inform"),
                "m_ecode_modify" => $this->input->post("ipe-ecode_inform"),
                "m_dept_modify" => $this->input->post("ipe-dept_inform"),
                "m_deptcode_modify" => $this->input->post("ipe-deptcode_inform"),
                "m_datetime_modify" => date("Y-m-d H:i:s"),
                "m_status" => "Waiting Send Data"
            );

            $this->db->where("m_formno" , $getFormno);
            $this->db->update("acci_main" , $arUpdateData);

            $filename = "ipe-file_name";
            $formno = $getFormno;
            $userInform = $this->input->post("ipe-user_inform");
            $ecodeInform = $this->input->post("ipe-ecode_inform");
            uploadImage($filename , $formno , $userInform , $ecodeInform);

            $output = array(
                "msg" => "บันทึกข้อมูลสำเร็จ",
                "status" => "Update Data Success",
                "formno" => $getFormno
            );
        }else{
            $output = array(
                "msg" => "บันทึกข้อมูลไม่สำเร็จ",
                "status" => "Update Data Not Success"
            );
        }

        echo json_encode($output);
    }


    public function api_loadAcciList($startDate , $endDate , $acciType , $userInform , $userType , $status)
    {
         // DB table to use
         $table = 'datalist';
 
         // Table's primary key
         $primaryKey = 'm_autoid';
 
         $columns = array(
             array(
                 'db' => 'm_formno', 'dt' => 0,
                 'formatter' => function ($d, $row) {
                     if(getStatus($d)->m_status == "Waiting Send Data"){
                         $newstatus = '<img src="'.base_url('assets/images/new.gif').'">';
                     }else{
                         $newstatus = '';
                     }
                     return '<b><a href="javascript:void(0)" class="acci_formno" data_formno="'.$d.'"><b>' . $d . '</b></a>&nbsp;'.$newstatus.'</b>'; //or any other format you require
                 }
             ),
             array('db' => 'm_acci_datetime', 'dt' => 1 ,
                'formatter' => function($d , $row){
                    return conDateTimeFromDb($d);
                }
            ),
             array('db' => 'm_acci_location', 'dt' => 2),
             array('db' => 'm_type3', 'dt' => 3),
             array('db' => 'm_acci_name', 'dt' => 4),
             array('db' => 'm_type1', 'dt' => 5),
             array('db' => 'm_user_inform', 'dt' => 6),
             array('db' => 'm_status', 'dt' => 7 , 
                'formatter' => function($d , $row){
                    $color = '';
                    if($d == "Open"){
                        $color = 'color:#00CCFF;';
                    }else if($d == "Manager Approved"){
                        $color = 'color:#009900;';
                    }else if($d == "Manager Not Approve"){
                        $color = 'color:#CC0000;';
                    }else if($d == "OHS Approved"){
                        $color = 'color:#009900;';
                    }else if($d == "OHS Not Approve"){
                        $color = 'color:#CC0000;';
                    }else if($d == "OHS Reject"){
                        $color = 'color:#FF9900;';
                    }else if($d == "User Cancel"){
                        $color = 'color:#CC0000;';
                    }else if($d == "Complete"){
                        $color = 'color:#0000CC;';
                    }
                    $output = '<span style="'.$color.'"><b>'.$d.'</b></span>';

                    return $output;
                }
            ),
         );
 
         // SQL server connection information
         $sql_details = array(
             'user' => getDb()->db_username,
             'pass' => getDb()->db_password,
             'db'   => getDb()->db_databasename,
             'host' => getDb()->db_host
         );
 
         /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
                * If you just want to use the basic configuration for DataTables with PHP
                * server-side, there is no need to edit below this line.
                */
         // $path = $_SERVER['DOCUMENT_ROOT']."/intsys/oss/server-side/scripts/ssp.class.php";
         require('server-side/scripts/ssp.class.php');

         $sql_searchBydate = "";
        
         if($startDate == "0" && $endDate == "0"){
             $sql_searchBydate = "m_acci_datetime LIKE '%%' ";
         }else if($startDate == "0" && $endDate != "0"){
             $sql_searchBydate = "m_acci_datetime BETWEEN '$endDate 00:00:01' AND '$endDate 23:59:59' ";
         }else if($startDate != "0" && $endDate != "0"){
             $sql_searchBydate = "m_acci_datetime BETWEEN '$startDate 00:00:01' AND '$endDate 23:59:59' ";
         }else if($startDate != "0" && $endDate == "0"){
             $sql_searchBydate = "m_acci_datetime BETWEEN '$startDate 00:00:01' AND '$startDate 23:59:59' ";
         }

         $query_Type = "";
         $conQuery_Type = "";
         if($acciType == "0"){
             $query_Type = "";
         }else{
            $conQuery_Type = getAcciType($acciType)->type_name;
            $query_Type = "AND m_type3 = '$conQuery_Type' ";
         }
 
         $query_userInform = "";
         if($userInform == "0"){
             $query_userInform = "";
         }else{
             $query_userInform = "AND m_ecode_inform = '$userInform' ";
         }
 
         $query_userType = "";
         if($userType == "0"){
             $query_userType = "";
         }else if($userType == "1"){
             $query_userType = "AND m_type1 = 'บุคคลภายนอกบริษัท' ";
         }else if($userType == "2"){
            $query_userType = "AND m_type1 = 'บุคคลภายในบริษัท' ";
         }


         $query_status = "";
         $con_status = "";
         if($status == "0"){
             $query_status = "";
         }else{
            $con_status = str_replace("-" , " ",$status);
             $query_status = "AND m_status = '$con_status' ";
         }
         

        echo json_encode(
            SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, "$sql_searchBydate $query_Type $query_userInform $query_userType $query_status")
        );
 
      
        
        //  echo json_encode(
        //      SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns)
        //  );
    }

    public function api_getFilterAcciType()
    {
        $received_data = json_decode(file_get_contents("php://input"));

        if($received_data->action == "getFilterAcciType"){
            $sql = $this->db->query("SELECT type_autoid , type_name , type_description FROM acci_type ORDER BY type_name ASC");

            $output = array(
                "msg" => "ดึงข้อมูลประเภทของอุบัติเหตุสำเร็จ",
                "status" => "Select Data Success",
                "result" => $sql->result()
            );
        }else{
            $output = array(
                "msg" => "ดึงข้อมูลประเภทของอุบัติเหตุไม่สำเร็จ",
                "status" => "Select Data Not Success",
            );
        }

        echo json_encode($output);
    }

    public function api_getFilterUserInform()
    {
        $sql = $this->db->query("SELECT m_user_inform , m_ecode_inform FROM acci_main GROUP BY m_ecode_inform ORDER BY m_user_inform ASC");
        $output = array(
            "msg" => "ดึงรายชื่อผู้แจ้งเหตุสำเร็จ",
            "status" => "Select Data Success",
            "result" => $sql->result()
        );

        echo json_encode($output);
    }

    public function api_getFilterUserType()
    {
        $sql = $this->db->query("SELECT m_type1 FROM acci_main GROUP BY m_type1");
        $output = array(
            "msg" => "ดึงข้อมูลสถานะบุคคลสำเร็จ",
            "status" => "Select Data Success",
            "result" => $sql->result()
        );

        echo json_encode($output);
    }


    public function api_getViewData()
    {
        $received_data = json_decode(file_get_contents("php://input"));

        if($received_data->action == "getViewData"){
            $m_formno = $received_data->formno;
            $sql = $this->db->query("SELECT
            acci_main.m_autoid,
            acci_main.m_formno,
            acci_main.m_acci_datetime,
            acci_main.m_acci_location,
            acci_main.m_type1,
            acci_main.m_type2,
            acci_main.m_type3,
            acci_main.m_acci_name,
            acci_main.m_acci_ecode,
            acci_main.m_acci_dept,
            acci_main.m_acci_deptcode,
            acci_main.m_acci_cardno,
            acci_main.m_acci_carno,
            acci_main.m_acci_res,
            acci_main.m_acci_detail,
            acci_main.m_acci_filestatus,
            acci_main.m_user_inform,
            acci_main.m_ecode_inform,
            acci_main.m_dept_inform,
            acci_main.m_deptcode_inform,
            acci_main.m_datetime_inform,
            acci_main.m_status,
            acci_main.m_user_modify,
            acci_main.m_ecode_modify,
            acci_main.m_dept_modify,
            acci_main.m_deptcode_modify
            FROM
            acci_main
            WHERE m_formno = '$m_formno'
            ");

            $result_file = $this->getFiles($m_formno);

            $output = array(
                "msg" => "ดึงข้อมูลเอกสารเลขที่ $m_formno สำเร็จ",
                "status" => "Select Data Success",
                "result" => $sql->row(),
                "result_file" => $result_file->result()
            );
        }else{
            $output = array(
                "msg" => "ดึงข้อมูลไม่สำเร็จ",
                "status" => "Select Data Not Success"
            );
        }
        echo json_encode($output);
    }

    private function getFiles($formno)
    {
        if($formno != ""){
            $sql = $this->db->query("SELECT * FROM acci_files WHERE file_m_formno = '$formno' ORDER BY file_autoid ASC ");
            return $sql;
        }
    }


    public function api_deleteFile()
    {
        $received_data = json_decode(file_get_contents("php://input"));
        if($received_data->action == "deleteFileEdit"){
            $file_autoid = $received_data->file_autoid;
            $file_path = $received_data->file_path;
            $file_name = $received_data->file_name;
            $file_formno = $received_data->file_formno;

            $this->db->where("file_autoid" , $file_autoid);
            $this->db->delete("acci_files");

            $path = $_SERVER['DOCUMENT_ROOT']."/intsys/rao/rao_backend/$file_path/$file_name";
            unlink($path);

            $sql = $this->db->query("SELECT * FROM acci_files WHERE file_m_formno = '$file_formno' ");

            $output = array(
                "msg" => "ลบไฟล์สำเร็จ",
                "status" => "Delete Data Success",
                "result" => $sql->result()
            );
        }else{
            $output = array(
                "msg" => "ลบไฟล์ไม่สำเร็จ",
                "status" => "Delete Data Not Success"
            );
        }

        echo json_encode($output);
    }

    public function api_canCelDoc()
    {
        $received_data = json_decode(file_get_contents("php://input"));
        if($received_data->action == "api_canCelDoc"){
            $formno = $received_data->formno;

            $arCancelDoc = array(
                "m_status" => "User Cancel",
                "m_datetime_modify" => date("Y-m-d H:i:s")
            );

            $this->db->where("m_formno" , $formno);
            $this->db->update("acci_main" , $arCancelDoc);

            $output = array(
                "msg" => "ยกเลิกเอกสารสำเร็จ",
                "status" => "Cancel Document Success"
            );
        }else{
            $output = array(
                "msg" => "ยกเลิกเอกสารไม่สำเร็จ",
                "status" => "Cancel Document Not Success"
            );
        }

        echo json_encode($output);
    }


    public function api_sendData()
    {
        $received_data = json_decode(file_get_contents("php://input"));
        if($received_data->action == "api_sendData"){
            $formno = $received_data->formno;

            // Update Status
            $arupDate = array(
                "m_status" => "Open",
                "m_datetime_modify" => date("Y-m-d H:i:s")
            );

            $this->db->where("m_formno" , $formno);
            $this->db->update("acci_main" , $arupDate);

            // Get Data For Send Email 
            $this->email_model->sendEmail_toMgr($formno);

            $output = array(
                "msg" => "ส่งข้อมูลสำเร็จ",
                "status" => "Send Data Success"
            );
        }else{
            $output = array(
                "msg" => "ส่งข้อมูลไม่สำเร็จ",
                "status" => "Send Data Not Success"
            );
        }
        echo json_encode($output);
    }


    public function api_saveMgr()
    {
        if($this->input->post("ip-acci-mgrappro-formno") != ""){
            $formno = $this->input->post("ip-acci-mgrappro-formno");
            if($this->input->post("ip-acci-mgrappro") == "อนุมัติ"){
                $mgrApprove = "Manager Approved";
            }else if($this->input->post("ip-acci-mgrappro") == "ไม่อนุมัติ"){
                $mgrApprove = "Manager Not Approve";
            }
            $arMgrUpdate = array(
                "m_mgr_approve" => $this->input->post("ip-acci-mgrappro"),
                "m_mgr_memo" => $this->input->post("ip-acci-mgrappro-memo"),
                "m_mgr_user" => $this->input->post("ip-acci-mgrappro-user"),
                "m_mgr_dept" => $this->input->post("ip-acci-mgrappro-dept"),
                "m_mgr_datetime" => date("Y-m-d H:i:s"),
                "m_status" => $mgrApprove
            );
            $this->db->where("m_formno" , $formno);
            $this->db->update("acci_main" , $arMgrUpdate);

            $this->email_model->sendEmail_toOhs($formno);

            $output = array(
                "msg" => "บันทึกข้อมูลการอนุมัติของผู้จัดการสำเร็จ",
                "status" => "Save Mgr Approve Success"
            );
        }else{
            $output = array(
                "msg" => "บันทึกข้อมูลการอนุมัติของผู้จัดการไม่สำเร็จ",
                "status" => "Save Mgr Approve Not Success"
            );
        }
        echo json_encode($output);
    }


    public function api_checkMgrData()
    {
        $received_data = json_decode(file_get_contents("php://input"));
        if($received_data->action == "checkMgrData"){
            $formno = $received_data->formno;
            $sql = $this->db->query("SELECT
            acci_main.m_mgr_datetime,
            acci_main.m_mgr_dept,
            acci_main.m_mgr_user,
            acci_main.m_mgr_memo,
            acci_main.m_mgr_approve,
            acci_main.m_status
            FROM
            acci_main
            WHERE m_formno = '$formno' ");
            if($sql->row()->m_status == "Open"){
                $output = array(
                    "msg" => "ยังไม่พบการอนุมัติจากผู้จัดการ",
                    "status" => "Not Found Data Mgr"
                );
            }else{
                $output = array(
                    "msg" => "พบการอนุมัติจากผู้จัดการ",
                    "status" => "Found Data Mgr",
                    "result" => $sql->row()
                );
            }

            
        }
        echo json_encode($output);
    }


    public function api_checkOhsData()
    {
        $received_data = json_decode(file_get_contents("php://input"));
        if($received_data->action == "checkOhsData"){
            $formno = $received_data->formno;
            $sql = $this->db->query("SELECT
            acci_main.m_ohs_datetime,
            acci_main.m_ohs_dept,
            acci_main.m_ohs_user,
            acci_main.m_ohs_memo,
            acci_main.m_ohs_approve,
            acci_main.m_status,
            acci_main.m_formno,
            acci_main.m_complaint_formno
            FROM
            acci_main
            WHERE m_formno = '$formno' ");
            if($sql->row()->m_status == "Manager Approved"){
                $output = array(
                    "msg" => "ยังไม่พบการอนุมัติจาก จป.",
                    "status" => "Not Found Data Ohs"
                );
            }else if($sql->row()->m_status == "OHS Approved" || $sql->row()->m_status == "OHS Not Approve" || $sql->row()->m_status == "OHS Reject"){
                $output = array(
                    "msg" => "พบการอนุมัติจาก จป.",
                    "status" => "Found Data Ohs",
                    "result" => $sql->row()
                );
            }else if($sql->row()->m_status == "Complete"){
                $output = array(
                    "msg" => "จป. สร้างเอกสาร Complaint เรียบร้อยแล้ว",
                    "status" => "Complaint Created",
                    "result" => $sql->row()
                );
            }

            
        }
        echo json_encode($output);
    }


    public function api_saveOhs()
    {
        if($this->input->post("ip-acci-ohsappro-formno") != ""){
            $formno = $this->input->post("ip-acci-ohsappro-formno");

            if($this->input->post("ip-acci-ohsappro") == "อนุมัติ"){
                $ohsApprove = "OHS Approved";
            }else if($this->input->post("ip-acci-ohsappro") == "ไม่อนุมัติ"){
                $ohsApprove = "OHS Not Approve";
            }else if($this->input->post("ip-acci-ohsappro") == "แจ้งแก้ไข"){
                $ohsApprove = "OHS Reject";
            }
            $arOhsUpdate = array(
                "m_ohs_approve" => $this->input->post("ip-acci-ohsappro"),
                "m_ohs_memo" => $this->input->post("ip-acci-ohsappro-memo"),
                "m_ohs_user" => $this->input->post("ip-acci-ohsappro-user"),
                "m_ohs_dept" => $this->input->post("ip-acci-ohsappro-dept"),
                "m_ohs_datetime" => date("Y-m-d H:i:s"),
                "m_status" => $ohsApprove
            );
            $this->db->where("m_formno" , $formno);
            $this->db->update("acci_main" , $arOhsUpdate);

            if($this->input->post("ip-acci-ohsappro") == "แจ้งแก้ไข"){
                $sql = $this->db->query("SELECT
                acci_main.m_autoid,
                acci_main.m_formno,
                acci_main.m_complaint_formno,
                acci_main.m_acci_datetime,
                acci_main.m_acci_location,
                acci_main.m_type1,
                acci_main.m_type2,
                acci_main.m_type3,
                acci_main.m_acci_name,
                acci_main.m_acci_ecode,
                acci_main.m_acci_dept,
                acci_main.m_acci_deptcode,
                acci_main.m_acci_cardno,
                acci_main.m_acci_carno,
                acci_main.m_acci_res,
                acci_main.m_acci_detail,
                acci_main.m_acci_filestatus,
                acci_main.m_user_inform,
                acci_main.m_ecode_inform,
                acci_main.m_dept_inform,
                acci_main.m_deptcode_inform,
                acci_main.m_datetime_inform,
                acci_main.m_status,
                acci_main.m_user_modify,
                acci_main.m_ecode_modify,
                acci_main.m_dept_modify,
                acci_main.m_deptcode_modify,
                acci_main.m_datetime_modify,
                acci_main.m_mgr_approve,
                acci_main.m_mgr_memo,
                acci_main.m_mgr_user,
                acci_main.m_mgr_dept,
                acci_main.m_mgr_datetime,
                acci_main.m_ohs_approve,
                acci_main.m_ohs_memo,
                acci_main.m_ohs_user,
                acci_main.m_ohs_dept,
                acci_main.m_ohs_datetime
                FROM
                acci_main
                WHERE m_formno = '$formno'");

                foreach($sql->result() as $rs){
                    $arInsertHistory = array(
                        "m_formno" => $formno,
                        "m_acci_datetime" => $rs->m_acci_datetime,
                        "m_acci_location" => $rs->m_acci_location,
                        "m_type1" => $rs->m_type1,
                        "m_type2" => $rs->m_type2,
                        "m_type3" => $rs->m_type3,
                        "m_acci_name" => $rs->m_acci_name,
                        "m_acci_ecode" => $rs->m_acci_ecode,
                        "m_acci_dept" => $rs->m_acci_dept,
                        "m_acci_deptcode" => $rs->m_acci_deptcode,
                        "m_acci_cardno" => $rs->m_acci_cardno,
                        "m_acci_carno" => $rs->m_acci_carno,
                        "m_acci_res" => $rs->m_acci_res,
                        "m_acci_detail" => $rs->m_acci_detail,
                        "m_acci_filestatus" => $rs->m_acci_filestatus,
                        "m_user_inform" => $rs->m_user_inform,
                        "m_ecode_inform" => $rs->m_ecode_inform,
                        "m_dept_inform" => $rs->m_dept_inform,
                        "m_deptcode_inform" => $rs->m_deptcode_inform,
                        "m_datetime_inform" => $rs->m_datetime_inform,
                        "m_status" => $rs->m_status,
                        "m_user_modify" => $rs->m_user_modify,
                        "m_ecode_modify" => $rs->m_ecode_modify,
                        "m_dept_modify" => $rs->m_dept_modify,
                        "m_deptcode_modify" => $rs->m_deptcode_modify,
                        "m_datetime_modify" => $rs->m_datetime_modify,
                        "m_mgr_approve" => $rs->m_mgr_approve,
                        "m_mgr_memo" => $rs->m_mgr_memo,
                        "m_mgr_user" => $rs->m_mgr_user,
                        "m_mgr_dept" => $rs->m_mgr_dept,
                        "m_mgr_datetime" => $rs->m_mgr_datetime,
                        "m_ohs_approve" => $rs->m_ohs_approve,
                        "m_ohs_memo" => $rs->m_ohs_memo,
                        "m_ohs_user" => $rs->m_ohs_user,
                        "m_ohs_dept" => $rs->m_ohs_dept,
                        "m_ohs_datetime" => $rs->m_ohs_datetime
                    );

                    $this->db->insert("acci_main_history" , $arInsertHistory);
                }
            }

            $this->email_model->saveEmail_toOhs($formno);

            $output = array(
                "msg" => "บันทึกข้อมูลการอนุมัติของ จป สำเร็จ",
                "status" => "Save OHS Approve Success",
                "formno" => $this->input->post("ip-acci-ohsappro-formno")
            );
        }else{
            $output = array(
                "msg" => "บันทึกข้อมูลการอนุมัติของ จป ไม่สำเร็จ",
                "status" => "Save OHS Approve Not Success"
            );
        }
        echo json_encode($output);
    }

    
    public function api_forcomplaint($formno)
    {

        if($formno != ""){
            $sql = $this->db->query("SELECT
            acci_main.m_autoid,
            acci_main.m_formno,
            acci_main.m_acci_datetime,
            acci_main.m_acci_location,
            acci_main.m_type1,
            acci_main.m_type2,
            acci_main.m_type3,
            acci_main.m_acci_name,
            acci_main.m_acci_ecode,
            acci_main.m_acci_dept,
            acci_main.m_acci_deptcode,
            acci_main.m_acci_cardno,
            acci_main.m_acci_carno,
            acci_main.m_acci_res,
            acci_main.m_acci_detail,
            acci_main.m_acci_filestatus,
            acci_main.m_user_inform,
            acci_main.m_ecode_inform,
            acci_main.m_dept_inform,
            acci_main.m_deptcode_inform,
            acci_main.m_datetime_inform,
            acci_main.m_status,
            acci_main.m_user_modify,
            acci_main.m_ecode_modify,
            acci_main.m_dept_modify,
            acci_main.m_deptcode_modify
            FROM
            acci_main
            WHERE m_formno = '$formno'
            ");

            $result_file = $this->getFiles($formno);

            $output = array(
                "msg" => "ดึงข้อมูลเอกสารเลขที่ $formno สำเร็จ",
                "status" => "Select Data Success",
                "result" => $sql->row(),
                "result_file" => $result_file->result()
            );
        }

        echo json_encode($output);
    }

    public function api_getGraph1()
    {
        $received_data = json_decode(file_get_contents("php://input"));
        if($received_data->action == "getDataForGraph1"){
            $sql = $this->db->query("SELECT
            DATE_FORMAT(m_datetime_inform, '%m') as rao_conmonth,
            count(DATE_FORMAT(m_datetime_inform, '%m')) as count_by_month,
            DATE_FORMAT(m_datetime_inform, '%Y') as rao_conyear
            FROM
            acci_main
            WHERE DATE_FORMAT(m_datetime_inform, '%Y') = '2023'
            GROUP BY rao_conyear , rao_conmonth");
            

            $output = array(
                "msg" => "ดึงข้อมูลในการทำกราฟสำเร็จ",
                "status" => "Select Data Success",
                "result1" => $sql->result()
            );

            echo json_encode($output);
        }
    }









}/* End of file ModelName.php */

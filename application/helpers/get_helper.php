<?php
class getfn{
    public $ci;
    function __construct()
    {
        $this->ci = &get_instance();
        date_default_timezone_set("Asia/Bangkok");
    }

    public function gci()
    {
        return $this->ci;
    }

}


function getfn()
{
    $obj = new getfn();
    return $obj->gci();
}



// Template Set เป็นการกำหนดค่าให้กับ Template
// Template Set เป็นการกำหนดค่าให้กับ Template
// Template Set เป็นการกำหนดค่าให้กับ Template
// Template Set เป็นการกำหนดค่าให้กับ Template
function getHead()
{
    return getfn()->load->view("templates/head");
}

function getFooter()
{
    return getfn()->load->view("templates/footer");
}

function getContent($page , $data)
{
    return getfn()->parser->parse($page , $data);
}

function getModal()
{
    return getfn()->load->view("templates/modal");
}
// Template Set เป็นการกำหนดค่าให้กับ Template
// Template Set เป็นการกำหนดค่าให้กับ Template
// Template Set เป็นการกำหนดค่าให้กับ Template
// Template Set เป็นการกำหนดค่าให้กับ Template



function getFormNo()
{
    $obj = new getfn();
    // check formno ซ้ำในระบบ
    $checkRowdata = $obj->gci()->db->query("SELECT
    m_formno FROM acci_main ORDER BY m_formno DESC LIMIT 1 
    ");
    $result = $checkRowdata->num_rows();

    $cutYear = substr(date("Y"), 2, 2);
    $getMonth = substr(date("m"), 0, 2);
    $formno = "";
    if ($result == 0) {
        $formno = "ACCI" . $cutYear . $getMonth . "001";
    } else {

        $getFormno = $checkRowdata->row()->m_formno; //อันนี้ดึงเอามาทั้งหมด OS20001
        $cutGetFormno = substr($getFormno, 4, 2); //อันนี้ตัดเอาเฉพาะปีจาก 2020 ตัดเหลือ 20
        $cutNo = substr($getFormno, 8, 3); //อันนี้ตัดเอามาแค่ตัวเลขจาก CRF2003001 ตัดเหลือ 001
        $cutNo++;

        if ($cutNo < 10) {
            $cutNo = "00" . $cutNo;
        } else if ($cutNo < 100) {
            $cutNo = "0" . $cutNo;
        }

        if ($cutGetFormno != $cutYear) {
            $formno = "ACCI" . $cutYear .$getMonth. "001";
        } else {
            $formno = "ACCI" . $cutGetFormno .$getMonth. $cutNo;
        }
    }

    return $formno;
}


function getRuningCode($groupcode)
{
    $date = date_create();
    $dateTimeStamp = date_timestamp_get($date);
    return $groupcode.$dateTimeStamp;
}


function getDb()
{
    $sql = getfn()->db->query("SELECT
    db.db_autoid,
    db.db_username,
    db.db_password,
    db.db_databasename,
    db.db_host,
    db.db_active
    FROM
    db");

    return $sql->row();
}





// Query Zone
function getUser()
{
    getfn()->load->model("login_model");
    return getfn()->login_model->getUser();
}


function getStatus($formno)
{
    $sql = getfn()->db->query("SELECT m_status FROM acci_main WHERE m_formno = '$formno' ");
    return $sql->row();
}


function getAcciType($type_id)
{
    if($type_id != ""){
        $sql = getfn()->db->query("SELECT * FROM acci_type WHERE type_autoid = '$type_id' ");
        return $sql->row();
    }
}

function getViewFullData($formno)
{
    if($formno != ""){
        $sql = getfn()->db->query("SELECT
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
        WHERE m_formno = '$formno'
        ");

        return $sql->row();
    }
}

function getFile($formno)
{
    if($formno != "")
    {
        $sql = getfn()->db->query("SELECT * FROM acci_files WHERE file_m_formno = '$formno'");
        return $sql->result();
    }
}





// END Helper
?>
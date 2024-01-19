<?php
class emailfn{
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



function emailobj()
{
    $obj = new emailfn();
    return $obj->gci();
}



function getEmailUser()
{
    $query = emailobj()->db->query("SELECT * FROM email_information");
    return $query->row();
}



function send_email($subject , $body ,$to = "" , $cc = "" , $formno)
{
    require("PHPMailer_5.2.0/class.phpmailer.php");

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->CharSet = "utf-8";  // ในส่วนนี้ ถ้าระบบเราใช้ tis-620 หรือ windows-874 สามารถแก้ไขเปลี่ยนได้
    $mail->SMTPDebug = 1;                                      // set mailer to use SMTP
    $mail->Host = "mail.saleecolour.net";  // specify main and backup server

    $mail->Port = 587; // พอร์ท

    $mail->SMTPAuth = true;     // turn on SMTP authentication
    $mail->Username = getEmailUser()->email_user;  // SMTP username

    $mail->Password = getEmailUser()->email_password; // SMTP password

    $mail->From = getEmailUser()->email_user;
    $mail->FromName = "โปรแกรมแจ้งอุบัติเหตุ [ เอกสารเลขที่ $formno ]";


    if($to != ""){
        foreach($to as $email){
            $mail->AddAddress($email);
        }
    }


    if($cc != ""){
        foreach($cc as $email){
            $mail->AddCC($email);
        }
    }


    // $mail->AddAddress("chainarong_k@saleecolour.com");
    $mail->AddBCC("chainarong_k@saleecolour.com");

    $mail->WordWrap = 50;                                 // set word wrap to 50 characters
    $mail->IsHTML(true);                                  // set email format to HTML
    $mail->Subject = $subject;
    $mail->Body = '
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Sarabun&display=swap");

        h3{
            font-family: Tahoma, sans-serif;
            font-size:14px;
        }

        table {
            font-family: Tahoma, sans-serif;
            font-size:14px;
            border-collapse: collapse;
            width: 90%;
        }
        
        td, th {
            border: 1px solid #ccc;
            text-align: left;
            padding: 8px;
        }
        
        tr:nth-child(even) {
            background-color: #F5F5F5;
        }

        .bghead{
            text-align:center;
            background-color:#D3D3D3;
        }
    </style>
    '.$body;
    // $mail->send();
    if($_SERVER['HTTP_HOST'] != "localhost"){
        $mail->send();
    }
}


// Query Get Manager Email
function getManagerEmail($deptcode)
{
    emailobj()->db2 = emailobj()->load->database('saleecolour', TRUE);
    if($deptcode == 1007){
        $ccSpeacial = "OR ecode = 'M0040' ";
    }else{
        $ccSpeacial = '';
    }
    $sql = emailobj()->db2->query("SELECT memberemail From member Where DeptCode = '$deptcode' and posi IN (65 , 75) and resigned = 0 and areaid is null $ccSpeacial");
    return $sql;
}


function getOwnerEmail($ecode)
{
    emailobj()->db2 = emailobj()->load->database('saleecolour', TRUE);
    $sql = emailobj()->db2->query("SELECT memberemail From member Where ecode = '$ecode' and resigned = 0 ");
    return $sql;
}

function getOhsEmail()
{
    emailobj()->db2 = emailobj()->load->database('saleecolour', TRUE);
    $sql = emailobj()->db2->query("SELECT memberemail From member Where ecode = 'M0004' and resigned = 0 ");
    return $sql;
}











?>
<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Login_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        //Do your magic here
        date_default_timezone_set("Asia/bangkok");
        $this->db2 = $this->load->database('saleecolour', TRUE);
    }


    private function escape_string()
    {
        return mysqli_connect("localhost", "ant", "Ant1234", "saleecolour");
    }


    // //Start checklogin method
    // public function checklogin()
    // {

    //     if ($_POST['username'] != "" && $_POST['password'] != "") {

    //         $this->load->library('user_agent');
    //         $user = mysqli_real_escape_string($this->escape_string(), $_POST['username']);
    //         $pass = mysqli_real_escape_string($this->escape_string(), md5($_POST['password']));
    //         // If System go on Please add md5 to element name password 'md5'

    //         $checkuser = $this->db2->query(sprintf("SELECT * FROM member WHERE username = '%s' and password = '%s'  ", $user, $pass));
    //         $checkdata = $checkuser->num_rows();

    //         if ($checkdata == 0) {
    //             echo $this->session->set_flashdata('msg', '<div class="alert alert-danger" role="alert" style="font-size:15px;text-align: center;">ชื่อผู้ใช้ หรือ รหัสผ่านไม่ถูกต้อง</div>');
    //             redirect('login');
    //             die();
    //         } else {

    //             foreach ($checkuser->result_array() as $r) {
    //                 $_SESSION['username'] = $r['username'];
    //                 $_SESSION['password'] = $r['password'];
    //                 $_SESSION['Fname'] = $r['Fname'];
    //                 $_SESSION['Lname'] = $r['Lname'];
    //                 $_SESSION['Dept'] = $r['Dept'];
    //                 $_SESSION['ecode'] = $r['ecode'];
    //                 $_SESSION['DeptCode'] = $r['DeptCode'];
    //                 $_SESSION['memberemail'] = $r['memberemail'];
    //                 $_SESSION['file_img'] = $r['file_img'];
    //                 $_SESSION['posi'] = $r['posi'];

    //                 // insert login log
    //                 session_write_close();
    //             }


    //             $uri = isset($_SESSION['RedirectKe']) ? $_SESSION['RedirectKe'] : '/intsys/msd_pulverizer';
    //             header('location:' . $uri);
    //             // header("refresh:0; url=" . base_url());

    //         }

    //     }
    // } //End checklogin







    public function logout()
    {
        session_destroy();
        $this->session->unset_userdata('referrer_url');
        header("refresh:0; url=" . base_url());
        die();
    }



    public function getuser()
    {
        $sessionEcode = $_SESSION['ecode'];
        $sql = $this->db2->query("SELECT * FROM member WHERE ecode = '$sessionEcode' ");
        return $sql->row();
    }


    // public function callLogin()
    // {
    //     $controller = $this->router->class;
    //     if ($this->session->userdata("ecode") == "") {
    //         if ($controller != "login") {
    //             $_SESSION['RedirectKe'] = $_SERVER['REQUEST_URI'];
    //             header("refresh:0; url=" . base_url('login'));
    //             exit();
    //         }
    //     }else{
    //         $uri = isset($_SESSION['RedirectKe']) ? $_SESSION['RedirectKe'] : '/intsys/msd_pulverizer';
    //         header('location:' . $uri);
    //     }
    // }



    
}/* End of file ModelName.php */

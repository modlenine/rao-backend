<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Email_model extends CI_model{
    
    public function __construct()
    {
        parent::__construct();
        //Do your magic here
        date_default_timezone_set("Asia/Bangkok");
    }

   
function createQrcode($linkQrcode, $id)
{
   // $obj = new emailfn();
   // $obj->gci()->load->library("Ciqrcode");
   require("phpqrcode/qrlib.php");
   // $this->load->library('phpqrcode/qrlib');

   $SERVERFILEPATH = $_SERVER['DOCUMENT_ROOT'] . '/intsys/rao/rao_backend/uploads/qrcode/';
   $urlQrcode = $linkQrcode;
   // $filename1 = 'qrcode' . rand(2, 200) . ".png";
   $filename1 = 'qrcode' . $id . ".png";
   $folder = $SERVERFILEPATH;

   $filename = $folder . $filename1;

   QRcode::png(
      $urlQrcode,
      $filename,
      // $outfile = false,
      $level = QR_ECLEVEL_H,
      $size = 4,
      $margin = 2
   );

   // echo "<img src='http://192.190.10.27/crf/upload/qrcode/".$filename1."'>";
   return $filename1;
}


// ส่ง Email หา Manager หลังจากสร้าง รายการ
function sendemail_toLabManager($ncp_no)
{
   if(getncpdata($ncp_no)->ncp_qmrApproveType == "แจ้งแก้ไข"){
      $textSub = "รอแก้ไขวิธีการดำเนินงานเบื้องต้น";
   }else{
      $textSub = "รอกำหนดวิธีการดำเนินงานเบื้องต้น";
   }

   $subject = "NCP เลขที่ [".getncpdata($ncp_no)->ncp_formno."] มีรายการใหม่ $textSub";
   $short_url = 'https://intranet.saleecolour.com/intsys/ncps/viewdata/' . $ncp_no;

   $body = '
      <h2>รายการ '.$textSub.'</h2>
      <table>
      <tr>
         <td><strong>เลขที่เอกสาร</strong></td>
         <td>' . getncpdata($ncp_no)->ncp_formno . '</td>
         <td><strong>วันที่สร้างรายการ</strong></td>
         <td>' . conDateTime(getncpdata($ncp_no)->ncp_datetime) . '</td>
      </tr>


      <tr>
         <td><strong>เลขที่ Complaint อ้างอิง</strong></td>
         <td>' . getncpdata($ncp_no)->ncp_com_refno . '</td>
         <td><strong>ผู้พบ</strong></td>
         <td>' . getncpdata($ncp_no)->ncp_userpost . '</td>
      </tr>


      <tr>
         <td><strong>รหัสพนักงาน</strong></td>
         <td>' . getncpdata($ncp_no)->ncp_userecode . '</td>
         <td><strong>แผนก</strong></td>
         <td>' . getncpdata($ncp_no)->ncp_userdeptcode . '</td>
      </tr>

      <tr>
         <td><strong>ตรวจสอบรายการ</strong></td>
         <td colspan="3"><a href="' . $short_url . '">' . $ncp_no . '</a></td>
      </tr>

      <tr>
         <td><strong>Scan QrCode</strong></td>
         <td colspan="3"><img src="' . base_url('upload/qrcode/') . $this->createQrcode($short_url, $formno) . '"></td>
      </tr>


      </table>
      ';

   $to = "";
   $cc = "";

   //  Email Zone
   $optionTo = getLabManagerEmail();
   $to = array();
   foreach ($optionTo->result_array() as $result) {
      $to[] = $result['memberemail'];
   }


   $optioncc = getUserPostEmail(getncpdata($ncp_no)->ncp_userecode);
   $cc = array();
   foreach ($optioncc->result_array() as $resultcc) {
      $cc[] = $resultcc['memberemail'];
   }

   emailSaveDataTH($subject, $body, $to, $cc , getncpdata($ncp_no)->ncp_formno);
   //  Email Zone
}
// ส่ง Email หาบัญชีเพื่อตรวจสอบ Budget


public function sendEmail_toMgr($formno)
{
   if($formno != ""){
      $viewfulldata = getViewFullData($formno);

      $subject = "เอกสารเลขที่ [".$viewfulldata->m_formno."] รอผู้จัดการอนุมัติ";
      $short_url = 'https://intranet.saleecolour.com/intsys/rao/viewdata/' . $formno;

      $body = '
      <h2>เอกสารแจ้งอุบัติเหตุ รอผู้จัดการอนุมัติ</h2>
      <table>
      <tr>
         <td><strong>เลขที่เอกสาร</strong></td>
         <td>' . $viewfulldata->m_formno . '</td>
         <td><strong>วันที่สร้างรายการ</strong></td>
         <td>' . conDateTime($viewfulldata->m_datetime_inform) . '</td>
      </tr>


      <tr>
         <td><strong>วันที่เกิดเหตุ</strong></td>
         <td>' . conDateTime($viewfulldata->m_acci_datetime) . '</td>
         <td><strong>สถานที่เกิดเหตุ</strong></td>
         <td>' . $viewfulldata->m_acci_location . '</td>
      </tr>


      <tr>
         <td><strong>ประเภทของอุบัติเหตุ</strong></td>
         <td>' . $viewfulldata->m_type3 . '</td>
         <td><strong>อุบัติเหตุเกิดจาก</strong></td>
         <td>' . $viewfulldata->m_type1 . '</td>
      </tr>

      <tr>
         <td><strong>ผู้แจ้ง</strong></td>
         <td>' . $viewfulldata->m_user_inform . '</td>
         <td><strong>รหัสพนักงาน</strong></td>
         <td>' . $viewfulldata->m_ecode_inform . '</td>
      </tr>

      <tr>
         <td><strong>ตรวจสอบรายการ</strong></td>
         <td colspan="3"><a href="' . $short_url . '">' . $formno . '</a></td>
      </tr>

      <tr>
         <td><strong>Scan QrCode</strong></td>
         <td colspan="3"><img src="' . base_url('uploads/qrcode/') . $this->createQrcode($short_url, $formno) . '"></td>
      </tr>


      </table>
      ';
      $to = "";
      $cc = "";

      //  Email Zone
      $optionTo = getManagerEmail($viewfulldata->m_deptcode_inform);
      $to = array();
      foreach ($optionTo->result_array() as $result) {
         $to[] = $result['memberemail'];
      }


      $optioncc = getOwnerEmail($viewfulldata->m_ecode_inform);
      $cc = array();
      foreach ($optioncc->result_array() as $resultcc) {
         $cc[] = $resultcc['memberemail'];
      }

      send_email($subject , $body ,$to , $cc , $formno);

   }
}


public function sendEmail_toOhs($formno)
{
   if($formno != ""){
      $viewfulldata = getViewFullData($formno);

      $subject = "เอกสารเลขที่ [".$viewfulldata->m_formno."] รอ จป.อนุมัติ";
      $short_url = 'https://intranet.saleecolour.com/intsys/rao/viewdata/' . $formno;

      $body = '
      <h2>เอกสารแจ้งอุบัติเหตุ รอ จป.อนุมัติ</h2>
      <table>
      <tr>
         <td><strong>เลขที่เอกสาร</strong></td>
         <td>' . $viewfulldata->m_formno . '</td>
         <td><strong>วันที่สร้างรายการ</strong></td>
         <td>' . conDateTime($viewfulldata->m_datetime_inform) . '</td>
      </tr>


      <tr>
         <td><strong>วันที่เกิดเหตุ</strong></td>
         <td>' . conDateTime($viewfulldata->m_acci_datetime) . '</td>
         <td><strong>สถานที่เกิดเหตุ</strong></td>
         <td>' . $viewfulldata->m_acci_location . '</td>
      </tr>


      <tr>
         <td><strong>ประเภทของอุบัติเหตุ</strong></td>
         <td>' . $viewfulldata->m_type3 . '</td>
         <td><strong>อุบัติเหตุเกิดจาก</strong></td>
         <td>' . $viewfulldata->m_type1 . '</td>
      </tr>

      <tr>
         <td><strong>ผู้แจ้ง</strong></td>
         <td>' . $viewfulldata->m_user_inform . '</td>
         <td><strong>รหัสพนักงาน</strong></td>
         <td>' . $viewfulldata->m_ecode_inform . '</td>
      </tr>

      <tr>
         <td colspan="4" class="bghead"><strong>ผลการอนุมัติจากผู้จัดการ</strong></td>
      </tr>
         <tr>
         <td><strong>ผลการอนุมัติ</strong></td>
         <td colspan="3">' . $viewfulldata->m_mgr_approve . '</td>
      </tr>
      <tr>
         <td><strong>เหตุผล</strong></td>
         <td colspan="3">' . $viewfulldata->m_mgr_memo . '</td>
      </tr>
      <tr>
         <td><strong>ผู้อนุมัติ</strong></td>
         <td colspan="3">' . $viewfulldata->m_mgr_user . '</td>
      </tr>
      <tr>
         <td><strong>ฝ่าย</strong></td>
         <td>' . $viewfulldata->m_mgr_dept . '</td>
         <td><strong>ลงวันที่</strong></td>
         <td>' . conDateTimeFromDb($viewfulldata->m_mgr_datetime) . '</td>
      </tr>

      <tr>
         <td><strong>ตรวจสอบรายการ</strong></td>
         <td colspan="3"><a href="' . $short_url . '">' . $formno . '</a></td>
      </tr>

      <tr>
         <td><strong>Scan QrCode</strong></td>
         <td colspan="3"><img src="' . base_url('uploads/qrcode/') . $this->createQrcode($short_url, $formno) . '"></td>
      </tr>


      </table>
      ';
      $to = "";
      $cc = "";

      //  Email Zone
      $optionTo = getOhsEmail();
      $to = array();
      foreach ($optionTo->result_array() as $result) {
         $to[] = $result['memberemail'];
      }


      $optioncc = getOwnerEmail($viewfulldata->m_ecode_inform);
      $cc = array();
      foreach ($optioncc->result_array() as $resultcc) {
         $cc[] = $resultcc['memberemail'];
      }

      send_email($subject , $body ,$to , $cc , $formno);

   }
}


public function saveEmail_toOhs($formno)
{
   if($formno != ""){
      $viewfulldata = getViewFullData($formno);

      $subject = "เอกสารเลขที่ [".$viewfulldata->m_formno."] จป. ดำเนินการแล้ว";
      $short_url = 'https://intranet.saleecolour.com/intsys/rao/viewdata/' . $formno;

      $body = '
      <h2>เอกสารแจ้งอุบัติเหตุ จป.ดำเนินการแล้ว</h2>
      <table>
      <tr>
         <td><strong>เลขที่เอกสาร</strong></td>
         <td>' . $viewfulldata->m_formno . '</td>
         <td><strong>วันที่สร้างรายการ</strong></td>
         <td>' . conDateTime($viewfulldata->m_datetime_inform) . '</td>
      </tr>


      <tr>
         <td><strong>วันที่เกิดเหตุ</strong></td>
         <td>' . conDateTime($viewfulldata->m_acci_datetime) . '</td>
         <td><strong>สถานที่เกิดเหตุ</strong></td>
         <td>' . $viewfulldata->m_acci_location . '</td>
      </tr>


      <tr>
         <td><strong>ประเภทของอุบัติเหตุ</strong></td>
         <td>' . $viewfulldata->m_type3 . '</td>
         <td><strong>อุบัติเหตุเกิดจาก</strong></td>
         <td>' . $viewfulldata->m_type1 . '</td>
      </tr>

      <tr>
         <td><strong>ผู้แจ้ง</strong></td>
         <td>' . $viewfulldata->m_user_inform . '</td>
         <td><strong>รหัสพนักงาน</strong></td>
         <td>' . $viewfulldata->m_ecode_inform . '</td>
      </tr>

      <tr>
         <td colspan="4" class="bghead"><strong>ผลการอนุมัติจากผู้จัดการ</strong></td>
      </tr>
      <tr>
         <td><strong>ผลการอนุมัติ</strong></td>
         <td colspan="3">' . $viewfulldata->m_mgr_approve . '</td>
      </tr>
      <tr>
         <td><strong>เหตุผล</strong></td>
         <td colspan="3">' . $viewfulldata->m_mgr_memo . '</td>
      </tr>
      <tr>
         <td><strong>ผู้อนุมัติ</strong></td>
         <td colspan="3">' . $viewfulldata->m_mgr_user . '</td>
      </tr>
      <tr>
         <td><strong>ฝ่าย</strong></td>
         <td>' . $viewfulldata->m_mgr_dept . '</td>
         <td><strong>ลงวันที่</strong></td>
         <td>' . conDateTimeFromDb($viewfulldata->m_mgr_datetime) . '</td>
      </tr>

      <tr>
         <td colspan="4" class="bghead"><strong>ผลการอนุมัติจาก จป.</strong></td>
      </tr>
      <tr>
         <td><strong>ผลการอนุมัติ</strong></td>
         <td colspan="3">' . $viewfulldata->m_ohs_approve . '</td>
      </tr>
      <tr>
         <td><strong>เหตุผล</strong></td>
         <td colspan="3">' . $viewfulldata->m_ohs_memo . '</td>
      </tr>
      <tr>
         <td><strong>ผู้อนุมัติ</strong></td>
         <td colspan="3">' . $viewfulldata->m_ohs_user . '</td>
      </tr>
      <tr>
         <td><strong>ฝ่าย</strong></td>
         <td>' . $viewfulldata->m_ohs_dept . '</td>
         <td><strong>ลงวันที่</strong></td>
         <td>' . conDateTimeFromDb($viewfulldata->m_ohs_datetime) . '</td>
      </tr>

      <tr>
         <td><strong>ตรวจสอบรายการ</strong></td>
         <td colspan="3"><a href="' . $short_url . '">' . $formno . '</a></td>
      </tr>

      <tr>
         <td><strong>Scan QrCode</strong></td>
         <td colspan="3"><img src="' . base_url('uploads/qrcode/') . $this->createQrcode($short_url, $formno) . '"></td>
      </tr>


      </table>
      ';
      $to = "";
      $cc = "";

      //  Email Zone
      $optionTo = getOwnerEmail($viewfulldata->m_ecode_inform);
      $to = array();
      foreach ($optionTo->result_array() as $result) {
         $to[] = $result['memberemail'];
      }


      // $optioncc = getUserPostEmail(getncpdata($ncp_no)->ncp_userecode);
      // $cc = array();
      // foreach ($optioncc->result_array() as $resultcc) {
      //    $cc[] = $resultcc['memberemail'];
      // }

      send_email($subject , $body ,$to , $cc , $formno);

   }
}















    
}
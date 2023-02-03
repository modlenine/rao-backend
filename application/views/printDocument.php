<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Document No. <?=$dataMain->m_formno?></title>
</head>

<body>
    <?php

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('IT Dept');
    $pdf->SetTitle('ฟอร์มแจ้งอุบัติเหต');
    $pdf->SetSubject('ฟอร์มแจ้งอุบัติเหต');
    $pdf->SetKeywords('ฟอร์มแจ้งอุบัติเหต');

    // set default header data

    // $pdf->SetHeaderData('Document Library');
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);


    // set header and footer fonts
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);


    // set margins

    // $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

    // $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);

    // $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetMargins(10, 5, 10, true);

    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {

        require_once(dirname(__FILE__) . '/lang/eng.php');

        $pdf->setLanguageArray($l);
    }

    // ---------------------------------------------------------

    // set font
    $pdf->SetFont('thsarabun', '', 12);
    // Print a table
    // add a page
    $pdf->AddPage();
    // create some HTML content

    $typeCheck1 = '';
    $typeCheck2 = '';
    if($dataMain->m_type1 == "บุคคลภายในบริษัท"){
        $typeCheck1 = 'checked="checked"';
    }else if($dataMain->m_type1 == "บุคคลภายนอกบริษัท"){
        $typeCheck2 = 'checked="checked"';
    }


    $type2Check1 = '';
    $type2Check2 = '';
    if($dataMain->m_type2 == "asset"){
        $type2Check1 = 'checked="checked"';
    }else if($dataMain->m_type2 == "man"){
        $type2Check2 = 'checked="checked"';
    }


    $html ='
    <style>
        .textH1{
            font-size:22px;
            font-weight:600;
        }
        .textSub{
            font-size:18px;
        }
        .mt-2{
            margin-top:20px;
        }
        td{
            font-size:16px;
        }
    </style>

    <div style="text-align:center;">
        <span class="textH1"><b>ฟอร์มแจ้งอุบัติเหตุ</b></span><br>
        <span class="textSub">เอกสารเลขที่ : '.$dataMain->m_formno.'</span>
    </div>
    <hr>
    <div>
        <table>
            <tr>
                <td><span><b>วันที่เกิดเหตุ : </b>'.conDateTime($dataMain->m_acci_datetime).'</span></td>
                <td><span><b>สถานที่เกิดเหตุ : </b>'.$dataMain->m_acci_location.'</span></td>
                
            </tr>
            <br>
            <tr>
                <td>
                    <span><b>การเกิดขึ้นของอุบัติเหตุ (เกิดจาก) : </b></span><br>
                    <input '.$typeCheck1.' readonly="true" type="radio" id="p-input-m_type1-1" name="p-input-m_type1" class="custom-control-input" value="บุคคลภายในบริษัท">
                    <label class="custom-control-label" for="p-input-m_type1-1">บุคคลภายในบริษัท</label><br>

                    <input '.$typeCheck2.' readonly="true" type="radio" id="p-input-m_type1-2" name="p-input-m_type1" class="custom-control-input" value="บุคคลภายนอกบริษัท">
                    <label class="custom-control-label" for="p-input-m_type1-2">บุคคลภายนอกบริษัท</label><br>
                </td>
                <td>
                    <span><b>หมวดหมู่ : </b></span><br>
                    <input '.$type2Check1.' readonly="true" type="radio" id="p-input-m_type2-1" name="p-input-m_type2" class="custom-control-input" value="Asset">
                    <label class="custom-control-label" for="p-input-m_type2-1">Asset</label><br>

                    <input '.$type2Check2.' readonly="true" type="radio" id="p-input-m_type2-2" name="p-input-m_type2" class="custom-control-input" value="Man">
                    <label class="custom-control-label" for="p-input-m_type2-2">Man</label><br>
                </td>
            </tr>
            <tr>
                <td>
                    <span><b>ประเภทของอุบัติเหตุ : </b>'.$dataMain->m_type3.'</span>
                </td>
            </tr>
            <br>
            <tr>
                <td>
                    <span><b>ชื่อผู้ประสบเหตุ / ผู้ที่เกี่ยวข้อง : </b>'.$dataMain->m_acci_name.'</span>
                </td>
                <td>
                    <span><b>รหัสพนักงาน : </b>'.$dataMain->m_acci_ecode.'</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span><b>แผนก : </b>'.$dataMain->m_acci_dept.'</span>
                </td>
                <td>
                    <span><b>เลขบัตรประจำตัวประชาชน : </b>'.$dataMain->m_acci_cardno.'</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span><b>ทะเบียนรถ : </b>'.$dataMain->m_acci_carno.'</span>
                </td>
                <td>
                    <span><b>ตำแหน่ง / หน้าที่ความรับผิดชอบ : </b>'.$dataMain->m_acci_res.'</span>
                </td>
            </tr>
            <br>
            <tr>
                <td colspan="2">
                    <span><b>รายละเอียด : </b>'.$dataMain->m_acci_detail.'</span>
                </td>
            </tr>
            <br>
            <tr>
                <td>
                    <span><b>แนบหลักฐาน : </b></span>
                </td>
            </tr>
        </table>
    </div>
    ';


    $html .='
        <div style="text-align:center;">';

        foreach($dataFile as $rs){
            $html .='
                <img src="'.base_url().$rs->file_path.$rs->file_name.'" alt="test alt attribute" width="200" height="200" border="0" />
            ';
        }

    $html .='
        </div>
    ';

    $html .='
    <hr>
    <br>
    <div style="margin-top:15px;"></div>
    <table style="text-align:center;">
        <tr>
            <td>
                <span><b>ผู้แจ้ง</b></span><br>
                <span>'.$dataMain->m_user_inform.'</span><br>
                <span>'.conDateTime($dataMain->m_datetime_inform).'</span>
            </td>
            <td>
                <span><b>ผู้จัดการอนุมัติ</b></span><br>
                <span>'.$dataMain->m_mgr_user.'</span><br>
                <span>'.conDateTime($dataMain->m_mgr_datetime).'</span>
            </td>
            <td>
                <span><b>จป.อนุมัติ</b></span><br>
                <span>'.$dataMain->m_ohs_user.'</span><br>
                <span>'.conDateTime($dataMain->m_ohs_datetime).'</span>
            </td>
        </tr>
    </table>
    ';











    
    // output the HTML content
    $pdf->writeHTML($html, true, false, true, false, '');

    // reset pointer to the last page
    $pdf->lastPage();

    // Print all HTML colors
    ob_end_clean();

    $filename = "$dataMain->m_formno.pdf";

    //Close and output PDF document
    $pdf->Output($filename, 'I');

    //============================================================+
    // END OF FILE
    //============================================================+

    ?>
    
</body>
</html>
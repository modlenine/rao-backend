<?php
class confn
{
    public $ci;
    function __construct()
    {
        $this->ci = &get_instance();
        date_default_timezone_set("Asia/Bangkok");
    }

    function cci()
    {
        return $this->ci;
    }
}

function cfn()
{
    $obj = new confn();
    return $obj->cci();
}

function conPosi($posiNum)
{
    $posiName = "";
    switch ($posiNum) {
        case "15":
            $posiName = "Staff";
            break;
        case "35":
            $posiName = "Group leader";
            break;
        case "45":
            $posiName = "Foreman";
            break;
        case "55":
            $posiName = "Supervisor";
            break;
        case "65":
            $posiName = "Asst manager";
            break;
        case "75":
            $posiName = "Manager";
            break;
        case "85":
            $posiName = "Director";
            break;
        case "95":
            $posiName = "Manager director";
            break;
    }
    return $posiName;
}


function conFormno($formno)
{
    return str_replace("-" , "/" , $formno);
}


function conDateTime($datetime)
{
    $dateOri = date_create($datetime);
    return date_format($dateOri , "d/m/Y H:i:s");
}

function conDate($datetime)
{
    $dateOri = date_create($datetime);
    return date_format($dateOri , "Y-m-d");
}

function conDateTimeToDb($datetime)
{
    $dateOri = date_create($datetime);
    return date_format($dateOri , "Y-m-d H:i:s");
}

function conDateTimeFromDb($datetime)
{
    if($datetime != ""){
        $datetimeIn = date_create($datetime);
        return date_format($datetimeIn,"d/m/Y H:i:s");
    }else{
        return null;
    }

}



function duration($start, $end) 
{ 
    $oridatetimeStart = conDate($start);
    $oridatetimeEnd = conDate($end);

    $datetime1 = new DateTime($oridatetimeStart); 
    $datetime2 = new DateTime($oridatetimeEnd); 
    $interval = $datetime1->diff($datetime2); 
    $woweekends = 0; 
    for ($i = 0; $i <= $interval->d; $i++) { 
        $datetime1->modify('+1 day'); 
        $weekday = $datetime1->format('w'); 
        if ($weekday !== "0" && $weekday !== "6") { // 0 for Sunday and 6 for Saturday 
            $woweekends++; 
        } 
    } 
    return $woweekends; 
}


function duration2($start, $end)
{
    // Declare two dates
$start_date = strtotime($start);
$end_date = strtotime($end);
  
// Get the difference and divide into 
// total no. seconds 60/60/24 to get 
// number of days
return (($end_date - $start_date)/60/60/24)+1;
}

function duration3($start, $end)
{
    $jobsubmitdate = new DateTime($start); // --> วันที่ job เข้ามา 
    $daynow = new DateTime($end); // --> วันที่ปัจจุบัน 
    $daynow->modify('+1 day'); 
    $interval = $daynow->diff($jobsubmitdate); 
    $days = $interval->days; 
    $period = new DatePeriod($jobsubmitdate, new DateInterval('P1D'), $daynow); 
    $holidays = array("Y"); 
    foreach ($period as $dt) { 
        $curr = $dt->format('D'); 
        if ($curr == 'Sat' || $curr == 'Sun') { 
            $days--; 
        } elseif (in_array($dt->format('Y-m-d'), $holidays)) { 
            $days--; 
        } 
    } 
    return $days; 
}


function conPrice($priceinput)
{
    $oriprice = str_replace("," , "" , $priceinput);
    return $oriprice;
}


function conDeviceIdToName($deviceId)
{
    cfn()->db3 = cfn()->load->database('itasset_plus', TRUE);
    if($deviceId != ""){
        $sql = cfn()->db3->query("SELECT
        it_device_detail.dv_dt_id,
        it_device_detail.dv_dt_name,
        it_device_detail.dv_dt_type,
        it_device_detail.dv_dt_owner,
        it_device_master.dv_mt_status
        FROM it_device_detail
        INNER JOIN it_device_master ON it_device_master.dv_mt_id = it_device_detail.dv_dt_id
        WHERE it_device_detail.dv_dt_name IS NOT NULL 
        AND it_device_master.dv_mt_status = 'Active'
        AND it_device_detail.dv_dt_id = '$deviceId'");

        if($sql->num_rows() != 0){
            return $sql;
        }else{
            return 'NOT OK';
        }

    }else{
        return "NOT OK";
    }
}


function getLeadtime($startDatetime , $finishDatetime)
{
    if($startDatetime != "" && $finishDatetime != ""){
        $current_date_time_sec = strtotime($startDatetime); 
        $future_date_time_sec = strtotime($finishDatetime); 
        $difference = $future_date_time_sec - $current_date_time_sec; 
        $hours = ($difference / 3600); 
        $minutes = ($difference / 60 % 60); 
        $seconds = ($difference % 60); 
        $days = ($hours / 24);

        // $hours = ($hours % 24); 
        // echo "The difference is <br/>"; 
        // if ($days < 0) { 
        //     echo ceil($days) . " days AND "; 
        // } else { 
        //     echo floor($days) . " days AND "; 
        // } 
        return sprintf("%02d", $hours) . ":" . sprintf("%02d", $minutes) . ":" . sprintf("%02d", $seconds);
    }else{
        return "";
    }
    
}



function conDatetimeToTimesec($datetime)
{
    return strtotime($datetime);
}

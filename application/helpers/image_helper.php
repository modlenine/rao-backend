<?php
class image_fn{
    private $ci;
    function __construct()
    {
        $this->ci =&get_instance();
        date_default_timezone_set("Asia/Bangkok");
    }

    function imageci()
    {
        return $this->ci;
    }
}

function imagefn()
{
    $obj = new image_fn();
    return $obj->imageci();
}

function resize($width, $targetFile, $originalFile ) 
{
    $info = getimagesize($originalFile); 
    $mime = $info['mime']; 
 
    switch ($mime) { 
            case 'image/jpeg':
                    header('Content-Type: image/jpeg');
                    $image_create_func = 'imagecreatefromjpeg'; 
                    $image_save_func = 'imagejpeg'; 
                    $filename_type = 'jpg'; 
                    break; 
 
            case 'image/png': 
                    header('Content-Type: image/png');
                    $image_create_func = 'imagecreatefrompng'; 
                    $image_save_func = 'imagepng'; 
                    $filename_type = 'png'; 
                    break; 
 
            case 'image/gif':
                    header('Content-Type: image/gif');
                    $image_create_func = 'imagecreatefromgif'; 
                    $image_save_func = 'imagegif'; 
                    $filename_type = 'gif'; 
                    break; 
 
            default:  
                    throw error_log('Unknown image type.'); 
    } 
 

    list($width_orig, $height_orig) = getimagesize($originalFile); 
    $height = (int) (($width / $width_orig) * $height_orig); 
    $image_p = imagecreatetruecolor($width, $height);
    $image   = $image_create_func($originalFile);
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
 
    
    // $image_save_func($tmp, "$targetFile.$new_image_ext"); 
    //Fix Orientation
    $exif = exif_read_data($originalFile);
    if ($exif && isset($exif['Orientation']))
    {
        $orientation = $exif['Orientation'];
        switch($orientation) {
            case 3:
                $image_p = imagerotate($image_p, 180, 0);
                break;
            case 6:
                $image_p = imagerotate($image_p, -90, 0);
                break;
            case 8:
                $image_p = imagerotate($image_p, 90, 0);
                break;
        }
    }
    // Output
    $image_save_func($image_p, "$targetFile.$filename_type", 90);




}


function uploadImage($fileInput , $maincode , $user , $ecode)
{
    // Upload file Zone
    // Check folder ว่ามีอยู่หรือไม่
    $yearNow = date("Y");
    $dateNow = date("Y-m-d");
    $imagePath = "uploads/images/".$yearNow."/".$dateNow."/";
    // $paths = 'uploads\images';
    $fileno = 1;

    $url = $_SERVER['HTTP_HOST'];
    if($url == "localhost"){
        $paths = 'uploads\images';
        if(!file_exists($paths."\\".$yearNow)){
            mkdir($paths."\\".$yearNow , 0755 , true);
        }
        if(!file_exists($paths."\\".$yearNow."\\".$dateNow)){
            mkdir($paths."\\".$yearNow."\\".$dateNow , 0755 , true);
        }
    }else if($url == "intranet.saleecolour.com"){
        $paths = 'uploads/images';
        if(!file_exists($paths."/".$yearNow)){
            mkdir($paths."/".$yearNow , 0755 , true);
        }
        if(!file_exists($paths."/".$yearNow."/".$dateNow)){
            mkdir($paths."/".$yearNow."/".$dateNow , 0755 , true);
        }
    }
   
    $file_name = $_FILES[$fileInput]["name"];
    $getRunningCode = getRuningCode(7);

    foreach($file_name as $key => $value){

        if ($_FILES[$fileInput]['tmp_name'][$key] != "") {

            $time = date("H-i-s"); //ดึงเวลามาก่อน
            $path_parts = pathinfo($value);

            if($path_parts['extension'] == "jpeg"){
                $filename_type = "jpg";
            }else{
                $filename_type = $path_parts['extension'];
            }
            
            $file_name_date = substr_replace($value,  $maincode ."-".$getRunningCode. "-" . $fileno .".". $filename_type, 0);

            $file_name_s = substr_replace($value,  $maincode ."-".$getRunningCode. "-" . $fileno , 0);
            // Upload file
            $file_tmp = $_FILES[$fileInput]['tmp_name'][$key];



            if($path_parts['extension'] != "pdf" && $path_parts['extension'] != "png" && $path_parts['extension'] != "PNG"){
                $newWidth = 1000;
                resize($newWidth, "uploads/images/".$yearNow."/".$dateNow."/".$file_name_s, $file_tmp);
                // move_uploaded_file($file_tmp, "upload/images/" . $file_name_date);
                // correctImageOrientation($file_tmp);
            }else{
                move_uploaded_file($file_tmp, "uploads/images/".$yearNow."/".$dateNow."/". $file_name_date);
            }

            // Save Data Image to Database
            $arSaveDataImage = array(
                "file_name" => $file_name_date,
                "file_m_formno" => $maincode,
                "file_path" => $imagePath,
                "file_user" => $user,
                "file_ecode" => $ecode,
                "file_datetime" => date("Y-m-d H:i:s")
            );
            imagefn()->db->insert("acci_files" , $arSaveDataImage);

        } 

        $fileno++;
    }
    
   
        
        
   
    // Upload file Zone
}









?>
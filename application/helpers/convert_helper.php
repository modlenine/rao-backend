<?php
class convertfn{
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

function getcon()
{
    $obj = new convertfn();
    return $obj->gci();
}










?>
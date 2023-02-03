<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Api_mobile extends CI_Controller {

    
    public function __construct()
    {
        parent::__construct();
        //Do your magic here
        $this->load->model('api_mobile_model' , 'api_mobile');
    }
    

    public function index()
    {
        $this->api_mobile->loadOnsitelist();
    }


}

/* End of file Controllername.php */

?>
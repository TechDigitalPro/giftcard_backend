<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function output_data($data, $str_only=false){
    $result = array('res'=>'success', 'api_result'=>$data);
    echo json_encode($result); die;
}

class Crontab_controller extends MY_Controller{

    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
    }

    protected function add_log($data){
        $log_file = FCPATH."/cron/log.txt";
        $myfile = fopen($log_file, "a") or die("Unable to open file!");
        fwrite($myfile, "------------------------------------------------".date('Y-m-d H:i:s')."----------------------------------------------------------------------------------\n");
        fwrite($myfile, "".$data."\n");
        fwrite($myfile, "--------------------------------------------------------------------------------------------------------------------------------------------\n");
        fclose($myfile);
    }

}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hook_controller extends MY_Controller{
    protected $data;
    function output_data($data, $str_only=false){
        $result = array('res'=>'success', 'api_result'=>$data);
        echo json_encode($result); die;
    }
    public function __construct(){
        parent::__construct();
    }
    public function index(){
        //redirect('user/login/index');
    }

   

    protected function get_user_info($condition){
        $user_info = $this->user_model->get($condition);
        $user_info = (empty($user_info) ? array() : $user_info);
        return $user_info;
    }

  
    
    protected function add_log($log_data){
        $log_file = APPPATH."/logs/log.txt";
        try{
            $myfile = fopen($log_file, "a") or die("Unable to open file!");
            $txt = date("Y-m-d H:i:s");
            fwrite($myfile, $txt."\n");
            fwrite($myfile, $log_data."\n");
            fclose($myfile);
        }catch (Exception $e){
            return false;
        }
        return true;
    }
}
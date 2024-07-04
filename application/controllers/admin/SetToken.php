<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once 'Admin_controller.php';

class SetToken extends Admin_Controller {
    public function __construct(){
        parent::__construct();
        $this->check_login();
    }

    public function register_admin_token(){
        $this->load->model('mb_token_model');
        $push_token = $_POST['push_token'];
        file_put_contents("2.txt", print_r($push_token, 1));
        if(empty($push_token)){
            output_error("Please enable notifications on browser");
        }
        $device_key = "";
        $mb_user_token_info = $this->mb_token_model->get(array("user_id"=>0));
//        if(!empty($mb_user_token_info)){
//            if($mb_user_token_info['push_token'] != $push_token) {
//                $update_data = array();
//                $update_data['user_id'] = 0;
//                $update_data['token'] = "";
//                $update_data['login_time'] = TIMESTAMP;
//                $update_data['client_type'] = "website";
//                $update_data['push_token'] = $_POST['push_token'];
//                $update_data['api_version'] = "1";
//                $result = $this->mb_token_model->update($update_data, array("user_id"=>0));
//            }
//        }else{
//            $mb_user_token_info = array();
//            $mb_user_token_info['user_id'] = 0;
//            $mb_user_token_info['device_key'] = "";
//            $mb_user_token_info['token'] = "";
//            $mb_user_token_info['login_time'] = TIMESTAMP;
//            $mb_user_token_info['client_type'] = "website";
//            $mb_user_token_info['push_token'] = $_POST['push_token'];
//            $mb_user_token_info['api_version'] = "1";
//            $result = $this->mb_token_model->insert($mb_user_token_info);
//        }
        $mb_user_token_info = array();
        $mb_user_token_info['user_id'] = 0;
        $mb_user_token_info['device_key'] = "";
        $mb_user_token_info['token'] = "";
        $mb_user_token_info['login_time'] = TIMESTAMP;
        $mb_user_token_info['client_type'] = "website";
        $mb_user_token_info['push_token'] = $_POST['push_token'];
        $mb_user_token_info['api_version'] = "1";
        $result = $this->mb_token_model->insert($mb_user_token_info);
        return "1";
    }

}
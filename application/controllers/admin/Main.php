<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once 'Admin_controller.php';

class Main extends Admin_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('consumer_user_model');
        $this->load->model('business_user_model');
        $this->load->model('qr_model');
    }

    public function login(){
        $type = get_param_from_request('type');
        $email = get_param_from_request('email');
        if($email == '') output_error("email is required");
        $password = get_param_from_request("password");
        if($password == '') output_error("Password is required");
        $condition = array(
            'user_email'=>$email
        );
        $admin_info = "";
        if ($type == "business") {
            $admin_info = $this->business_user_model->get($condition);
        } else {
            $admin_info = $this->consumer_user_model->get($condition);
        }
        if(empty($admin_info)){
            output_error("Incorrect login info");
        }
        if(md5($password) !== $admin_info['user_password']) {
            output_error("Incorrect login info");
        }
        $token = $this->_create_token(array("id"=>$admin_info['id'], "password"=>$admin_info['user_password'], "user_type"=>$type));
        $result = array('token'=>$token, 'user_info'=>$admin_info);

        output_data($result);
    }
    
    public function register(){
        $type = get_param_from_request('type');
        $business_name = get_param_from_request('business_name');
        $first_name = get_param_from_request('first_name');
        $last_name = get_param_from_request('last_name');
        $email = get_param_from_request('email');
        if($email == '') output_error("email is required");
        $password = get_param_from_request("password");
        if($password == '') output_error("Password is required");
        $condition = array(
            'user_email'=>$email
        );
        $admin_info = "";
        if ($type == "business") {
            $admin_info = $this->business_user_model->get($condition);
        } else {
            $admin_info = $this->consumer_user_model->get($condition);
        }
        
        if(!empty($admin_info)){
            output_error("This user already exist");
        }
        $insert_data = array();
        $insert_id = "";
        if ($type == "business") {
            $insert_data['business_name'] = $business_name;
            $insert_data['user_email'] = $email;
            $insert_data['user_password'] = md5($password);
            $insert_data['user_key_1'] = substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTVWXYZ"), 0, 16);
            $insert_data['user_key_2'] = substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTVWXYZ"), 0, 16);
            $insert_data['balance'] = 0;
            $insert_id = $this->business_user_model->insert($insert_data);
        } else {
            $insert_data['user_first_name'] = $first_name;
            $insert_data['user_last_name'] = $last_name;
            $insert_data['user_email'] = $email;
            $insert_data['user_password'] = md5($password);
            $insert_data['balance'] = 0;
            $insert_data['point'] = 0;
            $insert_id = $this->consumer_user_model->insert($insert_data);
        }

        $admin_info = "";
        if ($type == "business") {
            $admin_info = $this->business_user_model->get($condition);
        } else {
            $admin_info = $this->consumer_user_model->get($condition);
        }

        $token = $this->_create_token(array("id"=>$insert_id, "password"=>$insert_data['user_password'], "user_type"=>$type));
        $result = array('token'=>$token, 'user_info'=>$admin_info);

        output_data($result);
    }
    
    private function _create_token($user_info, $delete_failed=false){

        $token = md5($user_info['id']).md5($user_info['password'] . strval(TIMESTAMP) . strval(rand(0,999999)));
        $condition['user_id'] = $user_info['id'];
        $mb_user_token_info = $this->mb_token_model->get($condition);
        if(!empty($mb_user_token_info)) {
            $update_data = array();
            $update_data['user_id'] = $user_info['id'];
            $update_data['user_type'] = $user_info['user_type'];
            $update_data['token'] = $token;
            $update_data['login_time'] = TIMESTAMP;
            $update_data['api_version'] = $this->api_version;
            $result = $this->mb_token_model->update($update_data, $condition);
        } else {
            $mb_user_token_info = array();
            $mb_user_token_info['user_id'] = $user_info['id'];
            $mb_user_token_info['user_type'] = $user_info['user_type'];
            $mb_user_token_info['token'] = $token;
            $mb_user_token_info['login_time'] = TIMESTAMP;
            $mb_user_token_info['api_version'] = $this->api_version;
            $result = $this->mb_token_model->insert($mb_user_token_info);
        }
        return $token;
    }

    public function update_user_info(){
        $type = get_param_from_request('type');
        $business_name = get_param_from_request('business_name');
        $first_name = get_param_from_request('first_name');
        $last_name = get_param_from_request('last_name');
        $balance = get_param_from_request('balance');
        $email = get_param_from_request('email');
        if($email == '') output_error("email is required");
        $condition = array(
            'user_email'=>$email
        );
        $admin_info = "";
        if ($type == "business") {
            $admin_info = $this->business_user_model->get($condition);
        } else {
            $admin_info = $this->consumer_user_model->get($condition);
        }
        
        if(empty($admin_info)){
            output_error("This user don't exist");
        }
        $insert_data = array();
        $insert_id = "";
        if ($type == "business") {
            $insert_data['business_name'] = $business_name;
            $insert_data['user_email'] = $email;
            $insert_data['balance'] = $balance;
            $insert_id = $this->business_user_model->update($insert_data, $condition);
        } else {
            $insert_data['user_first_name'] = $first_name;
            $insert_data['user_last_name'] = $last_name;
            $insert_data['balance'] = $balance;
            $insert_id = $this->consumer_user_model->update($insert_data, $condition);
        }
        $insert_data['id'] = $insert_id;

        output_data($insert_data);
    }

    public function purchase_gift(){
        $business_id = get_param_from_request('merchant_id');
        $first_key = get_param_from_request('first_key');
        $last_key = get_param_from_request('last_key');
        $date = get_param_from_request('date');
        $amount = get_param_from_request('amount');
        $item_id = get_param_from_request('item_id');
        $description = get_param_from_request('description');
        $lat = get_param_from_request('lat');
        $long = get_param_from_request('long');
        $consumer_id = get_param_from_request('consumer_id');

        $email = get_param_from_request('email');
        if($email == '') output_error("email is required");
        $condition_user = array(
            'user_email'=>$email
        );
        $user_info = "";
        $user_info = $this->consumer_user_model->get($condition_user);
        
        if(empty($user_info)){
            output_error("This user is not exist");
        }

        $condition_business = array(
            'id'=>$business_id,
            'user_key_1'=>$first_key,
            'user_key_2'=>$last_key,
        );
        $business_info = "";
        $business_info = $this->business_user_model->get($condition_business);
        
        if(empty($business_info)){
            output_error("Qr code is not correct.");
        }

        $consumer_data = array();
        $consumer_data['balance'] = $user_info['balance'] - $amount;
        $consumer_data['point'] = $user_info['point'] + ($amount * 10);
        $this->consumer_user_model->update($consumer_data, $condition_user);

        $business_data = array();
        $business_data['balance'] = $business_info['balance'] + $amount;
        $this->business_user_model->update($business_data, $condition_business);

        $insert_data = array();
        $insert_id = "";
        $insert_data['business_id'] = $business_id;
        $insert_data['consumer_id'] = $consumer_id;
        $insert_data['first_key'] = $first_key;
        $insert_data['last_key'] = $last_key;
        $insert_data['date'] = $date;
        $insert_data['amount'] = $amount;
        $insert_data['item_id'] = $item_id;
        $insert_data['lat'] = $lat;
        $insert_data['long'] = $long;
        $insert_data['description'] = $description;
        $insert_id = $this->qr_model->insert($insert_data);

        $insert_data['id'] = $insert_id;

        $user_info = $this->consumer_user_model->get($condition_user);

        output_data($user_info);
    }
}
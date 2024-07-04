<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once 'User_controller.php';

class Main extends User_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('verification_codes_model');
    }

    public function account_activate(){
        $data = $this->data;
        $data['page_title'] = 'Email verification';
        $key = $this->input->get('key');
        $v_id = (int) base64_decode($key);
        $condition = array('id'=>$v_id);
        $verification_info = $this->verification_codes_model->get($condition);
        if(empty($verification_info)){
            $data['message'] = "Verification link has been expired";
            $data['result'] = 'failed';
            $this->page->showpage($data); die;
        }else{
            $user_email = $verification_info['user'];
            $user_info = $this->user_model->get(array('user_email'=>$user_email));
            if(empty($user_info)){
                $data['message'] = "Invalid request (User does not exist)";
                $data['result'] = 'failed';
                $this->page->showpage($data); die;
            }
            $update_data = array(
                'email_verified'=>1
            );
            $this->user_model->update($update_data, array('id'=>$user_info['id']));
        }
        $this->verification_codes_model->delete($condition);
        $data['message'] = "Account email has been verified";
        $data['result'] = 'success';
        $this->page->showpage($data);
    }
    public function reset_password(){
        $data = $this->data;
        $data['page_title'] = 'Forgot password';
        $key = $this->input->get('key');
        $v_id = (int) base64_decode($key);
        $condition = array('id'=>$v_id);
        $verification_info = $this->verification_codes_model->get($condition);
        if(empty($verification_info)){
            $data['message'] = "Reset password link has been expired";
            $data['result'] = 'failed';
            $this->page->showpage($data,'main','main/account_activate'); die;
        }else{
            $user_email = $verification_info['user'];
            $user_info = $this->user_model->get(array('user_email'=>$user_email));
            if(empty($user_info)){
                $data['message'] = "Invalid request (User does not exist)";
                $data['result'] = 'failed';
                $this->page->showpage($data,'main','main/account_activate'); die;
            }
            $update_data = array(
                'user_password'=>$user_info['user_reset_password']
            );
            $this->user_model->update($update_data, array('id'=>$user_info['id']));
        }
        $this->verification_codes_model->delete($condition);
        $data['message'] = "Password has been reset.";
        $data['result'] = 'success';
        $this->page->showpage($data,'main','main/account_activate');
    }
}
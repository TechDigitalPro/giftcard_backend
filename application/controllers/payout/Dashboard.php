<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once 'User_controller.php';

class Dashboard extends User_Controller {
    public function __construct(){
        parent::__construct();
        $this->check_login();
    }

    public function index(){
        $data = $this->data;
        $data['page_title'] = "Dashboard";
        //print_r($data); die;
        $this->page->showpage($data);
    }
    public function set_ip(){
        $data = $this->data;
        $user_info = $data['user'];
        $ip = $this->input->post('ip');
        $nickname = $this->input->post('nickname');
        $condition = array(
            'id'=>$user_info['id']
        );
        $update_data = array(
            'current_ip'=>$ip,
            'current_ip_nickname'=>$nickname
        );
        $this->user_model->update($update_data, $condition);
        $user_info['current_ip'] = $ip;
        $user_info['current_ip_nickname'] = $nickname;
        $this->json_output_data($user_info);
    }

    public function get_user_ips(){
        $data = $this->data;
        $user_info = $data['user'];
        $this->json_output_data($user_info);
    }

    public function login_check(){
        $this->json_output_data("1");
    }
    public function logout(){
        $this->session->unset_userdata(array('user_logged_in','user'));
        redirect(base_url('login'));
    }
}
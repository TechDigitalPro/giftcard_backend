<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_controller extends MY_Controller{
    protected $data;
    protected $user_id;
    protected $user_name;

    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');

        $session_data = $this->session->get_userdata();
        $user_id=@$session_data['user']['id'];
        $user_info = $this->user_model->get(array('id'=>intval($user_id), 'status'=>'1', 'is_deleted'=>0));
        if(empty($user_info)){
            $this->session->unset_userdata(array('user_logged_in','user'));
            $user_info = array();
        }
        $data['user']=$user_info;
        $data['site_setting']=array();
        $data['status_message'] = $this->session->flashdata('status_message');
        $data['error_message'] = $this->session->flashdata('error_message');

        $site_lang = 'english';
        if(isset($session_data['site_lang'])){
            $site_lang = $session_data['site_lang'];
        }
        $data['site_lang'] = $site_lang;
        $this->data=$data;
    }

    protected function check_login($return_url = '')
    {
        if(empty($return_url))
            $return_url = get_self_url();

        if (!$this->session->userdata('user_logged_in')) {
            if($this->check_ajax()){
                return $this->json_output_error('Please login');
            } else{
                redirect(base_url('login').'?return_url='.urlencode($return_url));
                return false;
            }
        }
        $user_info = $this->session->userdata('user');
        $this->user_id = $user_info['id'];
        $this->user_name = $user_info['user_name'];
    }
    public function login_with_user($user_info) {
        $session_data = array(
            'user_logged_in' => true,
            'user' => $user_info
        );
        $this->session->set_userdata('user_logged_in', true);
        $this->session->set_userdata('user', $user_info);
        //$this->session->set_userdata($session_data);
    }
    protected function add_log($log_data){
        $user_info = $this->session->userdata('user');
        if(isset($user_info['id'])){
            $log_data['add_timestamp'] = TIMESTAMP;
            $log_data['ip'] = get_ip();
            $log_data['user_type'] = 'user';
            $log_data['user_id'] = $user_info['id'];
            $log_data['user_name'] = $user_info['user_name'];
            //$this->log_model->insert($log_data);
        }
    }
}
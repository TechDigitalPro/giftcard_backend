<?php
defined('BASEPATH') OR exit('No direct script access allowed');


function output_data($data){
    $result = array('res'=>'success', 'api_result'=>$data);

    echo json_encode($result); die;
}

function output_error($msg = "", $data = array()){
    if(empty($msg)) $msg = "Parameter error";
    $result = array('res'=>'fail', 'err_msg'=>$msg);
    if(!empty($data)){
        $result['data'] = $data;
    } else{
        $result['data'] = array('login_status'=>'1');
    }

    echo json_encode($result); die;
}

class User_controller extends MY_Controller{
    protected $data;
    protected $user_id;
    protected $user_name;

    public function __construct(){
        parent::__construct();
        $this->load->model('user_model');

        $session_data = $this->session->get_userdata();
        $user_id=@$session_data['user']['id'];
        $userInfo = $this->user_model->get_user_detail(intval($user_id), array('user_ips'));
        if(empty($userInfo)){
            $this->session->unset_userdata(array('user_logged_in','user'));
            $userInfo = array();
        }
        //print_r($userInfo); die;
        $data['user']=$userInfo;
        $data['site_setting']=array();
        $data['status_message'] = $this->session->flashdata('status_message');
        $data['error_message'] = $this->session->flashdata('error_message');

        $site_lang = 'english';
        if(isset($session_data['site_lang'])){
            $site_lang = $session_data['site_lang'];
        }
        $data['site_lang'] = $site_lang;
        $this->data = $data;
    }
    protected function check_login($return_url = '')
    {
        if(empty($return_url)){
            $return_url = get_self_url();
        }

        if (!$this->session->userdata('user_logged_in')) {
            if($this->check_ajax())
                return $this->json_output_error('Please login', array(), base_url('login?return_url='.urlencode($return_url)));
            else
                redirect(base_url('login?return_url='.urlencode($return_url)));
        }
        $user_info = $this->session->userdata('user');
        $this->user_id=@$user_info['user_id'];
        $this->user_name = @$user_info['user_name'];
    }
    public function login_with_user($user_info) {
        $session_data = array(
            'user_logged_in' => true,
            'user' => $user_info
        );
        $this->session->set_userdata($session_data);
    }

}
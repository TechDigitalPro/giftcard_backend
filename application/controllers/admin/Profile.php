<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once 'Admin_controller.php';

class Profile extends Admin_controller {
    public function __construct(){
        parent::__construct();
        $this->check_login();
        $this->data['page_title'] = "Edit Profile";
    }

    public function index(){
        $data =$this->data;
        if($this->check_submit()){
            $this->update_profile();
        }else{
            //print_r($data); die;
            $this->page->showpage($data);
        }
    }

    public function check_password(){
        $admin_id=$this->input->post('admin_id');
        $current_password=$this->input->post('current_password');
        $condition=array('admin_id'=>$admin_id);
        $admin_info=$this->admin_model->get($condition);
        if(empty($admin_info)){
            $result=array('status'=>"ERR",'message'=>"Invalid request");
        }else{
            if(sha1($current_password)==$admin_info['admin_password']){
                $result=array('status'=>"1","message"=>"success");
            }else{
                $result=array('status'=>"0","message"=>"Incorrect password");
            }
        }
        echo json_encode($result); die;
    }

    public function update_profile(){
        $this->form_validation->set_rules('admin_name', 'User name', 'trim|required');
        $this->form_validation->set_rules('admin_email', 'customer email', 'trim|required');
        //$this->form_validation->set_rules('captcha', 'Verification code', 'required');
        if ($this->form_validation->run() === FALSE)
            $this->showError('Invalid request');

        $admin_info = $this->admin_model->get(array('admin_id'=>$this->input->post('admin_id')));
        if(empty($admin_info)) $this->showError('Invalid request.');

        if($admin_info['admin_email']!=$this->input->post('admin_email')){
            $check_info = $this->admin_model->get(array('admin_email'=>$this->input->post('admin_email')));
            if(!empty($check_info)) $this->showError('Email already exists.');
        }

        $condition=array('admin_id'=>$this->input->post('admin_id'));
        $cur_date=date('Y-m-d H:i:s');
        $admin_info=array();
        $admin_info['admin_name']=$this->input->post('admin_name');
        $admin_info['admin_email']=$this->input->post('admin_email');
        if(!empty($_POST['admin_password'])){
            $admin_info['admin_password']=md5($_POST['admin_password']);
        }
        $admin_info['admin_updated_at']=$cur_date;

        $upload_filename = $this->upload_file();
        //print_r($upload_filename); die;
        if(!empty($upload_filename)){
            //$upload_file_url=base_url().UPLOADS_PATH."/".vendor_IMAGE_PATH."/".$upload_filename;
            $admin_info['admin_image'] = $upload_filename;
            if(!empty($info['admin_image'])){
                @unlink(FCPATH."/".UPLOADS_PATH."/".PROFILE_IMAGE_PATH."/".$info['admin_image']);
            }
        }

        $this->admin_model->update($admin_info,$condition);
        $this->session->set_flashdata('status_message', "Profile updated successfully");
        redirect(base_url('admin/profile/index'));
    }

    public function upload_file(){ //upload profile image
        $this->load->library('upload');
        $save_path = PROFILE_IMAGE_PATH."";
        list($result, $upload_file_name) = $this->upload->upload_to_local($save_path,'upload_file');

        if(!$result)
            return '';

        //$pic_file = FCPATH.UPLOADS_PATH."/{$save_path}/{$upload_file_name}";

        return $upload_file_name;
    }

}
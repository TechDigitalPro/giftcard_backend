<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once 'Admin_controller.php';

class Setting extends Admin_Controller {
    public function __construct(){
        parent::__construct();
        $this->check_login();
        $this->load->model('site_settings_model');
        $this->load->model('payment_method_model');
    }
    public function index(){
        $data = $this->data;
        $setting_info=$this->site_settings_model->get_site_setting();
        if(empty($setting_info)){
            $setting_info=array();
        }
        $paypal_info=$this->payment_method_model->get_setting();

        $data['setting_info']=$setting_info;
        $data['paypal_info']=$paypal_info;

        if($this->check_submit()){
            $this->update_setting();
        }
        $data['page_title'] = "Setting";
        $this->page->showpage($data);
    }

    public function update_setting(){
        $update_data = array(); // from post data
        $update_data['app_name'] = $_POST['app_name'];

        list($upload_result, $upload_filename) = $this->upload_file("upload_logo_file");
        if($upload_result){
            $update_data['logo_img'] = $upload_filename;
        }
        list($upload_result, $upload_filename) = $this->upload_file("upload_fav_file");
        if($upload_result){
            $update_data['favicon_img'] = $upload_filename;
        }
        $update_data['stripe_publishable_key'] = $_POST['stripe_publishable_key'];
        $update_data['stripe_secret_key'] = $_POST['stripe_secret_key'];
        foreach($update_data as $key => $value){
            $setting_info = array($key=>$value);
            $this->site_settings_model->update_site_setting($setting_info);
        }
        $this->session->set_flashdata('status_message', "Setting information has been updated successfully");
        redirect(base_url('admin/setting/index'));
    }
    public function update_paypal(){

        $condition=array('name'=>"paypal");
        $update_data = array();
        $update_data['merchant_email'] = $_POST['merchant_email'];
        $update_data['environment'] = $_POST['environment'];
        $update_data['status'] = ((isset($_POST['status']) && !empty($_POST['status']))?'1':'0');
        list($upload_result, $upload_filename) = $this->upload_file("upload_image_file");
        if($upload_result){
            $update_data['image'] = $upload_filename;
        }

        $this->payment_method_model->update($update_data,$condition);
        $this->session->set_flashdata('status_message', "Paypal information has been updated successfully");
        redirect(base_url('admin/setting/index'));
    }

    public function upload_file($file){ //upload image
        $this->load->library('upload');
        $save_path = IMAGE_PATH;
        list($result, $upload_file_name) = $this->upload->upload_to_local($save_path,$file);
        return array($result, $upload_file_name);
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once 'Admin_controller.php';

class Notification extends Admin_Controller {
    public function __construct(){
        parent::__construct();
        $this->check_login();
        $this->load->model('user_model');
        $this->load->model('notification_model');
        $this->load->model('mb_token_model');
        $this->load->model('category_model');
    }
    public function index(){
        $data = $this->data;
        $limit = 10; //item count per page
        $segment_index = 4;
        $sql = "select * from ".USER_TB." where is_deleted = 0";
        if(isset($_GET['keyword1']) && !empty($_GET['keyword1'])) {
            $keyword1 = "%".$_GET['keyword1']."%";
            $sql .= " and (user_name like '".$keyword1."' or country_name like '".$keyword1."' or user_email like '".$keyword1."')";
        }
        $query_cnt = $this->db->query($sql);
        $total = $query_cnt->num_rows();
        $sql.=" order by id asc";

        $page = 1;
        if(@$this->uri->segment($segment_index))
        {
            $page = (int)$this->uri->segment($segment_index);
        }
        $offset = ($page - 1)*$limit;
        $sql .= " limit ".$offset.",".$limit;
        $query = $this->db->query($sql);
        $list = $query->result_array();
        if(empty($list)) $list = array();


        $config['base_url'] = base_url('admin/notification/index');
        $config['total_rows'] = $total;
        $config['per_page'] = $limit;
        $config['uri_segment'] = $segment_index;

        $data['list']=$list;
        $data['show_pagination'] = pagination($config);
        $data['page_title'] = "Notification Management";
        $this->page->showpage($data);
    }

    public function update_setting(){
        $sql = "Select * from users where is_deleted = '0'";

        $query = $this->db->query($sql);
        $rslt = $query->result_array();
        if(count($rslt) > 0){
            $user_ids = array();
            foreach($rslt as $other_user_info){
                $user_ids[] = $other_user_info['id'];
            }
            $notification_data = array(
                'type'=>'admin',
                'notification_title'=>$_POST['title'],
                'notification_content'=>$_POST['content'],
//                'user_id'=>$user_id,
//                'post_id'=>$ad_id,
                'user_image'=>""
            );

            $this->_notify_to_users($user_ids, $notification_data);

        }

        $this->session->set_flashdata('status_message', "Notification has been sent to all users successfully");
        $message = "Notification has been sent successfully";
        $this->json_output_data(1, $message);
    }

    public function send_notification_to_user(){
        $notification_data = array(
            'type'=>'admin',
            'notification_title'=>$_POST['title'],
            'notification_content'=>$_POST['message'],
//                'user_id'=>$user_id,
//                'post_id'=>$ad_id,
            'user_image'=>""
        );

        $this->_notify_to_all($_POST['user_ids'], $notification_data);
        $message = "Notification has been sent to all users successfully";
        $this->json_output_data(1, $message);
        redirect(base_url('admin/notification/index'));
    }
    public function upload_file($file){ //upload image
        $this->load->library('upload');
        $save_path = IMAGE_PATH;
        list($result, $upload_file_name) = $this->upload->upload_to_local($save_path,$file);
        return array($result, $upload_file_name);
    }

    private function _notify_to_users($user_ids, $notification_data = array()){
        if(empty($user_ids)) return false;
        $player_ids = array();
        $user_ids_str = implode(",", $user_ids);
        $sql = "select * from ".MB_TOKEN_TB." where user_id in (".$user_ids_str.")";
        //$this->add_log($sql);
        $qry = $this->db->query($sql);
        $token_list = $qry->result_array();
        if(count($token_list) > 0){
            foreach($token_list as $token_info){
                if(!empty($token_info['push_token'])){
                    $player_ids[]=$token_info['push_token'];
                }
            }
        }
        if(!empty($player_ids)){
            send_onesignal_notification($notification_data, $player_ids);
        }
        $notification_type = (isset($notification_data['type']) ? $notification_data['type'] : "");
        $notification_row = array(
            'type'=>$notification_type,
            'title'=>$notification_data['notification_title'],
            'content'=>$notification_data['notification_content'],
            'data'=>json_encode($notification_data),
            'status'=>0,
            'add_timestamp'=>TIMESTAMP
        );
        foreach($user_ids as $user_id){
            $notification_row['receiver_id'] = $user_id;
            $this->notification_model->insert($notification_row);
        }

    }
    private function _notify_to_all($user_ids_str, $notification_data = array()){
        if(empty($user_ids_str)) return false;
        $player_ids = array();
        $sql = "select * from ".MB_TOKEN_TB." where user_id in (".$user_ids_str.")";
        //$this->add_log($sql);
        $qry = $this->db->query($sql);
        $token_list = $qry->result_array();
        if(count($token_list) > 0){
            foreach($token_list as $token_info){
                if(!empty($token_info['push_token'])){
                    $player_ids[]=$token_info['push_token'];
                }
            }
        }
        if(!empty($player_ids)){
            send_onesignal_notification($notification_data, $player_ids);
        }
        $notification_type = (isset($notification_data['type']) ? $notification_data['type'] : "");
        $notification_row = array(
            'type'=>$notification_type,
            'title'=>$notification_data['notification_title'],
            'content'=>$notification_data['notification_content'],
            'data'=>json_encode($notification_data),
            'status'=>0,
            'add_timestamp'=>TIMESTAMP
        );
        $user_ids = explode(',', $user_ids_str);
        foreach($user_ids as $user_id){
            $notification_row['receiver_id'] = $user_id;
            $this->notification_model->insert($notification_row);
        }

    }
}
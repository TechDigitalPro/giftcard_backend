<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model {
    public function __construct() {
        parent::__construct(USER_TB);
    }
    public function check_duplicate_account($account_info){
        if(isset($account_info['user_email']) && !empty($account_info['user_email'])){
            $sql = "select id from ".USER_TB." where user_email = '".$account_info['user_email']."' and is_deleted = 0 and id<>".$account_info['id'];
            $query = $this->db->query($sql);
            if($query->num_rows() > 0){
                return array(true, "Email address is already taken");
            }
        }
        if(isset($account_info['user_phone'])){
            $sql = "select id from ".USER_TB." where user_phone = '".$account_info['user_phone']."' and is_deleted = 0 and id<>".$account_info['id'];
            $query = $this->db->query($sql);
            if($query->num_rows() > 0){
                return array(true, "Phone number is already taken");
            }
        }
        if(isset($account_info['user_name']) && !empty($account_info['user_name'])){
            $sql = "select id from ".USER_TB." where user_name = '".$account_info['user_name']."' and is_deleted = 0 and id<>".$account_info['id'];
            $query = $this->db->query($sql);
            if($query->num_rows() > 0){
                return array(true, "Username is already taken");
            }
        }
        return array(false, "success");
    }
    
}
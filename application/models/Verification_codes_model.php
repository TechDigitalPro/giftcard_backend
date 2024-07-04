<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Verification_codes_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct('verification_codes');
    }

    public function add_verification_code($verification_info){
        $verification_info['add_timestamp']=TIMESTAMP;
        $condition=array('user'=>$verification_info['user'],'type'=>$verification_info['type']);
        $verification_record = $this->get($condition);
        if(empty($verification_record)){
            $id = $this->insert($verification_info);
        }else{
            $update_data=array('code'=>$verification_info['code'],'add_timestamp'=>$verification_info['add_timestamp']);
            $this->update($update_data,$condition);
            $id = $verification_record['id'];
        }
        return $id;
    }

    public function delete_verification_code($verification_info){
        $condition=array('user'=>$verification_info['user'],'type'=>$verification_info['type']);
        $this->delete($condition);
    }

}
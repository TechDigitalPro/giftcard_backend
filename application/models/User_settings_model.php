<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_settings_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct(USER_SETTING_TB);
    }

    public function get_user_setting($user_id = 1){
        $where = array('user_id'=>$user_id);
        $settingInfo = array();
        $setting_list = $this->get_all($where);
        if(empty($setting_list)) $setting_list = array();
        foreach($setting_list as $item){
            $key = $item['option_name'];
            $value = $item['option_value'];
            $settingInfo[$key] = $value;
        }
        return $settingInfo;
    }
    public function update_user_setting($update_data, $user_id = 0){
        $where = array('user_id'=>$user_id);
        $settingInfo = array();
        foreach($update_data as $key => $value){
            $settingInfo['option_name'] = $key;
            $settingInfo['option_value'] = $value;
        }
        $where['option_name'] = $settingInfo['option_name'];
        $item = $this->get($where);
        if(empty($item)){
            $settingInfo['user_id'] = $where['user_id'];
            $settingInfo['created_at'] = time();
            $this->insert($settingInfo);
        }else{
            unset($settingInfo['option_name']);
            $settingInfo['created_at'] = time();
            $this->update($settingInfo,$where);
        }
        return true;
    }
    public function delete_setting($key, $user_id){
        $where = array('user_id'=>$user_id, 'option_name'=>$key);
        $this->delete($where);
    }
}
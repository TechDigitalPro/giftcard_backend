<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site_settings_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct('site_settings');
    }

    public function get_site_setting($where = array('user_id'=>1)){
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
    public function update_site_setting($update_data, $where = array('user_id'=>1)){
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
    public function delete_setting($where){
        $this->delete($where);
    }
}
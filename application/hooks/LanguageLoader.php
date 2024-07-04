<?php
class LanguageLoader
{
    function initialize() {
        $ci =& get_instance();
        $ci->load->helper('language');
        $siteLang = $ci->session->userdata('site_lang');
        if (empty($siteLang)) {
            $siteLang='english';
        }
        $current_directory=$ci->router->fetch_directory();
        $current_directory=strtolower($current_directory);
        if($current_directory=='admin'){
            $langFile='admin/';
        }elseif ($current_directory=='customer/'){
            $langFile='customer';
        }elseif ($current_directory=='publisher/'){
            $langFile='publisher';
        }else{
            $langFile='home';
        }
        $ci->lang->load($langFile,$siteLang);
    }
}
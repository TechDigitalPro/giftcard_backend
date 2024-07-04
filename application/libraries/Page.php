<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page {

    function __construct()
    {

    }
    // --------------------------------------------------------------------
    function showpage($data=array(), $layout='', $view='')
    {
        $base_url=base_url();
        $theme_front_url=base_url().'themes/home/';
        $theme_user_url=base_url().'themes/user/';
        $theme_admin_url=base_url().'themes/admin/';
        $theme_adv_login_url = $theme_user_url;
        $view_file_path="";
        $CI=get_instance();
        $current_directory=$CI->router->fetch_directory();
        $current_c_directory = $current_directory;
        if(empty($current_directory)){
            $current_directory = "home/";
        }

        $current_controller=strtolower($CI->router->fetch_class());
        $current_method=$CI->router->fetch_method();
        $current_uri=strtolower($current_directory.$current_controller.'/'.$current_method);
        $base_dir=FCPATH.'application/views/'.$current_directory;
        if(empty($view)){
            $view_file_path=$base_dir.$current_controller.'/'.$current_method.'.php';
        }else{
            $view_file_path=$base_dir.$view.'.php';
        }
        if(!file_exists($view_file_path)){
            $view_file_path=$base_dir.'index.php';
        }
        if($layout==''){
            $layout=$base_dir.'layout/main_layout.php';
        }else{
            $layout=$base_dir.'layout/'.$layout.'_layout.php';
        }
        include($layout);
        exit;
    }

    // --------------------------------------------------------------------
    function message($data=array(), $view='')
    {
        $base_url=base_url();
        $theme_url=base_url().'themes/default/';
        $shop_theme_url=base_url().'themes/ecommerce/';
        $wap_theme_default_url=base_url().'themes/wap_default/';
        $wap_theme_adp_url=base_url().'themes/wap_adp/';
        $thumb_theme_url=base_url().'themes/thumb/';
        $wap_theme_owner_url=base_url().'themes/wap_owner/';
        $view_file_path="";
        $CI=get_instance();
        $directory=$CI->router->fetch_directory();
        $base_dir=FCPATH.'application/views/'.$directory;
        if(empty($view)){
            $view='msg';
        }
        $view_file_path=$base_dir.$view.'.php';
        if(!file_exists($view_file_path)){
            $view_file_path=$base_dir.'index.php';
        }
        include($base_dir.'layout/msg_layout.php');
        exit;
    }

}
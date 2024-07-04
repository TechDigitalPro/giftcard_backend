<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 10/27/2017
 * Time: 11:32 AM
 */
class LanguageSwitcher extends CI_Controller
{
    public function __construct() {
        parent::__construct();
    }

    function switchLang($language = "") {

        $language = ($language != "") ? $language : "english";
        $this->session->set_userdata('site_lang', $language);

        redirect($_SERVER['HTTP_REFERER']);

    }
}
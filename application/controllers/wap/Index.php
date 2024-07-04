<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends CI_Controller{
    private $data;
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $data=$this->data;
        $data['base_url']=base_url();
        $data['theme_url']=base_url()."themes/default/one-pages/";
        $this->load->view('wap/index',$data);
    }
}
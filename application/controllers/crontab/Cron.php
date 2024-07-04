<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once 'Crontab_controller.php';
/*
 * cron url
 * http://192.168.1.72/000_work/higher_level_mlm/Source/crontab/cron/index
 *
 * */
class Cron extends Crontab_controller {

    public function __construct() {
        parent::__construct();
    }

    public function index(){
        
    }
}
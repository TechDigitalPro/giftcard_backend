<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once 'Crontab_controller.php';
require_once APPPATH . 'third_party/validate-payment/autoload.php';
use ReceiptValidator\GooglePlay\Validator;

/*
 * cron url
 * http://192.168.1.72/000_work/higher_level_mlm/Source/crontab/day/index
 *
 * */
class Day extends Crontab_controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->add_log('day cron start');

    }
}

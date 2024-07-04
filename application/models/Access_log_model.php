<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Access_log_model extends MY_Model {

    public function __construct() {
        parent::__construct(ACCESS_LOG_TB);
    }
}
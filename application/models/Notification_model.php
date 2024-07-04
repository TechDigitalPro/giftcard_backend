<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_model extends MY_Model {

    public function __construct() {
        parent::__construct(NOTIFICATION_TB);
    }

}
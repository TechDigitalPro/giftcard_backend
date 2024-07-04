<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Consumer_user_model extends MY_Model {
    public function __construct() {
        parent::__construct(USER_CONSUMER_TB);
    }
    
}
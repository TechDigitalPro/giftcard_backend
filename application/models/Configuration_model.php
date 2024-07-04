<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Configuration_model extends MY_Model{

    public function __construct() {
        parent::__construct(CONFIGURATION_TB);
    }
}
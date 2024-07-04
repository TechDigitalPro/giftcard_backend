<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_model extends MY_Model{

    public function __construct() {
        parent::__construct(PROJECT_TB);
    }
}
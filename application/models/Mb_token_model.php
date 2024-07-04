<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mb_token_model extends MY_Model{
    public function __construct() {
        parent::__construct(MB_TOKEN_TB);
    }
}
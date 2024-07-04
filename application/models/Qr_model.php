<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Qr_model extends MY_Model {
    public function __construct() {
        parent::__construct(QR_INFO_TB);
    }
    
}
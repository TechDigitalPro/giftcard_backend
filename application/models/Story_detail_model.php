<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Story_detail_model extends MY_Model{

    public function __construct() {
        parent::__construct(STORY_DETAIL_TB);
    }
}
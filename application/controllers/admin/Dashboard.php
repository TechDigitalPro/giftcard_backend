<?php
defined('BASEPATH') or exit('No direct script access allowed');

include_once 'Admin_controller.php';

class Dashboard extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        // $this->check_login();
        $this->load->model('story_model');
        $this->load->model('story_description_model');
        $this->load->model('addiopizzo_model');
    }

    public function get_dashboard_info() {
        $story_count = 0;
        $isop_count = 0;
        $ipicciridi_count = 0;
        $ilegal_count = 0;
        $extra_count = 0;
        $canzoni_count = 0;
        $pizzo_count = 0;
        $story_list = $this->story_model->get_all(array('type'=>"story", 'status' => 1));
        if (!empty($story_list)) {
            $story_count = sizeof($story_list);
        }
        
        $isop_list = $this->story_model->get_all(array('type'=>"isop", 'status' => 1));
        if (!empty($isop_list)) {
            $isop_count = sizeof($isop_list);
        }

        $ipicciridi_list = $this->story_model->get_all(array('type'=>"ipicciridi", 'status' => 1));
        if (!empty($ipicciridi_list)) {
            $ipicciridi_count = sizeof($ipicciridi_list);
        }

        $ilegal_list = $this->story_model->get_all(array('type'=>"ilegal", 'status' => 1));
        if (!empty($ilegal_list)) {
            $ilegal_count = sizeof($ilegal_list);
        }
        $pizzo_list = $this->addiopizzo_model->get_all(array('status' => 1));
        if (!empty($pizzo_list)) {
            $pizzo_count = sizeof($pizzo_list);
        }

        $extra_list = $this->story_model->get_all(array('type'=>"extra", 'status' => 1));
        if (!empty($extra_list)) {
            $extra_count = sizeof($extra_list);
        }

        $canzoni_list = $this->story_model->get_all(array('type'=>"canzoni", 'status' => 1));
        if (!empty($canzoni_list)) {
            $canzoni_count = sizeof($canzoni_list);
        }

        output_data(array("story"=>$story_count, "isop"=>$isop_count, "ipicciridi"=>$ipicciridi_count, "ilegal"=>$ilegal_count,"pizzo"=>$pizzo_count, "extra"=>$extra_count, "canzoni"=>$canzoni_count));
    }
    
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once 'Hook_controller.php';

class InAppPurchase extends Hook_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('subscription_plan_model');
        $this->load->model('subscription_model');
        $this->load->model('transaction_model');
    }

    public function purchase_notify(){
        $post_input = @file_get_contents("php://input");
        
        file_put_contents("inapppurchase_log.txt", print_r($post_input, 1));
        $this->add_log("In App Purchase log----".$post_input);
        
    }


}
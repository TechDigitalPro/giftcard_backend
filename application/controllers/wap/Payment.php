<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'third_party/paypal/paypal.php';
require_once APPPATH.'third_party/stripe-php/init.php';

class Payment extends MY_Controller{
    private $data;
    public function __construct(){
        parent::__construct();
        $this->load->model('payment_method_model');
        $this->load->model('payment_history_model');
        $this->load->model('payment_deposit_model');
        $this->load->model('user_model');
    }



    private function _update_ref_user_info($customer_id){
        return true;
        $user_info = $this->customer_model->get(array('customer_id'=>$customer_id));
        if(!empty($user_info) && isset($user_info['ref_id']) && !empty($user_info['ref_id'])){
            $condition = array('customer_id'=>$user_info['ref_id']);
            $ref_user_info = $this->customer_model->get($condition);
            if(!empty($ref_user_info)){
                $subscription_info = $this->subscription_model->get(array('customer_id'=>$user_info['ref_id'], 'status'=>'1'));
                if(empty($subscription_info)){
                    $subscription_info = $this->createTrialSubscription($user_info['ref_id'], 0);
                }
                $end_time = intval($subscription_info['end_time']);
                $end_time+=intval(C('days_referral')) * 86400;
                $update_data = array('end_time'=>$end_time);
                $where = array('id'=>$subscription_info['id']);
                $this->subscription_model->update($update_data, $where);
            }
        }
    }
    private function createTrialSubscription($customer_id, $expired_days){
        $update_data=array();
        $update_data['customer_id'] = $customer_id;
        $update_data['start_time'] = TIMESTAMP;
        $update_data['end_time'] = $update_data['start_time'] + intval($expired_days) * 86400;
        $update_data['status'] = '1';
        $subscribe_id = $this->subscription_model->insert($update_data);
        $api_result = array('subscribe_id'=>$subscribe_id);
        $update_data['id'] = $subscribe_id;
        return $update_data;
    }
    private function _update_subscription($subscribe_id,$plan_id){
        $condition = array('id'=>$subscribe_id);
        $condition1 = array('id'=>$plan_id);
        $plan_info = $this->plan_model->get($condition1);
        $subscription_info = $this->subscription_model->get($condition);
        if(!empty($subscription_info) && !empty($plan_info)){
            $update_data = array('status'=>'1');
            $update_data['plan_id'] = $plan_id;
            $update_data['period'] = $plan_info['plan_duration'];
            $update_data['paid_amount'] = decimal_format(floatval($plan_info['plan_original_price']) * (100 - floatval($plan_info['plan_discount'])) / 100);

            if($subscription_info['status'] == '1'){
                $start_time = intval($subscription_info['end_time']);
                if($start_time < TIMESTAMP){
                    $start_time = TIMESTAMP;
                    $update_data['start_time'] = $start_time;
                }
                $update_data['end_time'] = $start_time + intval($plan_info['plan_duration']) * 30 * 86400;
                $update_data['paid_amount'] += $subscription_info['paid_amount'];
            }
            else{
                $update_data['start_time'] = time();
                $update_data['end_time'] = $update_data['start_time'] + intval($update_data['period']) * 30 * 86400;
            }
            $this->subscription_model->update($update_data, $condition);
        }
        return true;
    }

    /******************************paypal payment********************************/
    public function paypal_success(){
        $this->add_log("Paypal payment success----".json_encode($_POST));
        $cur_time=time();
        $payment_amount=!isset($_POST['payment_gross'])?$_POST['amt']:$_POST['payment_gross'];
        $payment_fee=!isset($_POST['payment_fee'])?0:$_POST['payment_fee'];
        $payment_currency=!isset($_POST['mc_currency'])?$_POST['cc']:$_POST['mc_currency'];
        $payment_status=!isset($_POST['payment_status'])?$_POST['st']:$_POST['payment_status'];
        $txn_id=!isset($_POST['txn_id'])?$_POST['tx']:$_POST['txn_id'];
        $custom=!isset($_POST['custom'])?$_POST['cm']:$_POST['custom'];
        $custom_arr=explode('|',$custom);
        //print_r($custom_arr); die;
        $ip_address=$custom_arr[0];
        $customer_id=$custom_arr[1];
        $subscribe_id=$custom_arr[2];
        $environment=$custom_arr[3];
        $payment_record=array(
            'user_id'=>$customer_id,
            'deposit_request_id'=>$subscribe_id,
            'gateway'=>'paypal',
            'payment_amount'=>floatval($payment_amount),
            'payment_network_amount'=>floatval($payment_amount)-floatval($payment_fee),
            'payment_fee'=>floatval($payment_fee),
            'payment_currency'=>$payment_currency,
            'txn_id'=>$txn_id,
            'payment_status'=>$payment_status,
            'created_at'=>$cur_time,
            'detail'=>serialize($_POST),
            'ip_address'=>$ip_address,
            'environment'=>$environment
        );

        $ipn = new PaypalIPN();
        if($payment_record['environment'] == '0'){
            $ipn->useSandbox();
        }
        $ipn->usePHPCerts();
        $verified = $ipn->verifyIPN();
        //$verified = true;
        if ($verified) {
            $this->add_log("paypal ipn validation success");
        }else{
            $this->add_log("paypal ipn validation failed");
            return false;
        }
        $condition = array('txn_id'=>$payment_record['txn_id']);
        $payment_check_info = $this->payment_history_model->get($condition);
        if(!empty($payment_check_info)){
            if($payment_check_info['payment_status']=='Completed'){
                redirect(base_url('user/dashboard/index'));
                return true;
            }else{
                $this->payment_history_model->update($payment_record,$condition);
            }
        }else{
            $this->payment_history_model->insert($payment_record);
        }
        if($payment_record['payment_status']=='Completed'){
            $condition = array('id'=>$subscribe_id);
            $subscription_info = $this->payment_deposit_model->get($condition);
            if(!empty($subscription_info)){
                $this->payment_deposit_model->update(array("status" => 1),array("id"=>$subscribe_id));
            }
        }
        $html_file_path = FCPATH.DS.'payment'.DS.'paypal_'.$subscribe_id.'.html';
        if(file_exists($html_file_path)){
            @unlink($html_file_path);
        }
        redirect(base_url('user/dashboard/index'));
    }
    public function paypal_notify(){
        $this->add_log("Paypal payment success(notify)----".json_encode($_POST));
        $cur_time=time();
        $payment_amount=!isset($_POST['payment_gross'])?$_POST['amt']:$_POST['payment_gross'];
        $payment_fee=!isset($_POST['payment_fee'])?0:$_POST['payment_fee'];
        $payment_currency=!isset($_POST['mc_currency'])?$_POST['cc']:$_POST['mc_currency'];
        $payment_status=!isset($_POST['payment_status'])?$_POST['st']:$_POST['payment_status'];
        $txn_id=!isset($_POST['txn_id'])?$_POST['tx']:$_POST['txn_id'];
        $custom=!isset($_POST['custom'])?$_POST['cm']:$_POST['custom'];
        $custom_arr=explode('|',$custom);
        //print_r($custom_arr); die;
        $ip_address=$custom_arr[0];
        $customer_id=$custom_arr[1];
        $subscribe_id=$custom_arr[2];
        $environment=$custom_arr[3];
        $payment_record=array(
            'user_id'=>$customer_id,
            'deposit_request_id'=>$subscribe_id,
            'gateway'=>'paypal',
            'payment_amount'=>floatval($payment_amount),
            'payment_network_amount'=>floatval($payment_amount)-floatval($payment_fee),
            'payment_fee'=>floatval($payment_fee),
            'payment_currency'=>$payment_currency,
            'txn_id'=>$txn_id,
            'payment_status'=>$payment_status,
            'created_at'=>$cur_time,
            'detail'=>serialize($_POST),
            'ip_address'=>$ip_address,
            'environment'=>$environment
        );
        $ipn = new PaypalIPN();
        if($payment_record['environment'] == '0'){
            $ipn->useSandbox();
        }
        $ipn->usePHPCerts();
        $verified = $ipn->verifyIPN();
        if ($verified) {
            $this->add_log("paypal ipn validation success");
        }else{
            $this->add_log("paypal ipn validation failed");
            return false;
        }
        $condition = array('txn_id'=>$payment_record['txn_id']);
        $payment_check_info = $this->payment_history_model->get($condition);
        if(!empty($payment_check_info)){
            if($payment_check_info['payment_status']=='Completed'){
                redirect(base_url('user/dashboard/index'));
                return true;
            }else{
                $this->payment_history_model->update($payment_record,$condition);
            }
        }else{
            $this->payment_history_model->insert($payment_record);
        }
        if($payment_record['payment_status']=='Completed'){
            $condition = array('id'=>$subscribe_id);
            $subscription_info = $this->payment_deposit_model->get($condition);
            if(!empty($subscription_info)){
                $this->payment_deposit_model->update(array("status" => 1),array("id"=>$subscribe_id));
            }
        }

        $html_file_path = FCPATH.DS.'payment'.DS.'paypal_'.$subscribe_id.'.html';
        if(file_exists($html_file_path)){
            @unlink($html_file_path);
        }
        echo 'success';
        redirect(base_url('user/dashboard/index'));
    }
    public function paypal_cancel($subscribe_id){
        $this->add_log("Paypal payment canceled----".json_encode($_POST));
        $condition = array('id'=>$subscribe_id);
        $this->payment_deposit_model->delete($condition);
        //$this->subscription_model->delete($update_data,$condition);
        $html_file_path = FCPATH.DS.'payment'.DS.'paypal_'.$subscribe_id.'.html';
        if(file_exists($html_file_path)){
            @unlink($html_file_path);
        }
        redirect(base_url('user/dashboard/index'));
    }


    /******************************stripe payment********************************/
    public function stripe_process(){
        $this->add_log("Stripe process log----started!");
        $stripe_gateway_info = $this->payment_method_model->get_payment_gateway_info(array('name'=>'stripe'));
        $customer_id = $_GET['customer_id'];
        $subscribe_id = $_GET['subscribe_id'];
        $plan_id = $_GET['plan_id'];
        $amount = $_GET['amount'];
        $token = $_GET['token'];
        $email = $_GET['email'];
        $client_ip = $_GET['client_ip'];
        try{
            $stripe_secret_key = $stripe_gateway_info['environment'] == '1' ? $stripe_gateway_info['attributes']['secret_key'] : $stripe_gateway_info['attributes']['test_secret_key'];
            \Stripe\Stripe::setApiKey($stripe_secret_key);
            $customer = \Stripe\Customer::create([
                'email' => $email,
                'source'  => $token,
                'metadata' => array(
                    'customer_id'=>$customer_id,
                    'subscribe_id'=>$subscribe_id,
                    'plan_id'=>$plan_id,
                    'client_ip'=>$client_ip
                )
            ]);
            //print_r($customer);
            $charge = \Stripe\Charge::create([
                'customer' => $customer->id,
                'amount'   => intval($amount),
                'currency' => 'usd'
            ]);
            $this->add_log("Stripe process log----".json_encode($charge));
            if ($charge->paid == 1 && $charge->status == 'succeeded') {
                $payment_amount = floatval($amount)/100;
                $payment_record=array(
                    'customer_id'=>$customer_id,
                    'subscribe_id'=>$subscribe_id,
                    'plan_id'=>$plan_id,
                    'gateway'=>'stripe',
                    'payment_amount'=>$payment_amount,
                    'payment_network_amount'=>$payment_amount,
                    'payment_fee'=>0,
                    'payment_currency'=>'usd',
                    'txn_id'=>$charge->id,
                    'payment_status'=>"Completed",
                    'created_at'=>time(),
                    'detail'=>serialize($charge),
                    'ip_address'=>$client_ip,
                    'environment'=>$stripe_gateway_info['environment']
                );
                $condition = array('txn_id'=>$payment_record['txn_id']);
                $payment_check_info = $this->payment_history_model->get($condition);
                if(!empty($payment_check_info)){
                    if($payment_check_info['payment_status']=='Completed'){
                        return true;
                    }else{
                        $this->payment_history_model->update($payment_record,$condition);
                    }
                }else{
                    $this->payment_history_model->insert($payment_record);
                }
                if($payment_record['payment_status']=='Completed'){
                    $condition = array('id'=>$subscribe_id);
                    $subscription_info = $this->subscription_model->get($condition);
                    if(!empty($subscription_info)){
                        $this->_update_subscription($subscribe_id,$plan_id);
                 //       $this->_update_ref_user_info($customer_id);
                    }
                }
            }
            else {
                $condition = array('id'=>$subscribe_id);
                //$this->subscription_model->delete($condition);
            }
            //print_r($charge); die;
        }catch (Exception $e){
            $body = $e->getJsonBody();
            $err  = $body['error'];
            $error_message=$err['message'];
            echo $error_message;
            $condition = array('id'=>$subscribe_id);
            //$this->subscription_model->delete($condition);
        }
        $html_file_path = FCPATH.DS.'payment'.DS.'stripe_'.$subscribe_id.'.html';
        if(file_exists($html_file_path)){
            @unlink($html_file_path);
        }

    }
    public function stripe_cancel(){
        $customer_id = $_GET['customer_id'];
        $subscribe_id = $_GET['subscribe_id'];
        $condition = array('id'=>$subscribe_id);
        //$this->subscription_model->delete($condition);
        $html_file_path = FCPATH.DS.'payment'.DS.'stripe_'.$subscribe_id.'.html';
        if(file_exists($html_file_path)){
            @unlink($html_file_path);
        }
    }
    public function stripe_notify(){
        $stripe_gateway_info = $this->payment_method_model->get_payment_gateway_info(array('name'=>'stripe'));
        $post_input = @file_get_contents("php://input");
        $this->add_log("Stripe payment log----".$post_input);
        $event_data=json_decode($post_input,true);
        $environment = $event_data['livemode']?"1":"0";
        $event_type=$event_data['type'];
        $subscribe_id = 0;
        switch($event_type){
            case "charge.succeeded":
                $payment_data = $event_data['data']['object'];
                $customer_id = $payment_data['customer'];
                try{
                    $stripe_secret_key = $environment == '1' ? $stripe_gateway_info['attributes']['secret_key'] : $stripe_gateway_info['attributes']['test_secret_key'];
                    \Stripe\Stripe::setApiKey($stripe_secret_key);
                    $customer = \Stripe\Customer::retrieve($customer_id);
                    $customer_data = $customer->metadata;
                    $customer_id = $customer_data->customer_id;
                    $subscribe_id = $customer_data->subscribe_id;
                    $plan_id = $customer_data->plan_id;
                    $client_ip = $customer_data->client_ip;
                    if($payment_data['status'] =='succeeded'){
                        $amount = $payment_data['amount'];
                        $payment_amount = floatval($amount)/100;
                        $payment_record=array(
                            'customer_id'=>$customer_id,
                            'subscribe_id'=>$subscribe_id,
                            'plan_id'=>$plan_id,
                            'gateway'=>'stripe',
                            'payment_amount'=>$payment_amount,
                            'payment_network_amount'=>$payment_amount,
                            'payment_fee'=>0,
                            'payment_currency'=>'usd',
                            'txn_id'=>$payment_data['id'],
                            'payment_status'=>"Completed",
                            'created_at'=>time(),
                            'detail'=>$post_input,
                            'ip_address'=>$client_ip,
                            'environment'=>$environment
                        );

                        $condition = array('txn_id'=>$payment_record['txn_id']);
                        $payment_check_info = $this->payment_history_model->get($condition);
                        if(!empty($payment_check_info)){
                            if($payment_check_info['payment_status']=='Completed'){
                                return true;
                            }else{
                                $this->payment_history_model->update($payment_record,$condition);
                            }
                        }else{
                            $this->payment_history_model->insert($payment_record);
                        }
                        if($payment_record['payment_status']=='Completed'){
                            $condition = array('id'=>$subscribe_id);
                            $subscription_info = $this->subscription_model->get($condition);
                            if(!empty($subscription_info)){
                                $this->_update_subscription($subscribe_id,$plan_id);
                //                $this->_update_ref_user_info($customer_id);
                            }
                        }
                        echo 'success';
                    }else{
                        $condition = array('id'=>$subscribe_id);
                        //$this->subscription_model->delete($condition);
                    }
                }catch (Exception $e){
                    $condition = array('id'=>$subscribe_id);
                    //$this->subscription_model->delete($condition);
                }
                $html_file_path = FCPATH.DS.'payment'.DS.'stripe_'.$subscribe_id.'.html';
                if(file_exists($html_file_path)){
                    @unlink($html_file_path);
                }
            break;
        }

    }
    protected function add_log($log_data){
        $log_file = APPPATH."/logs/log.txt";
        try{
            $myfile = fopen($log_file, "a") or die("Unable to open file!");
            $txt = date("Y-m-d H:i:s");
            fwrite($myfile, $txt."\n");
            fwrite($myfile, $log_data."\n");
            fclose($myfile);
        }catch (Exception $e){
            return false;
        }
        return true;
    }

}
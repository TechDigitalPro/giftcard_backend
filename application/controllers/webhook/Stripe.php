<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once 'Hook_controller.php';

class Stripe extends Hook_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('subscription_plan_model');
        $this->load->model('subscription_model');
        $this->load->model('transaction_model');
    }

    public function stripe_notify(){
        $post_input = @file_get_contents("php://input");
        
        file_put_contents("stripe_log.txt", print_r($post_input, 1));
        $this->add_log("Stripe payment log----".$post_input);

        $event_data=json_decode($post_input,false);
        $environment = $event_data->livemode?"1":"0";
        $event_type=$event_data->type;
        switch($event_type){
            case "customer.subscription.created":
                $subscription_data = $event_data->data->object;
                try{
                    if($subscription_data->status =='active' || $subscription_data->status == 'trialing'){
                        $user_id = $subscription_data->metadata->user_id;
                        $subscribe_id = $subscription_data->metadata->subscribe_id;
                        $plan_id = $subscription_data->metadata->plan_id;
                        $client_ip = $subscription_data->metadata->client_ip;
                        $stripe_subscription_id = $subscription_data->id;
                        $created_time = $subscription_data->created;
                        $start_time = $subscription_data->current_period_start;
                        $end_time = $subscription_data->current_period_end;
                        $customer_id = $subscription_data->customer;
                        $start_date = $subscription_data->start_date;
                        $condition = array(
                            "id" => $subscribe_id,
                        );
                        $update_data = array(
                            "stripe_subscription_id" => $stripe_subscription_id,
                            "customer_id" => $customer_id,
                            "client_ip" => $client_ip,
                            "start_time" => $start_time,
                            "end_time" => $end_time,
                            "start_date" => $start_date,
                            "environment" => $environment,
                            "status" => "1",
                            "created_time" => $created_time
                        );
                        $this->subscription_model->update($update_data, $condition);
                        $this->user_model->update(array("customer_id"=>$customer_id), array("id"=>$user_id));
                        echo 'success';
                    }else{
                        echo 'failed';
                    }
                }catch (Exception $e){
                    $body = $e->getJsonBody();
                    $err  = $body['error'];
                    $error_message=$err['message'];
                    echo 'failed';
                }
                break;
            case "customer.subscription.deleted":
                $subscription_data = $event_data->data->object;
                try{
                    if($subscription_data->status =='canceled'){
                        $subscribe_id = $subscription_data->metadata->subscribe_id;
                        $canceled_at = $subscription_data->canceled_at;
                        $ended_at = $subscription_data->ended_at;

                        $condition = array(
                            "id" => $subscribe_id,
                        );
                        $update_data = array(
                            "status" => "3",
                            "canceled_at" => $canceled_at,
                            "ended_at" => $ended_at
                        );
                        $this->subscription_model->update($update_data, $condition);
                        echo 'success';
                    }else{
                        echo 'failed';
                    }
                }catch (Exception $e){
                    $body = $e->getJsonBody();
                    $err  = $body['error'];
                    $error_message=$err['message'];
                    echo 'failed';
                }
                break;
            case "customer.subscription.updated":
                $subscription_data = $event_data->data->object;
                if($subscription_data->status =='active' || $subscription_data->status == 'trialing'){
                    $user_id = $subscription_data->metadata->user_id;
                    $subscribe_id = $subscription_data->metadata->subscribe_id;
                    $plan_id = $subscription_data->metadata->plan_id;
                    $client_ip = $subscription_data->metadata->client_ip;
                    $stripe_subscription_id = $subscription_data->id;
                    $created_time = $subscription_data->created;
                    $start_time = $subscription_data->current_period_start;
                    $end_time = $subscription_data->current_period_end;
                    $customer_id = $subscription_data->customer;
                    $start_date = $subscription_data->start_date;
                    $condition = array(
                        "id" => $subscribe_id,
                    );
                    $update_data = array(
                        "stripe_subscription_id" => $stripe_subscription_id,
                        "customer_id" => $customer_id,
                        "client_ip" => $client_ip,
                        "start_time" => $start_time,
                        "end_time" => $end_time,
                        "start_date" => $start_date,
                        "environment" => $environment,
                        "status" => "1",
                        "created_time" => $created_time
                    );
                    $this->subscription_model->update($update_data, $condition);
                    $this->user_model->update(array("customer_id"=>$customer_id), array("id"=>$user_id));
                    echo 'success';
                }else{
                    echo 'failed';
                }
                break;
            case "customer.subscription.trial_will_end":
                // $subscription_data = $event_data->data->object;
                // try{
                //     $subscribe_id = $subscription_data->metadata->subscribe_id;
                //     $canceled_at = $subscription_data->canceled_at;
                //     $ended_at = $subscription_data->ended_at;

                //     $condition = array(
                //         "id" => $subscribe_id,
                //     );
                //     $update_data = array(
                //         "status" => "2",
                //         "canceled_at" => $canceled_at,
                //         "ended_at" => $ended_at
                //     );
                //     $this->subscription_model->update($update_data, $condition);
                //     echo 'success';
                // }catch (Exception $e){
                //     $body = $e->getJsonBody();
                //     $err  = $body['error'];
                //     $error_message=$err['message'];
                //     echo 'failed';
                // }
                break;
            case "charge.succeeded":
                $payment_data = $event_data->data->object;
                try{
                    if($payment_data->status =='succeeded'){
                        $paid_amount = floatval($payment_data->amount) / 100;
                        $params = array(
                            'trans_id'=>$payment_data->id,
                            'balance_transaction'=>$payment_data->balance_transaction,
                            'customer_id'=>$payment_data->customer,
                            'currency'=>$payment_data->currency,
                            'amount'=>$paid_amount,
                            'environment'=>($payment_data->livemode ? "0" : "1"),
                            'payment_type'=>'stripe',
                            'description'=>$payment_data->description,
                            'payment_method'=>$payment_data->payment_method,
                            'created_time'=>$payment_data->created,
                        );
                        $this->transaction_model->add_transaction($params);
                        echo 'success';
                    }else{
                        echo 'failed';
                    }
                }catch (Exception $e){
                    $body = $e->getJsonBody();
                    $err  = $body['error'];
                    $error_message=$err['message'];
                    echo 'failed';
                }
                break;
        }
    }


}
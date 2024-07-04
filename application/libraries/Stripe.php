<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . "/third_party/stripe-php/init.php");
class Stripe {
    public $gateway_info;
    function __construct(){
        if(STRIPE_ENVIRONMENT == 'sandbox'){
            $this->gateway_info = array(
                'secret_key' => STRIPE_TEST_SECRET_KEY
            );
        }else{
            $this->gateway_info = array(
                'secret_key' => STRIPE_LIVE_SECRET_KEY
            );
        }
    }

    function stripe_charge_account($params){
        $gateway_info = $this->gateway_info;
        if(empty($gateway_info)) return false;

        $secret_key = $gateway_info['secret_key'];
        try {
            \Stripe\Stripe::setApiKey($secret_key);
            $result = \Stripe\Charge::create(
                array(
                    "amount" => $params['amount'],
                    "currency" => $params['currency'],
                    "source" => $params['token']
                )
            );
            return $result; //json_decode($result->__toJSON(),true);
        } catch(Exception $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err  = $body['error'];
            $error_message=$err['message'];
            $result = array("status" => "fail","msg" => $error_message);
            return $result;
        }
    }

    function stripe_charge_account_newly($params){ //create new customer and charge newly
        $gateway_info = $this->gateway_info;
        if(empty($gateway_info)) return false;

        $secret_key = $gateway_info['secret_key'];
        try {
            \Stripe\Stripe::setApiKey($secret_key);
            $customer = \Stripe\Customer::create([
                'email' => $params['owner'],
                "source" => array(
                    "object" => "card",
                    "number" => $params['number'],
                    "exp_month" => $params['exp_month'],
                    "exp_year" => $params['exp_year'],
                    "cvc" => $params['cvc'],
                )
            ]);
            $customer_id = $customer->id;
            $result = \Stripe\Charge::create([
                'customer' => $customer_id,
                'amount'   => intval($params['amount']),
                'currency' => $params['currency'],
                'metadata' => array(
                    'pay_sn'=>$params['pay_sn'],
                    'client_ip'=>$params['client_ip'],
                    'user_id'=>$params['user_id'],
                    'payment_product'=>$params['payment_product']
                )
            ]);
            return $result;//json_decode($result->__toJSON(),true);
        } catch(Exception $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err  = $body['error'];
            $error_message=$err['message'];
            $result = array("status" => "fail","msg" => $error_message);
            return $result;
        }
    }

    function stripe_charge_account_recursive($params){ //charge from customer id (subscription)
        $gateway_info = $this->gateway_info;
        if(empty($gateway_info)) return false;

        $secret_key = $gateway_info['secret_key'];
        try {
            \Stripe\Stripe::setApiKey($secret_key);
            $customer_id = $params['customer_id'];

            $result = \Stripe\Charge::create([
                'customer' => $customer_id,
                'amount'   => intval($params['amount']),
                'currency' => $params['currency'],
                'metadata' => array(
                    'pay_sn'=>$params['pay_sn'],
                    'client_ip'=>$params['client_ip'],
                    'user_id'=>$params['user_id'],
                    'payment_product'=>$params['payment_product']
                )
            ]);
            return $result;//json_decode($result->__toJSON(),true);
        } catch(Exception $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err  = $body['error'];
            $error_message=$err['message'];
            $result = array("status" => "fail","msg" => $error_message);
            return $result;
        }
    }

    function stripe_get_customer($id){
        $gateway_info = $this->gateway_info;
        if(empty($gateway_info)) return false;

        $secret_key = $gateway_info['secret_key'];
        try {
            \Stripe\Stripe::setApiKey($secret_key);
            $result = \Stripe\Customer::retrieve($id);
            return $result;//json_decode($result->__toJSON(),true);
        } catch(Exception $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err  = $body['error'];
            $error_message=$err['message'];
            $result = array("status" => "fail","msg" => $error_message);
            return $result;
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function create_three_d_secure($params){
        /*$params = array(
            'source_id'=>'',
            'email'=>'',
            'return_url'=>'',
            'description'=>'',
            'amount'=>100,
            'currency'=>'gbp'
        );*/
        $gateway_info = $this->gateway_info;
        if(empty($gateway_info)) return false;

        $secret_key = $gateway_info['secret_key'];
        try {
            \Stripe\Stripe::setApiKey($secret_key);
            $sourceId = $params['source_id'];// "src_1GaQ6XHzaZihTDYnrO9Wuwzm";// $params['source_id'];
            $customer = \Stripe\Customer::create([
                'description' => $params['description'],
                'email' => $params['email'],
                'source' => $sourceId,
            ]);
            $pa = [
                "amount" => $params['amount'],
                "currency" => $params['currency'],
                "type" => "three_d_secure",
                'three_d_secure' => [
                    'card' => $sourceId,
                    'customer' => $customer->id
                ],
                "redirect" => [
                    "return_url" => $params['return_url']."&customerId=".$customer->id
                ],
            ];
            $source = \Stripe\Source::create($pa);
            $customer->sources->create(['source' => $source->id]);
            //print_r($source->redirect);
            if(empty($source->redirect->failure_reason)){
                $result = array("status" => "succeeded","data" => $source->redirect->url);
                return $result;
            }else{
                $result = array("status" => "fail","msg" => $source->redirect->failure_reason);
                return $result;
            }
        } catch(Exception $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err  = $body['error'];
            $error_message=$err['message'];
            $result = array("status" => "fail","msg" => $error_message);
            return $result;
        }
    }
    function charge_process($params){ //charge from customer id (subscription)
        $gateway_info = $this->gateway_info;
        if(empty($gateway_info)) return false;

        $secret_key = $gateway_info['secret_key'];
        try {
            \Stripe\Stripe::setApiKey($secret_key);
            $charges = \Stripe\Charge::create(array(
                "amount" => $params['amount'],
                "currency" => $params['currency'],
                'source' => $params['source'],
                'customer' => $params['customerId'],
                "description" => $params['description']
            ));
            return $charges;//json_decode($result->__toJSON(),true);
        } catch(Exception $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err  = $body['error'];
            $error_message=$err['message'];
            $result = array("status" => "fail","msg" => $error_message);
            return $result;
        }
    }


}
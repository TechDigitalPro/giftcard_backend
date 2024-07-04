<?php
require_once(APPPATH . "/third_party/stripe-php/init.php");

function get_gateway_info($condition){
    $CI =get_instance();
    $query = $CI->db->get_where(GATEWAY_TB, $condition);
    $rslt = $query->result_array();
    if(!empty($rslt)) {
        return $rslt[0];
    }else{
        return array();
    }
}
function stripe_charge_account($params){
    $gateway_info = get_gateway_info(array('id'=>1, 'status'=>1));
    if(empty($gateway_info)) return false;

    $secret_key = ($gateway_info['environment'] == 'test' ? $gateway_info['val2'] : $gateway_info['val4']);
    try {
        \Stripe\Stripe::setApiKey($secret_key);
        $result = \Stripe\Charge::create(
            array(
                "amount" => $params['amount'],
                "currency" => "usd",
                "source" => array(
                    "object" => "card",
                    "number" => $params['number'],
                    "exp_month" => $params['exp_month'],
                    "exp_year" => $params['exp_year'],
                    "cvc" => $params['cvc'],
                )
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

function stripe_charge_account1($params){
    $gateway_info = get_gateway_info(array('id'=>1, 'status'=>1));
    if(empty($gateway_info)) return false;
    $secret_key = ($gateway_info['environment'] == 'test' ? $gateway_info['val2'] : $gateway_info['val4']);
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
            'currency' => 'usd',
            'metadata' => array(
                'pay_sn'=>$params['pay_sn'],
                'client_ip'=>$params['client_ip']
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

function stripe_charge_account2($params){
    $gateway_info = get_gateway_info(array('id'=>1, 'status'=>1));
    if(empty($gateway_info)) return false;
    $secret_key = ($gateway_info['environment'] == 'test' ? $gateway_info['val2'] : $gateway_info['val4']);
    try {
        \Stripe\Stripe::setApiKey($secret_key);
        $customer_id = 'cus_GF1uyPLPdchS4l';

        $result = \Stripe\Charge::create([
            'customer' => $customer_id,
            'amount'   => intval($params['amount']),
            'currency' => 'usd',
            'metadata' => array(
                'pay_sn'=>$params['pay_sn'],
                'client_ip'=>$params['client_ip']
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

function stripe_create_customer($params){
    /*$customer = \Stripe\Customer::create([
        'email' => $email,
        'source'  => $token,
        'metadata' => array(
            'pay_sn'=>$pay_sn,
            'client_ip'=>$client_ip
        )
    ]);
    $customer_id = $customer->id;*/
}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Uphold {
    public $access_token;
    function __construct(){

    }
    function get_uphold_connect_url($user_id=0, $user_type='u'){
        $state = $user_type."_".$user_id."_".time();
        if(UPHOLD_IS_SANDBOX){
            $uphold_connect_url = UPHOLD_SANDBOX_URL."/authorize/".UPHOLD_SANDBOX_CLIENT_ID;
            $uphold_connect_url.="?state=".$state;
            $uphold_connect_url.="&scope=cards:read%20accounts:read%20cards:write%20contacts:read%20contacts:write%20phones:read%20phones:write%20transactions:deposit%20transactions:read%20transactions:transfer:application%20transactions:transfer:others%20transactions:transfer:self%20transactions:withdraw%20transactions:commit:otp%20user:read";
        }else{
            $uphold_connect_url = UPHOLD_URL."/authorize/".UPHOLD_CLIENT_ID;
            $uphold_connect_url.="?state=".$state;
            $uphold_connect_url.="&scope=cards:read%20accounts:read%20cards:write%20contacts:read%20contacts:write%20phones:read%20phones:write%20transactions:deposit%20transactions:read%20transactions:transfer:application%20transactions:transfer:others%20transactions:transfer:self%20transactions:withdraw%20transactions:commit:otp%20user:read";
        }
        return $uphold_connect_url;
    }
    function get_uphold_api_url(){
        $api_url = UPHOLD_API_URL;
        if(UPHOLD_IS_SANDBOX){
            $api_url = UPHOLD_SANDBOX_API_URL;
        }
        return $api_url;
    }
    function get_uphold_auth_token($code, $state){
        $access_token = "";
        $api_url = UPHOLD_API_URL;
        $post_data = array(
            'client_id'=>UPHOLD_CLIENT_ID,
            'client_secret'=>UPHOLD_CLIENT_SECRET,
            'code'=>$code,
            'grant_type'=>'authorization_code'
        );
        if(UPHOLD_IS_SANDBOX){
            $post_data = array(
                'client_id'=>UPHOLD_SANDBOX_CLIENT_ID,
                'client_secret'=>UPHOLD_SANDBOX_CLIENT_SECRET,
                'code'=>$code,
                'grant_type'=>'authorization_code'
            );
            $api_url = UPHOLD_SANDBOX_API_URL;
        }
        $api_url.="/oauth2/token";
        $post_str = http_build_query($post_data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_str);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $output = curl_exec ($ch);
        curl_close ($ch);
        //print_r($server_output); die;
        /*
         {"access_token":"5b02b6b9c89490438a3fb78e9de85f526032835d","token_type":"bearer","scope":"cards:read accounts:read cards:write contacts:read contacts:write phones:read phones:write transactions:deposit transactions:read transactions:transfer:application transactions:transfer:others transactions:transfer:self transactions:withdraw transactions:commit:otp user:read"}
         * */
        try{
            $output_obj = json_decode($output, true);
            if(!isset($output_obj['error']) && isset($output_obj['access_token']) && !empty($output_obj['access_token'])){
                $access_token = $output_obj['access_token'];
            }
        }catch (Exception $e){

        }
        return $access_token;
    }
    function get_account_info($access_token){
        $this->access_token = $access_token;
        $api_url = $this->get_uphold_api_url();
        $api_url.="/v0/me";
        $api_result = $this->_api_get($api_url);
        return $api_result;
    }
    function get_card_list($access_token){
        $this->access_token = $access_token;
        $api_url = $this->get_uphold_api_url();
        $api_url.="/v0/me/cards";
        $api_result = $this->_api_get($api_url);
        return $api_result;
    }
    function create_card($access_token, $card_info){
        $this->access_token = $access_token;
        $api_url = $this->get_uphold_api_url();
        $api_url.="/v0/me/cards";
        /*$card_info = array(
            'label'=>'quanCard2',
            'currency'=>'USD'
        );*/
        $api_result = $this->_api_post($api_url, $card_info);
        return $api_result;
    }
    function get_availiable_card($access_token,$amount){
        $this->access_token = $access_token;
        $api_url = $this->get_uphold_api_url();
        $api_url.="/v0/me/cards";
        $card_list = $this->_api_get($api_url);
        if(isset($card_list['error'])){
            return $card_list;
        }else{
            foreach($card_list as $key => $card_info){
                $availiable_amount = floatval($card_info['available']);
                if($availiable_amount >= $amount){
                    return $card_info;
                }
            }
        }
        return array('error'=>'Insufficient amount in the uphold account');
    }
    function create_transaction($access_token, $transaction_info){
        $this->access_token = $access_token;
        $api_url = $this->get_uphold_api_url();
        if(!isset($transaction_info['card_id']) || empty($transaction_info['card_id'])){
            return array('error'=>'Card ID is empty');
        }
        $api_url.="/v0/me/cards/".$transaction_info['card_id']."/transactions?commit=true";
        $post_data = array(
            'denomination'=>array(
                'amount'=>decimal_format($transaction_info['amount']),
                'currency'=>strtoupper($transaction_info['currency'])
            ),
            'destination'=>$transaction_info['destination']
        );
        /*{ "denomination": { "amount": 5.12, "currency": "USD" }, "destination": "jintong113@outlook.com" }*/
        $api_result = $this->_api_raw_post($api_url, $post_data);
        return $api_result;
    }


    /****************************************************base functions**************************************************************/
    function _api_get($api_url, $params = array()){
        if(!empty($params)){
            $params_str = http_build_query($params);
            $api_url.= "?".$params_str;
        }
        $api_result = array();
        $authorization = "Authorization: Bearer ".$this->access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        //curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $post_str);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $output = curl_exec ($ch);
        curl_close ($ch);
        try{
            $output_obj = json_decode($output, true);
            if(true || !isset($output_obj['error'])){
                $api_result = $output_obj;
            }
        }catch (Exception $e){

        }
        return $api_result;
    }

    function _api_post($api_url, $params = array()){ //form url encoded
        $api_result = array();
        $authorization = "Authorization: Bearer ".$this->access_token;
        $params_str = http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded' , $authorization ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params_str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $output = curl_exec ($ch);
        curl_close ($ch);
        try{
            $output_obj = json_decode($output, true);
            if(true || !isset($output_obj['error'])){
                $api_result = $output_obj;
            }
        }catch (Exception $e){

        }
        return $api_result;
    }
    function _api_raw_post($api_url, $params = array()){ //raw json string
        $api_result = array();
        $authorization = "Authorization: Bearer ".$this->access_token;
        $params_str = json_encode($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',  'Content-Length: ' . strlen($params_str), $authorization ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params_str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $output = curl_exec($ch);
        curl_close ($ch);
        try{
            $output_obj = json_decode($output, true);
            if(true || !isset($output_obj['error'])){
                $api_result = $output_obj;
            }
        }catch (Exception $e){

        }
        return $api_result;
    }
}
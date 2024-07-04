<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "/third_party/stripe-php/init.php");

class Nmi {
    public $gateway_info;
    function __construct(){
        $gateway_info = array(
            'APIKey'=>'w4VGxmcwTfwQ43E5d7t88rXFApGYnS64',
            'gatewayURL'=>'https://secure.nmi.com/api/v2/three-step'
        );
        $this->gateway_info = $gateway_info;
    }

    function get_form_url($form_data){
        $xmlRequest = new DOMDocument('1.0','UTF-8');

        $xmlRequest->formatOutput = true;
        $xmlSale = $xmlRequest->createElement('sale');

        // Amount, authentication, and Redirect-URL are typically the bare minimum.
        appendXmlNode($xmlRequest, $xmlSale,'api-key',$this->gateway_info['APIKey']);
        appendXmlNode($xmlRequest, $xmlSale,'redirect-url',$_SERVER['HTTP_REFERER']);
        appendXmlNode($xmlRequest, $xmlSale, 'amount', $form_data['amount']);
        appendXmlNode($xmlRequest, $xmlSale, 'ip-address', $_SERVER["REMOTE_ADDR"]);
        //appendXmlNode($xmlRequest, $xmlSale, 'processor-id' , 'processor-a');
        appendXmlNode($xmlRequest, $xmlSale, 'currency', 'USD');
        //appendXmlNode($xmlRequest, $xmlSale, 'dup-seconds', 3);/////todo/////////////

        // Some additonal fields may have been previously decided by user
        appendXmlNode($xmlRequest, $xmlSale, 'order-id', $form_data['pay_sn']);
        appendXmlNode($xmlRequest, $xmlSale, 'order-description', $form_data['description']);
        appendXmlNode($xmlRequest, $xmlSale, 'merchant-defined-field-1' , 'Red');
        appendXmlNode($xmlRequest, $xmlSale, 'merchant-defined-field-2', 'Medium');
        appendXmlNode($xmlRequest, $xmlSale, 'tax-amount' , '0.00');
        appendXmlNode($xmlRequest, $xmlSale, 'shipping-amount' , '0.00');

        if(isset($form_data['customer-vault-id']) && !empty($form_data['customer-vault-id'])) {
            appendXmlNode($xmlRequest, $xmlSale, 'customer-vault-id' , $form_data['customer-vault-id']);
        }else {
            $xmlAdd = $xmlRequest->createElement('add-customer');
            appendXmlNode($xmlRequest, $xmlAdd, 'customer-vault-id' ,$form_data['user_id']."-".TIMESTAMP);
            $xmlSale->appendChild($xmlAdd);
        }

        // Set the Billing and Shipping from what was collected on initial shopping cart form
        $xmlBillingAddress = $xmlRequest->createElement('billing');
        appendXmlNode($xmlRequest, $xmlBillingAddress,'first-name', $form_data['owner']);
        appendXmlNode($xmlRequest, $xmlBillingAddress,'last-name', '');
        $xmlSale->appendChild($xmlBillingAddress);

        /*$xmlBillingAddress = $xmlRequest->createElement('billing');
        appendXmlNode($xmlRequest, $xmlBillingAddress,'first-name', 'John');
        appendXmlNode($xmlRequest, $xmlBillingAddress,'last-name', 'Doe');
        $xmlSale->appendChild($xmlBillingAddress);

        $xmlShippingAddress = $xmlRequest->createElement('shipping');
        appendXmlNode($xmlRequest, $xmlShippingAddress,'first-name', 'John');
        appendXmlNode($xmlRequest, $xmlShippingAddress,'last-name', 'Doe');
        $xmlSale->appendChild($xmlShippingAddress);

        $xmlProduct = $xmlRequest->createElement('product');
        appendXmlNode($xmlRequest, $xmlProduct,'product-code' , 'SKU-123456');
        appendXmlNode($xmlRequest, $xmlProduct,'description' , 'test 2 product description');
        $xmlSale->appendChild($xmlProduct);*/

        $xmlRequest->appendChild($xmlSale);

        // Process Step One: Submit all transaction details to the Payment Gateway except the customer's sensitive payment information.
        // The Payment Gateway will return a variable form-url.

        $data = sendXMLviaCurl($xmlRequest,$this->gateway_info['gatewayURL']);

        // Parse Step One's XML response
        $formURL = '';
        $gwResponse = @new SimpleXMLElement($data);
        //print_r($gwResponse); die;
        if ((string)$gwResponse->result ==1 ) {
            // The form url for used in Step Two below
            $formURL = $gwResponse->{'form-url'};
            if($formURL){
                $formURL = json_decode(json_encode($formURL), true);
                $formURL = $formURL[0];
            }
        } else {
            //throw New Exception(print " Error, received " . $data);
        }
        return $formURL;
    }

    function complete_transaction($tokenId){
        // Step Three: Once the browser has been redirected, we can obtain the token-id and complete
        // the transaction through another XML HTTPS POST including the token-id which abstracts the
        // sensitive payment information that was previously collected by the Payment Gateway.
        //$tokenId = $_GET['token-id'];
        $xmlRequest = new DOMDocument('1.0','UTF-8');
        $xmlRequest->formatOutput = true;
        $xmlCompleteTransaction = $xmlRequest->createElement('complete-action');
        appendXmlNode($xmlRequest, $xmlCompleteTransaction,'api-key',$this->gateway_info['APIKey']);
        appendXmlNode($xmlRequest, $xmlCompleteTransaction,'token-id',$tokenId);
        $xmlRequest->appendChild($xmlCompleteTransaction);

        // Process Step Three
        $data = sendXMLviaCurl($xmlRequest,$this->gateway_info['gatewayURL']);
        $gwResponse = @new SimpleXMLElement((string)$data);
        $gwResponse = json_decode(json_encode($gwResponse), true);
        //print_r($gwResponse); die;
        if($gwResponse['result'] == 1){
            return array(true, $gwResponse);
        }else{
            return array(false, $gwResponse['result-text']);
        }
        /*if ((string)$gwResponse->result == 1 ) {
            print " <p><h3> Transaction was Approved, XML response was:</h3></p>\n";
            print '<pre>' . (htmlentities($data)) . '</pre>';

        } elseif((string)$gwResponse->result == 2)  {
            print " <p><h3> Transaction was Declined.</h3>\n";
            print " Decline Description : " . (string)$gwResponse->{'result-text'} ." </p>";
            print " <p><h3>XML response was:</h3></p>\n";
            print '<pre>' . (htmlentities($data)) . '</pre>';
        } else {
            print " <p><h3> Transaction caused an Error.</h3>\n";
            print " Error Description: " . (string)$gwResponse->{'result-text'} ." </p>";
            print " <p><h3>XML response was:</h3></p>\n";
            print '<pre>' . (htmlentities($data)) . '</pre>';
        }*/
    }





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
        $gateway_info = $this->gateway_info;
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

    function stripe_charge_account_newly($params){ //create new customer and charge newly
        $gateway_info = $this->gateway_info;
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
        $secret_key = ($gateway_info['environment'] == 'test' ? $gateway_info['val2'] : $gateway_info['val4']);
        try {
            \Stripe\Stripe::setApiKey($secret_key);
            $customer_id = $params['customer_id'];

            $result = \Stripe\Charge::create([
                'customer' => $customer_id,
                'amount'   => intval($params['amount']),
                'currency' => 'usd',
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
        $secret_key = ($gateway_info['environment'] == 'test' ? $gateway_info['val2'] : $gateway_info['val4']);
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

}
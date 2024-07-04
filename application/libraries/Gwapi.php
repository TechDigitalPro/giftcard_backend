<?php
define("GW_APPROVED", 1);
define("GW_DECLINED", 2);
define("GW_ERROR", 3);

class Gwapi {

// Initial Setting Functions
    public $gateway_info;
    public $live_mode;
    function __construct(){
        $this->live_mode = NMI_IS_LIVE;
        if($this->live_mode){
            $gateway_info = array(
                'security_key'=>NMI_LIVE_SECRET_KEY,
                'gatewayURL'=>'https://secure.nmi.com/api/v2/three-step'
            );
        }else{
            $gateway_info = array(
                'security_key'=>NMI_TEST_SECRET_KEY,
                'gatewayURL'=>'https://secure.nmi.com/api/v2/three-step'
            );
        }
        $this->gateway_info = $gateway_info;
    }
    /*three step part*/
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

    /*direct payment api*/
    function setOrder($orderid,
                      $orderdescription,
                      $tax,
                      $shipping,
                      $ponumber,
                      $ipaddress) {
        $this->order['orderid']          = $orderid;
        $this->order['orderdescription'] = $orderdescription;
        $this->order['tax']              = $tax;
        $this->order['shipping']         = $shipping;
        $this->order['ponumber']         = $ponumber;
        $this->order['ipaddress']        = $ipaddress;
    }

    function setBilling($firstname,
                        $lastname = '',
                        $company = '',
                        $address1 = '',
                        $address2 = '',
                        $city = '',
                        $state = '',
                        $zip = '',
                        $country = '',
                        $phone = '',
                        $fax = '',
                        $email = '',
                        $website = '') {
        $this->billing['firstname'] = $firstname;
        $this->billing['lastname']  = $lastname;
        $this->billing['company']   = $company;
        $this->billing['address1']  = $address1;
        $this->billing['address2']  = $address2;
        $this->billing['city']      = $city;
        $this->billing['state']     = $state;
        $this->billing['zip']       = $zip;
        $this->billing['country']   = $country;
        $this->billing['phone']     = $phone;
        $this->billing['fax']       = $fax;
        $this->billing['email']     = $email;
        $this->billing['website']   = $website;
    }

    function setShipping($firstname,
                         $lastname='',
                         $company='',
                         $address1='',
                         $address2='',
                         $city='',
                         $state='',
                         $zip='',
                         $country='',
                         $email='') {
        $this->shipping['firstname'] = $firstname;
        $this->shipping['lastname']  = $lastname;
        $this->shipping['company']   = $company;
        $this->shipping['address1']  = $address1;
        $this->shipping['address2']  = $address2;
        $this->shipping['city']      = $city;
        $this->shipping['state']     = $state;
        $this->shipping['zip']       = $zip;
        $this->shipping['country']   = $country;
        $this->shipping['email']     = $email;
    }

    // Transaction Functions

    function doSale($amount, $ccnumber, $ccexp, $cvv="") {

        $query  = "";
        // Login Information
        $query .= "security_key=" . urlencode($this->gateway_info['security_key']) . "&";
        // Sales Information
        $query .= "ccnumber=" . urlencode($ccnumber) . "&";
        $query .= "ccexp=" . urlencode($ccexp) . "&";
        $query .= "amount=" . urlencode(number_format($amount,2,".","")) . "&";
        $query .= "cvv=" . urlencode($cvv) . "&";
        //$query .= "dup_seconds=" . urlencode($cvv) . "&";  //todo quan///////////////////

        // Order Information
        $query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
        $query .= "orderid=" . urlencode($this->order['orderid']) . "&";
        $query .= "orderdescription=" . urlencode($this->order['orderdescription']) . "&";
        $query .= "tax=" . urlencode(number_format($this->order['tax'],2,".","")) . "&";
        $query .= "shipping=" . urlencode(number_format($this->order['shipping'],2,".","")) . "&";
        $query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
        // Billing Information
        $query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
        $query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
        $query .= "company=" . urlencode($this->billing['company']) . "&";
        $query .= "address1=" . urlencode($this->billing['address1']) . "&";
        $query .= "address2=" . urlencode($this->billing['address2']) . "&";
        $query .= "city=" . urlencode($this->billing['city']) . "&";
        $query .= "state=" . urlencode($this->billing['state']) . "&";
        $query .= "zip=" . urlencode($this->billing['zip']) . "&";
        $query .= "country=" . urlencode($this->billing['country']) . "&";
        $query .= "phone=" . urlencode($this->billing['phone']) . "&";
        $query .= "fax=" . urlencode($this->billing['fax']) . "&";
        $query .= "email=" . urlencode($this->billing['email']) . "&";
        $query .= "website=" . urlencode($this->billing['website']) . "&";
        // Shipping Information
        $query .= "shipping_firstname=" . urlencode($this->shipping['firstname']) . "&";
        $query .= "shipping_lastname=" . urlencode($this->shipping['lastname']) . "&";
        $query .= "shipping_company=" . urlencode($this->shipping['company']) . "&";
        $query .= "shipping_address1=" . urlencode($this->shipping['address1']) . "&";
        $query .= "shipping_address2=" . urlencode($this->shipping['address2']) . "&";
        $query .= "shipping_city=" . urlencode($this->shipping['city']) . "&";
        $query .= "shipping_state=" . urlencode($this->shipping['state']) . "&";
        $query .= "shipping_zip=" . urlencode($this->shipping['zip']) . "&";
        $query .= "shipping_country=" . urlencode($this->shipping['country']) . "&";
        $query .= "shipping_email=" . urlencode($this->shipping['email']) . "&";
        $query .= "type=sale";
        return $this->_doPost($query);
    }

    function doAuth($amount, $ccnumber, $ccexp, $cvv="") {

        $query  = "";
        // Login Information
        $query .= "security_key=" . urlencode($this->gateway_info['security_key']) . "&";
        // Sales Information
        $query .= "ccnumber=" . urlencode($ccnumber) . "&";
        $query .= "ccexp=" . urlencode($ccexp) . "&";
        $query .= "amount=" . urlencode(number_format($amount,2,".","")) . "&";
        $query .= "cvv=" . urlencode($cvv) . "&";
        // Order Information
        $query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
        $query .= "orderid=" . urlencode($this->order['orderid']) . "&";
        $query .= "orderdescription=" . urlencode($this->order['orderdescription']) . "&";
        $query .= "tax=" . urlencode(number_format($this->order['tax'],2,".","")) . "&";
        $query .= "shipping=" . urlencode(number_format($this->order['shipping'],2,".","")) . "&";
        $query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
        // Billing Information
        $query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
        $query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
        $query .= "company=" . urlencode($this->billing['company']) . "&";
        $query .= "address1=" . urlencode($this->billing['address1']) . "&";
        $query .= "address2=" . urlencode($this->billing['address2']) . "&";
        $query .= "city=" . urlencode($this->billing['city']) . "&";
        $query .= "state=" . urlencode($this->billing['state']) . "&";
        $query .= "zip=" . urlencode($this->billing['zip']) . "&";
        $query .= "country=" . urlencode($this->billing['country']) . "&";
        $query .= "phone=" . urlencode($this->billing['phone']) . "&";
        $query .= "fax=" . urlencode($this->billing['fax']) . "&";
        $query .= "email=" . urlencode($this->billing['email']) . "&";
        $query .= "website=" . urlencode($this->billing['website']) . "&";
        // Shipping Information
        $query .= "shipping_firstname=" . urlencode($this->shipping['firstname']) . "&";
        $query .= "shipping_lastname=" . urlencode($this->shipping['lastname']) . "&";
        $query .= "shipping_company=" . urlencode($this->shipping['company']) . "&";
        $query .= "shipping_address1=" . urlencode($this->shipping['address1']) . "&";
        $query .= "shipping_address2=" . urlencode($this->shipping['address2']) . "&";
        $query .= "shipping_city=" . urlencode($this->shipping['city']) . "&";
        $query .= "shipping_state=" . urlencode($this->shipping['state']) . "&";
        $query .= "shipping_zip=" . urlencode($this->shipping['zip']) . "&";
        $query .= "shipping_country=" . urlencode($this->shipping['country']) . "&";
        $query .= "shipping_email=" . urlencode($this->shipping['email']) . "&";
        $query .= "type=auth";
        return $this->_doPost($query);
    }

    function doCredit($amount, $ccnumber, $ccexp) {

        $query  = "";
        // Login Information
        $query .= "security_key=" . urlencode($this->gateway_info['security_key']) . "&";
        // Sales Information
        $query .= "ccnumber=" . urlencode($ccnumber) . "&";
        $query .= "ccexp=" . urlencode($ccexp) . "&";
        $query .= "amount=" . urlencode(number_format($amount,2,".","")) . "&";
        // Order Information
        $query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
        $query .= "orderid=" . urlencode($this->order['orderid']) . "&";
        $query .= "orderdescription=" . urlencode($this->order['orderdescription']) . "&";
        $query .= "tax=" . urlencode(number_format($this->order['tax'],2,".","")) . "&";
        $query .= "shipping=" . urlencode(number_format($this->order['shipping'],2,".","")) . "&";
        $query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
        // Billing Information
        $query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
        $query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
        $query .= "company=" . urlencode($this->billing['company']) . "&";
        $query .= "address1=" . urlencode($this->billing['address1']) . "&";
        $query .= "address2=" . urlencode($this->billing['address2']) . "&";
        $query .= "city=" . urlencode($this->billing['city']) . "&";
        $query .= "state=" . urlencode($this->billing['state']) . "&";
        $query .= "zip=" . urlencode($this->billing['zip']) . "&";
        $query .= "country=" . urlencode($this->billing['country']) . "&";
        $query .= "phone=" . urlencode($this->billing['phone']) . "&";
        $query .= "fax=" . urlencode($this->billing['fax']) . "&";
        $query .= "email=" . urlencode($this->billing['email']) . "&";
        $query .= "website=" . urlencode($this->billing['website']) . "&";
        $query .= "type=credit";
        return $this->_doPost($query);
    }

    function doOffline($authorizationcode, $amount, $ccnumber, $ccexp) {

        $query  = "";
        // Login Information
        $query .= "security_key=" . urlencode($this->gateway_info['security_key']) . "&";
        // Sales Information
        $query .= "ccnumber=" . urlencode($ccnumber) . "&";
        $query .= "ccexp=" . urlencode($ccexp) . "&";
        $query .= "amount=" . urlencode(number_format($amount,2,".","")) . "&";
        $query .= "authorizationcode=" . urlencode($authorizationcode) . "&";
        // Order Information
        $query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
        $query .= "orderid=" . urlencode($this->order['orderid']) . "&";
        $query .= "orderdescription=" . urlencode($this->order['orderdescription']) . "&";
        $query .= "tax=" . urlencode(number_format($this->order['tax'],2,".","")) . "&";
        $query .= "shipping=" . urlencode(number_format($this->order['shipping'],2,".","")) . "&";
        $query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
        // Billing Information
        $query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
        $query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
        $query .= "company=" . urlencode($this->billing['company']) . "&";
        $query .= "address1=" . urlencode($this->billing['address1']) . "&";
        $query .= "address2=" . urlencode($this->billing['address2']) . "&";
        $query .= "city=" . urlencode($this->billing['city']) . "&";
        $query .= "state=" . urlencode($this->billing['state']) . "&";
        $query .= "zip=" . urlencode($this->billing['zip']) . "&";
        $query .= "country=" . urlencode($this->billing['country']) . "&";
        $query .= "phone=" . urlencode($this->billing['phone']) . "&";
        $query .= "fax=" . urlencode($this->billing['fax']) . "&";
        $query .= "email=" . urlencode($this->billing['email']) . "&";
        $query .= "website=" . urlencode($this->billing['website']) . "&";
        // Shipping Information
        $query .= "shipping_firstname=" . urlencode($this->shipping['firstname']) . "&";
        $query .= "shipping_lastname=" . urlencode($this->shipping['lastname']) . "&";
        $query .= "shipping_company=" . urlencode($this->shipping['company']) . "&";
        $query .= "shipping_address1=" . urlencode($this->shipping['address1']) . "&";
        $query .= "shipping_address2=" . urlencode($this->shipping['address2']) . "&";
        $query .= "shipping_city=" . urlencode($this->shipping['city']) . "&";
        $query .= "shipping_state=" . urlencode($this->shipping['state']) . "&";
        $query .= "shipping_zip=" . urlencode($this->shipping['zip']) . "&";
        $query .= "shipping_country=" . urlencode($this->shipping['country']) . "&";
        $query .= "shipping_email=" . urlencode($this->shipping['email']) . "&";
        $query .= "type=offline";
        return $this->_doPost($query);
    }

    function doCapture($transactionid, $amount =0) {

        $query  = "";
        // Login Information
        $query .= "security_key=" . urlencode($this->gateway_info['security_key']) . "&";
        // Transaction Information
        $query .= "transactionid=" . urlencode($transactionid) . "&";
        if ($amount>0) {
            $query .= "amount=" . urlencode(number_format($amount,2,".","")) . "&";
        }
        $query .= "type=capture";
        return $this->_doPost($query);
    }

    function doVoid($transactionid) {

        $query  = "";
        // Login Information
        $query .= "security_key=" . urlencode($this->gateway_info['security_key']) . "&";
        // Transaction Information
        $query .= "transactionid=" . urlencode($transactionid) . "&";
        $query .= "type=void";
        return $this->_doPost($query);
    }

    function doRefund($transactionid, $amount = 0) {

        $query  = "";
        // Login Information
        $query .= "security_key=" . urlencode($this->gateway_info['security_key']) . "&";
        // Transaction Information
        $query .= "transactionid=" . urlencode($transactionid) . "&";
        if ($amount>0) {
            $query .= "amount=" . urlencode(number_format($amount,2,".","")) . "&";
        }
        $query .= "type=refund";
        return $this->_doPost($query);
    }
    function _doPost($query) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://secure.nmi.com/api/transact.php");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_POST, 1);

        if (!($data = curl_exec($ch))) {
            return GW_ERROR;
        }
        curl_close($ch);
        unset($ch);
        //print "\n$data\n";
        $data = explode("&",$data);
        for($i=0;$i<count($data);$i++) {
            $rdata = explode("=",$data[$i]);
            $this->responses[$rdata[0]] = $rdata[1];
        }
        $this->responses['livemode'] = $this->live_mode;
        return $this->responses;
    }
}

/*$gw = new gwapi;
//$gw->setLogin("w4VGxmcwTfwQ43E5d7t88rXFApGYnS64");
$gw->setBilling("John","Smith","Acme, Inc.","123 Main St","Suite 200", "Beverly Hills",
    "CA","90210","US","555-555-5555","555-555-5556","support@example.com",
    "www.example.com");
$gw->setShipping("Mary","Smith","na","124 Shipping Main St","Suite Ship", "Beverly Hills",
    "CA","90210","US","support@example.com");
$gw->setOrder("1234","Big Order",1, 2, "PO1234","65.192.14.10");

$r = $gw->doSale("50.00","4111111111111111","1010");
print $gw->responses['responsetext'];*/
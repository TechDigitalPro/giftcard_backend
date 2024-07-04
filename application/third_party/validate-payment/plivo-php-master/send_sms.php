<?php

require 'vendor/autoload.php';
use Plivo\RestAPI;
function send_Plivo_SMS($phone_number,$content){
    $auth_id = "MAYTQ1ZTU0MTDJYZG0NG";
    $auth_token = "MTg2Y2I0MDMwMmE2Y2QzNWQwOGFjY2I4YmUxMjRk";

    $p = new RestAPI($auth_id, $auth_token);
    $phone = $phone_number;
    $code = $content;
    // Set message parameters
    $params = array(
        'src' => '16197282827', // Sender's phone number with country code
        'dst' => $phone, // Receiver's phone number with country code
        'text' => $code, // Your SMS text message
        'url' => 'http://postsweb.co/api/sms_report.php', // The URL to which with the status of the message is sent
        'method' => 'POST', // The method used to call the url
        'type' => 'sms' // The method used to call the url
    );
    // Send message
    $response = $p->send_message($params);
    // Print the response
    return $response['response'];

    // Print the Api ID
    //echo "<br> Api ID : {$response['response']['api_id']} <br>";

    // Print the Message UUID
    //echo "Message UUID : {$response['response']['message_uuid'][0]} <br>";
}


?>
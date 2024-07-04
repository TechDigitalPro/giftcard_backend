<?php
require 'vendor/autoload.php';
use Plivo\RestAPI;
$phone = $_GET['phone'];
$code = $_GET['code'];
send_Plivo_SMS($phone,$code);
function send_Plivo_SMS($phone,$content){
    $auth_id = "MAYTQ1ZTU0MTDJYZG0NG";
    $auth_token = "MTg2Y2I0MDMwMmE2Y2QzNWQwOGFjY2I4YmUxMjRk";

    $p = new RestAPI($auth_id, $auth_token);
    print_r($p);
    $code = $content;
// Set message parameters
    $params = array(
        'src' => 'Posts', // Sender's phone number with country code
        'dst' => $phone, // Receiver's phone number with country code
        'text' => 'Your verification code is '.$code, // Your SMS text message
        // To send Unicode text
        //'text' => 'こんにちは、元気ですか？' # Your SMS Text Message - Japanese
        //'text' => 'Ce est texte généré aléatoirement' # Your SMS Text Message - French
        'url' => 'http://example.com/report/', // The URL to which with the status of the message is sent
        'method' => 'POST', // The method used to call the url
        'type' => 'sms' // The method used to call the url
    );

    print_r($params);
// Send message
    $response = $p->send_message($params);

// Print the response

    print_r($response['response']) ;

// Print the Api ID
    //echo "<br> Api ID : {$response['response']['api_id']} <br>";

// Print the Message UUID
    //echo "Message UUID : {$response['response']['message_uuid'][0]} <br>";
}


?>
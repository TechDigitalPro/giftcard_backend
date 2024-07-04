<?php
require 'vendor/autoload.php';
use Plivo\RestAPI;

function send_Plivo_Voice_SMS($phone_number,$content)
{
    $auth_id = "MAZDE2MTRJY2VKZJVJYT";
    $auth_token = "YzUxYjZhNzJkMzYyYzJjMjEyNGZmNGI2MDI0ZjEz";
    $p = new RestAPI($auth_id, $auth_token);
    $params = array(
        'to' => $phone_number,   # The phone numer to which the call will be placed
        'from' => '16197282827', # The phone number to be used as the caller id

        # answer_url is the URL invoked by Plivo when the outbound call is answered
        # and contains instructions telling Plivo what to do with the call
        'answer_url' => "https://s3.amazonaws.com/static.plivo.com/answer.xml",
        'answer_method' => "GET", # The method used to call the answer_url

        # Example for asynchronous request
        # callback_url is the URL to which the API response is sent.
        #'callback_url' => "http://myvoiceapp.com/callback/",
        #'callback_method' => "GET" # The method used to notify the callback_url.
    );

# Make an outbound call and print the response
    $response = $p->make_call($params);
    return $response;
}
?>
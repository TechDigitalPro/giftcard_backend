<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 10/5/2019
 * Time: 10:54 AM
 */
function send_broadcast_onesignal_notification($data){
    $heading = array(
        "en" => $data['notification_title']
    );
    $content = array(
        "en" => $data['notification_content']
    );
    $fields = array(
        'app_id' => ONESIGNAL_APP_ID,
        'included_segments' => array('All'),
        'data' => $data,
        'large_icon' =>"ic_launcher_round.png",
        'headings' => $heading,
        /*   'ios_badgeType'=>'Increase',*/
        'contents' => $content
    );
    /*if(isset($data['ios_badgeCount'])){
        $fields['ios_badgeType'] = 'SetTo';
        $fields['ios_badgeCount'] = $data['ios_badgeCount'];
    }*/

    $fields = json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
        'Authorization: key='.FIREBASE_SERVER_KEY));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}
function send_onesignal_notification($content,$player_ids = array()){
    if(empty($player_ids)) return false;
    
    $fields = array (
        'registration_ids' => $player_ids,
        'data' => array(
            'title'=>"Zimbo",
            "body"=>$content
        ),
        'notification'=> array(
            'title'=>"Zimbo",
            "body"=>$content
        )
    );
    $fields = json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
        'Authorization: key='.FIREBASE_SERVER_KEY));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}
//multiple
// function send_onesignal_notification($data,$player_ids = array()){
//     if(empty($player_ids)) return false;
    
//     $fields = array (
//         'registration_ids' => $player_ids,
//         'data' => $data,
//         'notification'=> array(
//             'title'=>$data['notification_title'],
//             "body"=>$data['notification_content']
//         )
//     );
//     $fields = json_encode($fields);
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
//     curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
//         'Authorization: key='.FIREBASE_SERVER_KEY));
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//     curl_setopt($ch, CURLOPT_HEADER, FALSE);
//     curl_setopt($ch, CURLOPT_POST, TRUE);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

//     $response = curl_exec($ch);
//     curl_close($ch);

//     return $response;
// }
function send_onesignal_android_notification($data,$player_ids = array()){
    //$player_ids = array("6392d91a-b206-4b7b-a620-cd68e32c3a76","76ece62b-bcfe-468c-8a78-839aeaa8c5fa","8e0f21fa-9a5a-4ae7-a9a6-ca1f24294b86");
    add_notification_log(json_encode($data));
    add_notification_log(json_encode($player_ids));

    if(empty($player_ids)) return false;
    $heading = array(
        "en" => $data['notification_title']
    );
    $content = array(
        "en" => $data['notification_content']
    );
    $fields = array(
        'app_id' => ONESIGNAL_APP_ID,
        'include_player_ids'=>$player_ids,
        'data' => $data,
        'large_icon' =>"ic_launcher_round.png",
        'headings' => $heading,
        'contents' => $content
    );
    $fields = json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
        'Authorization: Basic '.ONESIGNAL_AUTH_KEY));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}
function send_onesignal_ios_notification($data,$player_ids = array()){
    //$player_ids = array("6392d91a-b206-4b7b-a620-cd68e32c3a76","76ece62b-bcfe-468c-8a78-839aeaa8c5fa","8e0f21fa-9a5a-4ae7-a9a6-ca1f24294b86");
    add_notification_log(json_encode($data));
    add_notification_log(json_encode($player_ids));

    if(empty($player_ids)) return false;
    $heading = array(
        "en" => $data['notification_title']
    );
    $content = array(
        "en" => $data['notification_content']
    );
    $fields = array(
        'app_id' => ONESIGNAL_APP_ID,
        'include_player_ids'=>$player_ids,
        'data' => $data,
        'large_icon' =>"ic_launcher_round.png",
        'headings' => $heading,
        'contents' => $content
    );
    if(isset($data['ios_badgeCount'])) {
        $fields['ios_badgeType'] = 'SetTo';
        $fields['ios_badgeCount'] = $data['ios_badgeCount'];
    }

    $fields = json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
        'Authorization: Basic '.ONESIGNAL_AUTH_KEY));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}
function send_onesignal_web_notification($data,$player_ids = array()){

    $heading = array(
        "en" => $data['notification_title']
    );
    $content = array(
        "en" => $data['notification_content']
    );
    $fields = array(
        'app_id' => ONESIGNAL_APP_ID,
        'include_player_ids'=>$player_ids,
        'data' => $data,
        'large_icon' =>"ic_launcher_round.png",
        'headings' => $heading,
        'contents' => $content
    );
    $fields = json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
        'Authorization: Basic '.ONESIGNAL_AUTH_KEY));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function add_notification_log($data){
    return false;
    $log_file = APPPATH."logs/log.txt";
    $myfile = fopen($log_file, "a") or die("Unable to open file!");
    fwrite($myfile, "--------------------------------------------------------------------------------------------------------------------------------------------\n");
    fwrite($myfile, "data: ".$data."\n");
    fwrite($myfile, "--------------------------------------------------------------------------------------------------------------------------------------------\n");
    fclose($myfile);
}
<?php
/*
 * run each hour
 *
 * */
include(dirname(__FILE__).DIRECTORY_SEPARATOR."common.php");

$cron_url = $base_url."/crontab/day/index";
//print_r($cron_url); die;
add_log('cron started on curl: '.$cron_url);
$res = getFromUrl($cron_url);
print_r($res);
//add_log($res);

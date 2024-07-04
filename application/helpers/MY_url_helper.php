<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('get_self_url')) {
    function get_self_url()
    {
        return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }
}
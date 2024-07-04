<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function pagination($config)
{
    $ci = & get_instance();
    $ci->load->library('pagination');
    $config['num_links'] = 2;

    $config['full_tag_open'] = '<ul class="pagination pagination-info">';
    $config['full_tag_close'] = '</ul>';
    $config['first_link'] = 'First';
    $config['last_link'] = 'Last';
    $config['next_link'] = '&gt;';
    $config['prev_link'] = '&lt;';
    $config['first_tag_open'] = ' <li>';
    $config['first_tag_close'] = ' </li>';
    $config['last_tag_open'] = ' <li>';
    $config['last_tag_close'] = ' </li>';
    $config['next_tag_open'] = ' <li>';
    $config['next_tag_close'] = ' </li>';
    $config['prev_tag_open'] = ' <li>';
    $config['prev_tag_close'] = ' </li>';
    $config['num_tag_open'] = ' <li>';
    $config['num_tag_close'] = '</li>';
    $config['cur_tag_open'] = ' <li class="active"><a href="javascript:void(0);">';
    $config['cur_tag_close'] = '</a></li>';

    $config['reuse_query_string'] = TRUE;
    $config['use_page_numbers'] = TRUE;
//    $config['display_pages'] = FALSE;
    $ci->pagination->initialize($config);
    return $ci->pagination->create_links();
}

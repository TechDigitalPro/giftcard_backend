<?php
//print_r($data['admin']); die;
$menu_arr=array();
$menu_arr[]=
    array(
        'text' 	=> 'Dashboard',
        'letter_text' 	=> 'view_headline',
        'sub_menu_list' =>array(
            array(
                'text' 	=> 'list',
                'tab_list' => array(
                    'admin/dashboard/index'
                )
            )
        )
    );
$menu_arr[]=
    array(
        'text' 	=> 'Users',
        'letter_text' 	=> 'people',
        'sub_menu_list' =>array(
            array(
                'text' 	=> 'list',
                'tab_list' => array(
                    'admin/user/index',
                    'admin/user/detail',
                    'admin/user/add',
                    'admin/user/change_status',
                    'admin/user/delete'
                )
            )
        )
    );
$menu_arr[]=
    array(
        'text' 	=> 'Profile',
        'letter_text' 	=> 'person',
        'sub_menu_list' =>array(
            array(
                'text' 	=> 'list',
                'tab_list' => array(
                    'admin/profile/index'
                )
            )
        )
    );
$menu_arr[]=
    array(
        'text' 	=> 'Feed',
        'letter_text' 	=> 'subject',
        'sub_menu_list' =>array(
            array(
                'text' 	=> 'list',
                'tab_list' => array(
                    'admin/feed/index',
                    'admin/feed/detail'
                )
            )
        )
    );
$menu_arr[]=
    array(
        'text' 	=> 'Donate',
        'letter_text' 	=> 'attach_money',
        'sub_menu_list' =>array(
            array(
                'text' 	=> 'list',
                'tab_list' => array(
                    'admin/payment/index',
                    'admin/payment/delete'
                )
            ),
            /*array(
                'text' 	=> 'Requested',
                'tab_list' => array(
                    'admin/withdraw/requested'
                )
            )*/
        )
    );

$menu_arr[]=
    array(
        'text' 	=> 'Setting',
        'letter_text' 	=> 'settings_applications',
        'sub_menu_list' =>array(
            array(
                'text' 	=> 'list',
                'tab_list' => array(
                    'admin/setting/index'
                )
            )
        )
    );
$menu_arr[]=
    array(
        'text' 	=> 'Log out',
        'letter_text' 	=> 'power_settings_new',
        'sub_menu_list' =>array(
            array(
                'text' 	=> 'list',
                'tab_list' => array(
                    'admin/dashboard/logout'
                )
            )
        )
    );


function contain_current_url($current_uri, $side_item_tab_list){
    $result=false;
    foreach ($side_item_tab_list as $item){
        if(is_array($item)){
            $uri=$item['uri'];
            $params=@$item['params'];
            if($current_uri==$uri){
                if(!empty($params)){
                    foreach ($params as $key => $value){
                        if(isset($_GET[$key])){
                            if($_GET[$key]==$value){
                                $result=true;
                            }
                        }else{
                            $result=true;
                        }
                    }
                }else{
                    $result=true;
                }
            }
        }else{
            if($current_uri==$item){
                $result=true;
            }
        }
    }
    return $result;
}
function make_url($side_item_tab_list_item){
    $result_url="#";
    if(is_array($side_item_tab_list_item)){
        $uri=base_url($side_item_tab_list_item['uri']);
        $params=@$side_item_tab_list_item['params'];
        if(!empty($params) && is_array($params)){
            $uri.="?";
            $uri.= http_build_query( $params );
        }
        $result_url=$uri;
    }else{
        if(!empty($side_item_tab_list_item)){
            $result_url=base_url($side_item_tab_list_item);
        }
    }
    return $result_url;
}

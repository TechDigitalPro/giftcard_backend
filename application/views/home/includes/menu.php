<?php
$menu_arr=array();
$menu_arr[]=
    array(
        'text' 	=> lang('Home'),
        'letter_text' 	=> '',
        'sub_menu_list' =>array(
            array(
                'text' 	=> 'list',
                'tab_list' => array(
                    'index/index'
                )
            )
        )
    );
$menu_arr[]=
    array(
        'text' 	=> lang('Public wall'),
        'letter_text' 	=> '',
        'sub_menu_list' =>array(
            array(
                'text' 	=> lang('Public wall'),
                'tab_list' => array(
                    'index/wall'
                )
            ),
            array(
                'text' 	=> lang('Friends'),
                'tab_list' => array(
                    'index/friend'
                )
            ),
            array(
                'text' 	=> lang('My Gallery'),
                'tab_list' => array(
                    'index/gallery'
                )
            ),
        )
    );
$menu_arr[]=
    array(
        'text' 	=> lang('Chat room'),
        'letter_text' 	=> '',
        'sub_menu_list' =>array(
            array(
                'text' 	=> 'list',
                'tab_list' => array(
                    'user/chat_room/index'
                )
            )
        )
    );
if(isset($data['user']) && !empty($data['user'])) {
    $menu_arr[]=
        array(
            'text' 	=> lang('Account'),
            'letter_text' 	=> 'fa fa-user',
            'sub_menu_list' =>array(
                array(
                    'text' 	=> lang('My Account'),
                    'tab_list' => array(
                        'user/profile/index',
                        'user/profile/edit'
                    )
                ),
                array(
                    'text' 	=> lang('Log out'),
                    'tab_list' => array(
                        'user/dashboard/logout'
                    )
                )
            )
        );
} else {
    $menu_arr[]=
        array(
            'text' 	=> lang('Log in'),
            'letter_text' 	=> '',
            'sub_menu_list' =>array(
                array(
                    'text' 	=> 'list',
                    'tab_list' => array(
                        'index/login',
                        'index/register',
                        'index/forgot_password'
                    )
                )
            )
        );
}




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
    $CI = get_instance();
    $routes = $CI->router->routes;
    $routes1 = array();
    foreach($routes as $key => $val){
        $routes1[$val] = $key;
    }

    $result_url="#";
    if(is_array($side_item_tab_list_item)){
        if(isset($routes1[$side_item_tab_list_item['uri']])){
            $uri=base_url($routes1[$side_item_tab_list_item['uri']]);
        }else{
            $uri=base_url($side_item_tab_list_item['uri']);
        }
        $params=@$side_item_tab_list_item['params'];
        if(!empty($params) && is_array($params)){
            $uri.="?";
            $uri.= http_build_query($params);
        }
        $result_url=$uri;
    }else{
        if(!empty($side_item_tab_list_item)){
            if(stripos($side_item_tab_list_item, "http://") !== false || stripos($side_item_tab_list_item, "https://") !== false){
                $result_url=$side_item_tab_list_item;
            }else{
                if(isset($routes1[$side_item_tab_list_item])){
                    $result_url=base_url($routes1[$side_item_tab_list_item]);
                }else{
                    $result_url=base_url($side_item_tab_list_item);
                }
            }
        }
    }
    return $result_url;
}

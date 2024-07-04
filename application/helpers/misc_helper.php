<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 产生验证码
 *
 * @param string $nchash 哈希数
 * @return string
 */
function makeSeccode($nchash, $is_mobile=false){
    if($is_mobile) {
        $seccode = strval(rand(1111, 9999));
        return encrypt(strtoupper($seccode) . "\t" . (time()) . "\t" . $nchash, MD5_KEY);
    } else {
        $seccode = random(6, 1);
        $seccodeunits = '';

        $s = sprintf('%04s', base_convert($seccode, 10, 23));
        $seccodeunits = 'ABCEFGHJKMPRTVXY2346789';
        if($seccodeunits) {
            $seccode = '';
            for($i = 0; $i < 4; $i++) {
                // $unit = ord($s{$i});
                $unit = ord($s[$i]);
                $seccode .= ($unit >= 0x30 && $unit <= 0x39) ? $seccodeunits[$unit - 0x30] : $seccodeunits[$unit - 0x57];
            }
        }
        setNcCookie('seccode'.$nchash, encrypt(strtoupper($seccode)."\t".(time())."\t".$nchash,MD5_KEY),31536000);
    }
    return $seccode;
}

function getSeccode($seccode_hash) {
    list($checkvalue, $checktime, $checkidhash) = explode("\t", decrypt($seccode_hash,MD5_KEY));
    return $checkvalue;
}

/**
 * 验证验证码
 *
 * @param string $nchash 哈希数
 * @param string $value 待验证值
 * @return boolean
 */
function checkSeccode($nchash,$value, $is_mobile=false, $mb_seccode_hash=''){
    $cur_time = TIMESTAMP;
    if($is_mobile) {
        list($checkvalue, $checktime, $checkidhash) = explode("\t", decrypt($mb_seccode_hash,MD5_KEY));
    } else {
        list($checkvalue, $checktime, $checkidhash) = explode("\t", decrypt(cookie('seccode'.$nchash),MD5_KEY));
    }
    $return = $checkvalue == strtoupper($value) && $checkidhash == $nchash;
    if (!$return) {
        if(!$is_mobile) {
            setNcCookie('seccode'.$nchash,'',-3600);
        }
    } else {
        if($is_mobile) {
            $mb_success_seccodes = rkcache('seccode_mb_success'.$nchash);
            foreach($mb_success_seccodes as $key=>$val) {
                if($cur_time - $key > 600) {
                    unset($mb_success_seccodes[$key]);
                }
            }
            if(isset($mb_success_seccodes[$checktime]) && in_array($checkvalue, $mb_success_seccodes[$checktime])) {
                $return = false;
            } else if($cur_time - $checktime > 600) {
                $return = false;
            } else {
                $mb_success_seccodes[$checktime] = empty($mb_success_seccodes[$checktime]) ? array() : $mb_success_seccodes[$checktime];
                $mb_success_seccodes[$checktime][] = $checkvalue;
            }
            wkcache('seccode_mb_success'.$nchash, $mb_success_seccodes);
        }
    }
    return $return;
}

/**
 * 设置cookie
 *
 * @param string $name cookie 的名称
 * @param string $value cookie 的值
 * @param int $expire cookie 有效周期
 * @param string $path cookie 的服务器路径 默认为 /
 * @param string $domain cookie 的域名
 * @param string $secure 是否通过安全的 HTTPS 连接来传输 cookie,默认为false
 */
function setNcCookie($name, $value, $expire='3600', $path='', $domain='', $secure=false){
    if (empty($path)) $path = '/';
    $name = defined('COOKIE_PRE') ? COOKIE_PRE.$name : strtoupper(substr(md5(MD5_KEY),0,4)).'_'.$name;
    $expire = intval($expire)?intval($expire):(intval(SESSION_EXPIRE)?intval(SESSION_EXPIRE):3600);
    $result = setcookie($name, $value, time()+$expire, $path, $domain, $secure);
    $_COOKIE[$name] = $value;
}

/**
 * 取得COOKIE的值
 *
 * @param string $name
 * @return unknown
 */
function cookie($name= ''){
    $name = defined('COOKIE_PRE') ? COOKIE_PRE.$name : strtoupper(substr(md5(MD5_KEY),0,4)).'_'.$name;
    return $_COOKIE[$name];
}

/**
 * 取上一步来源地址
 *
 * @param
 * @return string 字符串类型的返回结果
 */
function getReferer(){
    return empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
}
/**
 * 取验证码hash值
 *
 * @param
 * @return string 字符串类型的返回结果
 */
function getNchash($act='', $op=''){
    $act = empty($act) ? $_GET['act'] : $act;
    $op = empty($op) ? $_GET['op'] : $op;
    return substr(md5(APPPATH.$act.$op),0,8);
}

/**
 * 加密函数
 *
 * @param string $txt 需要加密的字符串
 * @param string $key 密钥
 * @return string 返回加密结果
 */
function encrypt($txt, $key = ''){
    if (empty($txt)) return $txt;
    if (empty($key)) $key = md5(MD5_KEY);
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
    $ikey ="-x6g6ZWm2G9g_vr0Bo.pOq3kRIxsZ6rm";
    $nh1 = rand(0,64);
    $nh2 = rand(0,64);
    $nh3 = rand(0,64);
    // $ch1 = $chars{$nh1};
    // $ch2 = $chars{$nh2};
    // $ch3 = $chars{$nh3};
    $ch1 = $chars[$nh1];
    $ch2 = $chars[$nh2];
    $ch3 = $chars[$nh3];
    $nhnum = $nh1 + $nh2 + $nh3;
    $knum = 0;$i = 0;
    while(isset($key[$i])) $knum +=ord($key[$i++]);
    $mdKey = substr(md5(md5(md5($key.$ch1).$ch2.$ikey).$ch3),$nhnum%8,$knum%8 + 16);
    $txt = base64_encode(time().'_'.$txt);
    $txt = str_replace(array('+','/','='),array('-','_','.'),$txt);
    $tmp = '';
    $j=0;$k = 0;
    $tlen = strlen($txt);
    $klen = strlen($mdKey);
    for ($i=0; $i<$tlen; $i++) {
        $k = $k == $klen ? 0 : $k;
        $j = ($nhnum+strpos($chars,$txt[$i])+ord($mdKey[$k++]))%64;
        $tmp .= $chars[$j];
    }
    $tmplen = strlen($tmp);
    $tmp = substr_replace($tmp,$ch3,$nh2 % ++$tmplen,0);
    $tmp = substr_replace($tmp,$ch2,$nh1 % ++$tmplen,0);
    $tmp = substr_replace($tmp,$ch1,$knum % ++$tmplen,0);
    return $tmp;
}

/**
 * 解密函数
 *
 * @param string $txt 需要解密的字符串
 * @param string $key 密匙
 * @return string 字符串类型的返回结果
 */
function decrypt($txt, $key = '', $ttl = 0){
    if (empty($txt)) return $txt;
    if (empty($key)) $key = md5(MD5_KEY);

    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
    $ikey ="-x6g6ZWm2G9g_vr0Bo.pOq3kRIxsZ6rm";
    $knum = 0;$i = 0;
    $tlen = @strlen($txt);
    while(isset($key[$i])) $knum +=ord($key[$i++]);
    $ch1 = @$txt[$knum % $tlen];
    $nh1 = strpos($chars,$ch1);
    $txt = @substr_replace($txt,'',$knum % $tlen--,1);
    $ch2 = @$txt[$nh1 % $tlen];
    $nh2 = @strpos($chars,$ch2);
    $txt = @substr_replace($txt,'',$nh1 % $tlen--,1);
    $ch3 = @$txt[$nh2 % $tlen];
    $nh3 = @strpos($chars,$ch3);
    $txt = @substr_replace($txt,'',$nh2 % $tlen--,1);
    $nhnum = $nh1 + $nh2 + $nh3;
    $mdKey = substr(md5(md5(md5($key.$ch1).$ch2.$ikey).$ch3),$nhnum % 8,$knum % 8 + 16);
    $tmp = '';
    $j=0; $k = 0;
    $tlen = @strlen($txt);
    $klen = @strlen($mdKey);
    for ($i=0; $i<$tlen; $i++) {
        $k = $k == $klen ? 0 : $k;
        $j = strpos($chars,$txt[$i])-$nhnum - ord($mdKey[$k++]);
        while ($j<0) $j+=64;
        $tmp .= $chars[$j];
    }
    $tmp = str_replace(array('-','_','.'),array('+','/','='),$tmp);
    $tmp = trim(base64_decode($tmp));

    if (preg_match("/\d{10}_/s",substr($tmp,0,11))){
        if ($ttl > 0 && (time() - substr($tmp,0,11) > $ttl)){
            $tmp = null;
        }else{
            $tmp = substr($tmp,11);
        }
    }
    return $tmp;
}

/**
 * 取得随机数
 *
 * @param int $length 生成随机数的长度
 * @param int $numeric 是否只产生数字随机数 1是0否
 * @return string
 */
function random($length, $numeric = 0) {
    $seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
    $hash = '';
    $max = strlen($seed) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $seed[mt_rand(0, $max)];
    }
    return $hash;
}

if (!function_exists('encrypt_str')) {
    function encrypt_str($message){
        $max_msg_size = 1000;
        $message = substr($message, 0, $max_msg_size);
        $password = HASH_KEY;
        $salt = sha1(mt_rand());
        $iv = substr(sha1(mt_rand()), 0, 16);
        $encrypted = openssl_encrypt(
            "$message", 'aes-256-cbc', "$salt:$password", null, $iv
        );
        $msg_bundle = "$salt:$iv:$encrypted";
        return $msg_bundle;
    }
}
if (!function_exists('decrypt_str')) {
    function decrypt_str($msg_bundle){
        $password = HASH_KEY;
        $saved_bundle = $msg_bundle;
        $components = explode( ':', $saved_bundle );;
        $salt          = $components[0];
        $iv            = $components[1];
        $encrypted_msg = $components[2];

        $decrypted_msg = openssl_decrypt(
            "$encrypted_msg", 'aes-256-cbc', "$salt:$password", null, $iv
        );
        if ( $decrypted_msg === false ) {
            return "";
        }
        return $decrypted_msg;
    }
}

function getBrowser() {
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";
    // First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif(preg_match('/Firefox/i',$u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif(preg_match('/Chrome/i',$u_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif(preg_match('/Safari/i',$u_agent)) {
        $bname = 'Apple Safari';
        $ub = "Safari";
    } elseif(preg_match('/Opera/i',$u_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif(preg_match('/Netscape/i',$u_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
    } else {
        $bname = '';
        $ub = "";
    }
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        } else {
            $version= $matches['version'][1];
        }
    } else {
        $version= $matches['version'][0];
    }
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
    $agentInfo = array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
    $browserInfo = isset($agentInfo['name'])?$agentInfo['name']:"";
    $browserInfo.=" ";
    $browserInfo.=isset($agentInfo['version'])?$agentInfo['version']:"";
    return $browserInfo;
}

if (!function_exists('get_client_ip')) {
    function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = '0.0.0.0';
        return $ipaddress;
    }
}

if (!function_exists('generateRandomString')) {
    function generateRandomString($length = 4) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}


/**
 * Think API模式函数库
 */

/**
 * 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @param mixed $default 默认值
 * @return mixed
 */
function C($name=null, $value=null,$default=null) {
    static $_config = array();
    if(empty($_config)){
        $CI = get_instance();
        $CI->load->model('site_settings_model');
        $_config = $CI->site_settings_model->get_site_setting();
    }
    if(empty($name)){
        return $_config;
    }else{
        return isset($_config[$name])?$_config[$name]:null;
    }
}
function U($user_id = 0, $name=null, $value=null,$default=null) {
    static $_u_config = array();
    if(empty($_config)){
        $CI = get_instance();
        $CI->load->model('user_settings_model');
        $_u_config = $CI->user_settings_model->get_user_setting($user_id);
    }
    if(empty($name)){
        if(empty($_u_config)){
            $_u_config = new stdClass();
        }
        return $_u_config;
    }else{
        return isset($_u_config[$name])?$_u_config[$name]:null;
    }
}
function Balance_Info($user_id = 0, $name=null, $value=null,$default=null) {
    static $_u_config = array();
    if(empty($_config)){
        $CI = get_instance();
        $CI->load->model('bank_info_model');
        $_u_config = $CI->user_settings_model->get_user_setting($user_id);
    }
    if(empty($name)){
        if(empty($_u_config)){
            $_u_config = new stdClass();
        }
        return $_u_config;
    }else{
        return isset($_u_config[$name])?$_u_config[$name]:null;
    }
}
/**
 * 缓存管理
 * @param mixed $name 缓存名称，如果为数组表示进行缓存设置
 * @param mixed $value 缓存值
 * @param mixed $options 缓存参数
 * @return mixed
 */
function S($name, $sub_name='', $value='',$ttl=86400) {
    static $cache   =   '';

    if(empty($cache)) {
        $CI =& get_instance();
        $CI->load->driver('cache');
        $cache = & $CI->cache->file;
    }

    if(''=== $value){ // 获取缓存
        $val_array = $cache->get($name);
        if(empty($sub_name)) {
            return $val_array;
        } else {
            return isset($val_array[$sub_name]) ? $val_array[$sub_name] : null;
        }
    } elseif (is_null($value)) { // 删除缓存
        if(empty($sub_name)) {
            return $cache->delete($name);
        } else {
            $val_array = $cache->get($name);
            if(isset($val_array[$sub_name])) {
                unset($val_array[$sub_name]);
                return $cache->save($name, $val_array, $ttl);
            }
        }
    }else { // 缓存数据
        if(empty($sub_name)) {
            return $cache->save($name, $value, $ttl);
        } else {
            $val_array = $cache->get($name);
            $val_array = is_array($val_array) ? $val_array : array();
            $val_array[$sub_name] = $value;
            return $cache->save($name, $val_array, $ttl);
        }
    }
}

/**
 * 快速文件数据读取和保存 针对简单类型数据 字符串、数组
 * @param string $name 缓存名称
 * @param mixed $value 缓存值
 * @return mixed
 */
function F($name, $is_reload=false) {
    static $cache   =   '';

    $CI =& get_instance();
    if(empty($cache)) {
        $CI->load->driver('cache');
        $cache = & $CI->cache->file;
    }

    if($is_reload) {
        $value = false;
        switch($name) {
            case 'setting':
                $CI->load->model('setting_model');
                $value = $CI->setting_model->get_all_settings();
                break;
            case 'categories':
                $CI->load->model('product_category_model');
                $value = $CI->product_category_model->get_all_categories();
                break;
        }
        $cache->save($name, $value, 30 * 86400);
        return $value;
    } else {
        return $cache->get($name);
    }
}

// number format
function price_format($price,$currency='$') {
    $result	= number_format($price, 2, '.', '');
    return $currency.$result;
}

function decimal_format($decimal) {
    $result	= number_format($decimal, 2, '.', '');
    return $result;
}

/**
 * @param bool $state
 * @param string $msg
 * @param array $data
 * @return array
 */
function callback($state = true, $msg = '', $data = array()) {
    return array('state' => $state, 'msg' => $msg, 'data' => $data);
}

function rearrange_by_key($array, $key) {
    if(is_array($array)) {
        $result = array();
        foreach($array as $v) {
            $result[$v[$key]] = $v;
        }
        return $result;
    } else {
        return $array;
    }
}

function relist_by_key($array, $key) {
    if(is_array($array)) {
        $result = array();
        foreach($array as $v) {
            $result[$v[$key]][] = $v;
        }
        return $result;
    } else {
        return $array;
    }
}

function rand_number ($length = 6)
{
    if($length < 1)
    {
        $length = 6;
    }

    $min = 1;
    for($i = 0; $i < $length - 1; $i ++)
    {
        $min = $min * 10;
    }
    $max = $min * 10 - 1;

    return rand($min, $max);
}

function get_mobile_country_code($country)
{
    return ($country == 'CN') ? '86' : '82';
}

function encrypt_mobile ($mobile)
{
    if(empty($mobile))
        return $mobile;

    // 处理手机号，不让前台显示
    $mobile = substr($mobile, 0, 3) . '*****' . substr($mobile, - 3);
    return $mobile;
}

function get_ip(){
    if (@$_SERVER['HTTP_CLIENT_IP'] && $_SERVER['HTTP_CLIENT_IP']!='unknown') {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (@$_SERVER['HTTP_X_FORWARDED_FOR'] && $_SERVER['HTTP_X_FORWARDED_FOR']!='unknown') {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
//    return preg_match('/^\d[\d.]+\d$/', $ip) ? $ip : '';
}

function str_to_timestamp($str){
    if ( preg_match('/^(?P<month>\d+)[-\/](?P<day>\d+)[-\/](?P<year>\d+)$/', $str, $matches) )
    {
        //print_r($matches); die;
        $timestamp = mktime(0, 0, 0, ( $matches['month'] ), $matches['day'], $matches['year']);
        return $timestamp;
    }else{
        return strtotime($str);
    }
}
function get_boxes($val){
    $str="";
    if(intval(@$val)>1){
        $str="s";
    }
    return $str;
}
function get_time_ago($timestamp){
    $timestamp=intval($timestamp);
    $cur_timestamp=time();
    $delta=intval($cur_timestamp-$timestamp);
    $duration=intval($delta);
    //print_r($duration); die;
    $year=intval($duration/(3600*24*30*365));
    $duration=$duration-$year*3600*24*30*365;
    $month=intval($duration/(3600*24*30));
    $duration=$duration-$month*3600*24*30;
    $day=intval($duration/(3600*24));
    $duration=$duration-$day*3600*24;
    $hour=intval($duration/3600);
    $duration=$duration-$hour*3600;
    $minute=intval($duration/60);
    $duration=$duration-$minute*60;
    $second=$duration;
    $time_str="Just Now";
    if($year===0 && $month===0 && $day===0 && $hour===0 && $minute===0){
        $time_str="JUST NOW";
    }elseif($year===0 && $month===0 && $day===0 && $hour===0){
        $time_str=$minute." MINUTE".strtoupper(get_boxes($minute))." AGO";
    }elseif($year===0 && $month===0 && $day===0){
        $time_str=$hour." HOUR".strtoupper(get_boxes($hour))." AGO";
    }elseif($year===0 && $month===0){
        $time_str=$day." DAY".strtoupper(get_boxes($day))." AGO";
    }elseif($year===0){
        $time_str=$month." MONTH".strtoupper(get_boxes($month))." AGO";
    }else{
        $time_str=$year." YEAR".strtoupper(get_boxes($year))." AGO";
    }
    return $time_str;
}

function makePaySn($member_id) {
    return mt_rand(10,99)
    . sprintf('%010d',time() - 946656000)
    . sprintf('%03d', (float) microtime() * 1000)
    . sprintf('%03d', (int) $member_id % 1000);
}
function makeOrderSn($pay_id) {
    static $num;
    if (empty($num)) {
        $num = 1;
    } else {
        $num ++;
    }
    return (date('y',time()) % 9+1) . sprintf('%013d', $pay_id) . sprintf('%02d', $num);
}
function array_under_reset($array, $key, $type=1){
    if (is_array($array)){
        $tmp = array();
        foreach ($array as $v) {
            if ($type === 1){
                $tmp[$v[$key]] = $v;
            }elseif($type === 2){
                $tmp[$v[$key]][] = $v;
            }
        }
        return $tmp;
    }else{
        return $array;
    }
}
function thumb($file_name, $placeholder = true, $dir_path = ''){
    if(empty($file_name)) {
        if($placeholder){
            $file_name =  placeholder();
        }
        return $file_name;
    }
    if($dir_path == ''){
        $dir_path = UPLOADS_PATH.DS.IMAGE_PATH;
    }
    $file_url = base_url().$dir_path.DS.$file_name;
    return $file_url;
}
function placeholder($type = 1){
    $file_url = base_url()."assets/global/img/placeholder".$type.".png";
    return $file_url;
}
function user_avatar($file_name, $placeholder = true, $dir_path = ''){
    $file_url = "";
    if(empty($file_name)) {
        if($placeholder){
            $file_url =  user_default_avatar();
        }
        return $file_url;
    }
    if($dir_path == ''){
        $dir_path = UPLOADS_PATH.DS.PROFILE_IMAGE_PATH;
    }
    if(file_exists(FCPATH.DS.$dir_path.DS.$file_name)){
        $file_url = base_url().$dir_path.DS.$file_name;
    }else{
        $file_url =  user_default_avatar();
    }
    return $file_url;
}
function user_default_avatar($type = 1){
    $file_url = base_url()."assets/global/img/default-avatar-".$type.".png";
    return $file_url;
}

function get_all_countries($code = ''){
    $file_path = FCPATH."/assets/global/other/CountryCodes.json";
    $myfile = fopen($file_path, "r") or die("Unable to open file!");
    $countries_json = fread($myfile, filesize($file_path));
    fclose($myfile);
    $countries_arr = json_decode($countries_json."", true);
    if(empty($code)) {
        $countries_arr = array_under_reset($countries_arr,'name',1);
        ksort($countries_arr);
        $adjusted_countries = array();
        foreach($countries_arr as $info){
            $adjusted_countries[]=$info;
        }
        return $adjusted_countries;
    } else {
        foreach($countries_arr as $key => $info){
            if($info['code']==$code){
                return $info;
            }
        }
        return array();
    }
}
function getFromUrl($url, $method = 'GET')
{
    $ch = curl_init();
    $agent = 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0)';

    switch(strtoupper($method))
    {
        case 'GET':
            curl_setopt($ch, CURLOPT_URL, $url);
            break;

        case 'POST':
            $info = parse_url($url);
            $url = $info['scheme'] . '://' . $info['host'] . $info['path'];
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $info['query']);
            break;

        default:
            return false;
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}
function sendXMLviaCurl($xmlRequest,$gatewayURL) {
    // helper function demonstrating how to send the xml with curl

    $ch = curl_init(); // Initialize curl handle
    curl_setopt($ch, CURLOPT_URL, $gatewayURL); // Set POST URL

    $headers = array();
    $headers[] = "Content-type: text/xml";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Add http headers to let it know we're sending XML
    $xmlString = $xmlRequest->saveXML();
    curl_setopt($ch, CURLOPT_FAILONERROR, 1); // Fail on errors
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Allow redirects
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return into a variable
    curl_setopt($ch, CURLOPT_PORT, 443); // Set the port number
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Times out after 30s
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlString); // Add XML directly in POST

    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);


    // This should be unset in production use. With it on, it forces the ssl cert to be valid
    // before sending info.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    if (!($data = curl_exec($ch))) {
        return false;
    }
    curl_close($ch);

    return $data;
}
function appendXmlNode($domDocument, $parentNode, $name, $value) {
    $childNode      = $domDocument->createElement($name);
    $childNodeValue = $domDocument->createTextNode($value);
    $childNode->appendChild($childNodeValue);
    $parentNode->appendChild($childNode);
}
function get_distance($lat1, $lon1, $lat2, $lon2, $unit) {
    if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        return 0;
    }
    else {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
   /* echo distance(32.9697, -96.80322, 29.46786, -98.53506, "M") . " Miles<br>";
    echo distance(32.9697, -96.80322, 29.46786, -98.53506, "K") . " Kilometers<br>";
    echo distance(32.9697, -96.80322, 29.46786, -98.53506, "N") . " Nautical Miles<br>";*/
}
function adjust_phone_number($mobile){
    $mobile1 = str_replace("+","",$mobile);
    return $mobile1;
}
function valid_phone_number($mobile_number, $prefix=''){
    $prefix = str_replace("+","",$prefix);
    $number = $prefix.$mobile_number;
    $number = filter_var($number, FILTER_SANITIZE_NUMBER_INT);
    $number = str_replace("+","",$number);
    $phone_to_check = str_replace("-", "", $number);
    // Check the lenght of number
    // This can be customized if you want phone number from a specific country
    if (strlen($phone_to_check) < 10 || strlen($phone_to_check) > 14) {
        return array(false, $number);
    } else {
        $mobile_number = str_replace("+","",$mobile_number);
        $mobile_number = str_replace("-", "", $mobile_number);
        return array($mobile_number, $prefix);
    }
}
function is_valid_email($email){
    if(filter_var($email, FILTER_VALIDATE_EMAIL) !== false){
        return $email;
    }else{
        return false;
    }
}
function get_message_template($template_id, $adjusted_template = true){
    $file_path = FCPATH."/assets/global/email_template/email_template_".$template_id.".html";
    $myfile = fopen($file_path, "r");
    if(empty($myfile)){
        return "";
    }
    $html = fread($myfile, filesize($file_path));
    fclose($myfile);
    if($adjusted_template) {
        $logo_url = base_url()."uploads/".IMAGE_PATH."/".C('logo_img');//base_url().'assets/home/images/HLFX_Logo_White-Green_TM-01.png';
        $html = str_replace("%%site_title%%", C('app_name'), $html);
        $html = str_replace("%%logo_url%%", $logo_url, $html);
        $html = str_replace("%%site_url%%", base_url(), $html);
    }
    return $html;
}
function get_site_version($is_dev = true){
    if($is_dev){
        $v = TIMESTAMP;
    }else{
        $v = '1.0';
    }
    return $v;
}
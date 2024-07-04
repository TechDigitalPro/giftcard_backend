<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function get_client_type(){
    $client = 'android';
    if(isset($_GET['client']) && !empty($_GET['client'])){
        $client = $_GET['client'];
    }
    return $client;
}
function get_api_version(){
    $api_version = '1';
    if(isset($_GET['api_version']) && !empty($_GET['api_version'])){
        $api_version = $_GET['api_version'];
    }
    return $api_version;
}
function output_data($data){
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");

    $result = array('status'=>true, 'data'=>$data, 'api_version'=>get_api_version());

    echo json_encode($result); die;
}

function output_error($msg = "", $data = array()){
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");

    if(empty($msg)) $msg = "Parameter error";
    $result = array('status'=>false, 'message'=>$msg, 'api_version'=>get_api_version());
    if(!empty($data)) $result['data'] = $data;
    echo json_encode($result); die;
}
function get_param_from_request($key, $request = 'POST'){
    if($request=='POST'){
        $value = isset($_POST[$key])?$_POST[$key]:"";
    }else{
        $value = isset($_GET[$key])?$_GET[$key]:"";
    }
    if (empty($value) && $value != 0) {
        output_error($key." filed is missing");
    }
    return $value;
}

class Admin_controller extends MY_Controller{
    protected $api_version;
    protected $client;
    protected $token;
    protected $user_info;

    public function __construct(){
        parent::__construct();
        $this->api_version = get_api_version();
        $this->client = get_client_type();
        $this->load->model('admin_model');
        $this->load->model('mb_token_model');
        $this->load->model('access_log_model');
        $this->load->model('user_model');
    }

    protected function _removeOtherToken($memberKey){
        $token_detail = $this->mb_token_model->get(array('token'=>$memberKey));
        if(empty($token_detail)) return false;
        $condition = array('user_id'=>$token_detail['user_id']);
        $token_list = $this->mb_token_model->get_all($condition);
        if(empty($token_list)) $token_list = array();
        foreach($token_list as $token_info){
            if($token_info['token'] != $memberKey){
                $where = array('token'=>$token_info['token']);
                $this->mb_token_model->delete($where);
            }
        }
    }

    /**
     * Get header Authorization
     * */
    function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    /**
     * get access token from header
     * */
    function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        // HEADER: 1Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    protected function check_login() {
        //$token = get_param_from_request('token','GET');
        // $token = $this->getBearerToken();
        // if(empty($token)) output_error("Parameter error", array('login_status'=>'0'));
        // $condition = array('token'=>$token);
        // $token_info = $this->mb_token_model->get($condition);
        // if(empty($token_info)) output_error("Token is Expired. Try login, again", array('login_status'=>'0'));
        // $user_id = $token_info['user_id'];
        // $where = array('id'=>$user_id);
        // if ($token_info['user_type'] == "admin") {
        //     $where = array('admin_id'=>$user_id);
        // }
        $user_id = 1;
        $where = array('admin_id'=>$user_id);
        $user_info = "";
        if ($token_info['user_type'] == "admin") {
            $user_info = $this->admin_model->get($where);
        } else {
            $user_info = $this->user_model->get($where);
        }
        if(empty($user_info)) output_error("User does not exist", array('login_status'=>'0'));
        $this->token = "c4ca4238a0b923820dcc509a6f75849bcaa90907e3323676c742c07cfd78b18b";
        $this->user_info = $user_info;
        //$this->_removeOtherToken($memberKey);
    }

    public function upload_file($path, $name){ //upload profile image
        $this->load->library('upload');
        list($result, $upload_file_name) = $this->upload->upload_to_local($path,$name);

        if(!$result)
            return '';

        //$pic_file = FCPATH.UPLOADS_PATH."/{$save_path}/{$upload_file_name}";

        return $upload_file_name;
    }
    
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");
function get_client_type(){
    $client = 'android';
    if(isset($_GET['device_type']) && !empty($_GET['device_type'])){
        $client = $_GET['device_type'];
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
    $result = array('res'=>'success', 'api_result'=>$data, 'api_version'=>get_api_version());

    echo json_encode($result); die;
}

function output_error($msg = "", $data = array()){
    if(empty($msg)) $msg = "Parameter error";
    $result = array('res'=>'fail', 'err_msg'=>$msg, 'api_version'=>get_api_version());
    if(!empty($data)) $result['data'] = $data;
    echo json_encode($result); die;
}

function get_param_from_request($key, $request = 'POST'){
    if($request=='POST'){
        $value = isset($_POST[$key])?$_POST[$key]:"";
    }else{
        $value = isset($_GET[$key])?$_GET[$key]:"";
    }
    return $value;
}
//////////////////////////////////////Mobile base controller//////////////////////////////////////////////////
class Mobile_controller extends MY_Controller{
    protected $api_version;
    protected $client;
    public function __construct(){
        parent::__construct();
        $this->api_version = get_api_version();
        $this->client = get_client_type();
        $this->load->model('user_model');
        $this->load->model('admin_model');
    }

    protected function check_user_exist($condition, $user_id = 0){
        $sql = "select id from ".USER_TB." where ".key($condition)." = '".current($condition)."' and id<>".$user_id;
        $query = $this->db->query($sql);
        $result = $query->num_rows();
        if(!empty($result)){
            return true;
        }else{
            return false;
        }
    }
    protected function get_user_info($condition){
        $user_info = $this->user_model->get($condition);
        if(empty($user_info)){
            return array();
        }
        return $user_info;
    }
    protected function write_file($data, $file_path, $flag = 'w'){;
        $myfile = fopen($file_path, $flag) or die("Unable to open file!");
        fwrite($myfile, $data);
        fclose($myfile);
        return true;
    }
    protected function read_file($file_path){;
        $myfile = fopen($file_path, 'r');
        $contents = fread($myfile, filesize($file_path));
        fclose($myfile);
        return $contents;
    }
    protected function upload_file($save_path, $form_field){ //upload profile image
        $this->load->library('upload');
        list($result, $upload_file_name) = $this->upload->upload_to_local($save_path,$form_field);
        if(!$result)
            return '';
        //$pic_file = FCPATH.UPLOADS_PATH."/{$save_path}/{$upload_file_name}";
        return $upload_file_name;
    }
    protected function upload_file_s3($file_key = 'image'){
        $this->load->library('aws_s3');
        $user_info = $this->user_info;
        if(isset($_FILES[$file_key])){
            $image = $this->aws_s3->upload_file_obj("image", "profile/", $user_info['id']);
            if(!empty($image)){
                return $image;
            }
        }
        return false;
    }
    
/*    protected function upload_file_s3($save_path = "123/", $form_field = "upload_file"){
        $s3 = new Aws\S3\S3Client([
            'region'  => AWS_BUCKET_REGION,
            'version' => 'latest',
            'credentials' => [
                'key'    => AWS_ACCESS_KEY_ID,
                'secret' => AWS_SECRET_ACCESS_KEY,
            ]
        ]);
        $file_name = $this->upload_file("tmp", $form_field);
        if(empty($file_name)){
            output_error('Unable to upload file (local)');
        }
        $filepath = FCPATH.UPLOADS_PATH."/tmp/".$file_name;

        //print_r($filepath); die;

        // Send a PutObject request and get the result object.
        $key = basename($filepath); //your file name in the bucket
        $result = $s3->putObject([
            'Bucket' => AWS_BUCKET,
            'Key'    => $save_path."aaa.png",
            'Body' => fopen($filepath, 'rb'),
            'ContentType' => mime_content_type($filepath),
            'ACL'    => 'public-read-write'
            //'SourceFile' => 'c:\samplefile.png' //-- use this if you want to upload a file from a local location
        ]);
        @unlink($filepath);

        // Print the body of the result by indexing into the result object.
        print_r($result);
    }
    public function create_folder_s3(){
        $s3 = new Aws\S3\S3Client([
            'region'  => AWS_BUCKET_REGION,
            'version' => 'latest',
            'credentials' => [
                'key'    => AWS_ACCESS_KEY_ID,
                'secret' => AWS_SECRET_ACCESS_KEY,
            ]
        ]);
        $key = $this->input->post('key');
        $result = $s3->putObject([
            'Bucket' => AWS_BUCKET,
            'Key'    => $key,
            'Body' => "",
            'ACL'    => 'public-read-write'
            //'SourceFile' => 'c:\samplefile.png' //-- use this if you want to upload a file from a local location
        ]);
        //@unlink($filepath);
        // Print the body of the result by indexing into the result object.
        print_r($result);
    }
    public function delete_file_s3(){
        $s3 = new Aws\S3\S3Client([
            'region'  => AWS_BUCKET_REGION,
            'version' => 'latest',
            'credentials' => [
                'key'    => AWS_ACCESS_KEY_ID,
                'secret' => AWS_SECRET_ACCESS_KEY,
            ]
        ]);
        $key = $this->input->post('key');
        $result = $s3->deleteObject([
            'Bucket' => AWS_BUCKET,
            'Key'    => $key
        ]);
        print_r($result);
    }*/
    protected function _removeOtherToken($token){
        $token_detail = $this->mb_token_model->get(array('token'=>$token));
        if(empty($token_detail)) return false;
        $condition = array('user_id'=>$token_detail['user_id']);
        $token_list = $this->mb_token_model->get_all($condition);
        if(empty($token_list)) $token_list = array();
        foreach($token_list as $token_info){
            if($token_info['token'] != $token){
                $where = array('token'=>$token_info['token']);
                $this->mb_token_model->delete($where);
            }
        }
    }
    protected function _send_verification_code($user_id){
        $user_info = $this->user_model->get(array('id'=>$user_id));
        if(empty($user_info)) output_error("Account does not exist");

        $prefix_mobile = $user_info['prefix_mobile'];
        $user_phone = $prefix_mobile.$user_info['user_phone'];
        $prefix_mobile = intval($prefix_mobile);
        $user_phone_1 = $prefix_mobile.$user_info['user_phone'];
        $code = random(5,1);
        $verification_info = array(
            'user'=>$user_phone,
            'type'=>'register',
            'code'=>$code
        );
        $this->verification_codes_model->add_verification_code($verification_info);
        $subject = "Your verification code: ".$code;
        list($result, $message) = $this->send_sms($user_phone_1, $subject);
        return array($result, $message);
    }
    protected function _send_verification_email($user_id){
        $user_info = $this->user_model->get(array('id'=>$user_id));
        if(empty($user_info)) output_error("Account does not exist");

        $user_email = $user_info['user_email'];
        $code = random(5,1);
        $verification_info = array(
            'user'=>$user_email,
            'type'=>'register',
            'code'=>$code
        );
        $id = $this->verification_codes_model->add_verification_code($verification_info);
        $message_templage = get_message_template(0);
        $message_templage = str_replace("%%first_name%%", $user_info['user_name'], $message_templage);

//        $verification_link = base_url('home/main/account_activate').'?key='.base64_encode($id);
        $verification_link = base_url('home/main/account_activate').'?key='.base64_encode($id);

        $message_templage = str_replace("%%verification_link%%", $verification_link, $message_templage);
        $message_templage = str_replace("%%app_name%%", APP_NAME, $message_templage);
        $subject = "Welcome to ".APP_NAME."!";
        $result = $this->send_email($user_info['user_email'], $subject, $message_templage);
        $message = 'Could not send email';
        if($result){
            $message = 'Email has been sent successfully';
        }
        return array($result, $message);
    }
    protected function _send_email_toadmin($user_id){
        $user_info = $this->user_model->get(array('id'=>$user_id));
        if(empty($user_info)) output_error("Account does not exist");

        $user_email = $user_info['user_email'];

        $message_templage = get_message_template(2);
        $message_templage = str_replace("%%user_name%%", $user_info['user_name'], $message_templage);
        $message_templage = str_replace("%%admin_link%%", base_url("admin"), $message_templage);

        $message_templage = str_replace("%%app_name%%", APP_NAME, $message_templage);
        $subject = "Welcome to ".APP_NAME."!";
        $result = $this->send_email("support@ZIMBO.com", $subject, $message_templage);
        $message = 'Could not send email';
        if($result){
            $message = 'Email has been sent successfully';
        }
        return array($result, $message);
    }
    protected function add_log($data){
        $log_file = APPPATH."/logs/log.txt";
        $myfile = fopen($log_file, "a") or die("Unable to open file!");
        fwrite($myfile, "--------------------------------------------------------------------------------------------------------------------------------------------\n");
        fwrite($myfile, "request: ".$data."\n");
        fwrite($myfile, "--------------------------------------------------------------------------------------------------------------------------------------------\n");
        fclose($myfile);
    }
}

class Mobile_member_controller extends Mobile_controller{
    protected $token;
    protected $user_info;
    public function __construct(){
        parent::__construct();
    }
    protected function check_login() {
        //$token = get_param_from_request('token','GET');
        $token = $this->getBearerToken();
        if(empty($token)) output_error("Parameter error", array('login_status'=>'0'));
        $condition = array('token'=>$token);
        $token_info = $this->mb_token_model->get($condition);
        if(empty($token_info)) output_error("Token is Expired. Try login, again", array('login_status'=>'0'));
        $user_id = $token_info['user_id'];
        $where = array('id'=>$user_id);
        if ($token_info['user_type'] == "admin") {
            $where = array('admin_id'=>$user_id);
        }
        
        $user_info = $this->user_model->get($where);
        if ($token_info['user_type'] == "admin") {
            $user_info = $this->admin_model->get($where);
        }
        if(empty($user_info)) output_error("User does not exist", array('login_status'=>'0'));
        $this->token = $token;
        $this->user_info = $user_info;
        //$this->_removeOtherToken($memberKey);
    }
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

    

}

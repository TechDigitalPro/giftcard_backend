<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'third_party/phpMailer/PHPMailerAutoload.php';

class MY_Controller extends CI_Controller{

    public function showMessage($msg, $url='', $show_type='html', $is_show=1, $time=3000){
        if($this->check_ajax())
            $this->json_output_data(null, $msg, $url);
        $url = ($url!='' ? $url : getReferer());

        switch ($show_type){
            case 'json':
                $return = '{';
                $return .= '"msg":"'.$msg.'",';
                $return .= '"url":"'.$url.'"';
                $return .= '}';
                echo $return;
                break;
            case 'javascript':
                echo "<script>";
                echo "alert('". $msg ."');";
                echo "location.href='". $url ."'";
                echo "</script>";
                exit;
                break;
            default:
                if (is_array($url)){
                    foreach ($url as $k => $v){
                        $url[$k]['url'] = $v['url']?$v['url']:getReferer();
                    }
                }
                $data = array(
                    'html_title'=>'system information',
                    'msg'=>$msg,
                    'redirect_url'=>$url,
                    'msg_type'=>'succ',
                    'is_show'=>$is_show,
                    'msg_duration'=>$time
                );
                $this->page->message($data);
        }
        exit;
    }

    public function showError($msg, $url='', $time=3000){

        if($this->check_ajax())
            $this->json_output_error($msg, $url);
        $url = ($url!='' ? $url : getReferer());

        if (is_array($url)){
            foreach ($url as $k => $v){
                $url[$k]['url'] = $v['url']?$v['url']:getReferer();
            }
        }
        $data = array(
            'html_title'=>'system information',
            'msg'=>$msg,
            'redirect_url' => $url,
            'msg_type' => 'error',
            'is_show' => 1,
            'msg_duration'=>$time
        );
        $this->page->message($data);
        exit;
    }

    public function showDialog($message = '', $url = '', $alert_type = 'error', $extrajs = '', $time = 2){

        if (empty($_GET['inajax'])){
            if ($url == 'reload') $url = '';
            if($alert_type === 'succ')
                $this->showMessage($message.$extrajs, $url,'html');
            else
                $this->showError($message.$extrajs,$url);
        }

        $message = str_replace("'", "\\'", strip_tags($message));

        $paramjs = '';
        if ($url == 'reload'){
            $paramjs = 'window.location.reload()';
        }elseif ($url != ''){
            $paramjs = 'window.location.href =\''.$url.'\'';
        }
        if ($paramjs){
            $paramjs = 'function (){'.$paramjs.'}';
        }else{
            $paramjs = 'null';
        }
        $modes = array('error' => 'alert', 'succ' => 'succ', 'notice' => 'notice','js'=>'js');
        $cover = $alert_type == 'error' ? 1 : 0;
        $extra = 'showDialog(\''.$message.'\', \''.$modes[$alert_type].'\', null, '.($paramjs ? $paramjs : 'null').', '.$cover.', null, null, null, null, '.(is_numeric($time) ? $time : 'null').', null);';
        $extra = $extra ? '<script type="text/javascript" reload="1">'.$extra.'</script>' : '';
        if ($extrajs != '' && substr(trim($extrajs),0,8) != '<script>'){
            $extrajs = '<script type="text/javascript" reload="1">'.$extrajs.'</script>';
        }
        $extra .= $extrajs;

        ob_end_clean();
        @header("Expires: -1");
        @header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
        @header("Pragma: no-cache");
        @header("Content-type: text/xml; charset=".CHARSET);

        $string =  '<?xml version="1.0" encoding="'.CHARSET.'"?>'."\r\n";
        $string .= '<root><![CDATA['.$message.$extra.']]></root>';
        echo $string;exit;
    }

    public function makecode($page = 'admin'){
        $nchash = getNchash($page, 'login');
        $seccode = makeSeccode($nchash);
        @header("Expires: -1");
        @header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
        @header("Pragma: no-cache");

        $this->load->library('seccode');
        $this->seccode->code = $seccode;
        $this->seccode->width = 90;
        $this->seccode->height = 26;
        $this->seccode->background = 1;
        $this->seccode->adulterate = 1;
        $this->seccode->scatter = '';
        $this->seccode->color = 1;
        $this->seccode->size = 0;
        $this->seccode->shadow = 1;
        $this->seccode->animator = 0;
        $this->seccode->datapath =  ASSETS_PATH.'/global/seccode/';
        $this->seccode->display();
    }
    public function check_submit(){
        $action = $this->input->post_get('action');
        return ($action && $action === 'submit');
    }
    public function check_ajax(){
        $ajax = $this->input->get('is_ajax');
        if(!$ajax)
            $ajax = $this->input->post('is_ajax');
        return $ajax && $ajax == 1;
    }
    protected function json_output_error($message, $redirect = '') {
        echo json_encode(array('status'=>'0','message'=>$message, 'redirect' =>$redirect));
        die;
    }
    protected function json_output_data($data, $message = '', $redirect = '') {
        echo json_encode(array('status'=>'1','message'=>$message,'data'=>$data, 'redirect'=>$redirect));
        die;
    }
    protected function get_cur_page(){
        $cur_page = $this->input->get('cur_page');
        if(empty($cur_page))
            $cur_page = 1;
        return $cur_page;
    }
    protected function get_user_id(){
        $user_id = 0;
        if($this->session->userdata('user_logged_in')) {
            $user_id = $this->session->userdata('user_id');
            $user_id = ($user_id && $user_id > 0) ? intval($user_id) : 0;
        }
        return $user_id;
    }
    protected function get_site_setting(){
        $CI=&get_instance();
        $query = $CI->db->get(SETTING_TB);
        $res=$query->result_array();
        if(empty($res)){
            return array();
        }else{
            return $res[0];
        }
    }
    protected function update_site_setting($data){
        if(empty($data)) return false;
        $CI=&get_instance();
        $setting_arr=$this->get_site_setting();
        $update_data=array();
        foreach ($data as $key =>$value){
            if(array_key_exists($key,$setting_arr)){
                $update_data[$key]=$value;
            }
        }
        $res = $CI->db->update(SETTING_TB,$update_data,array('id'=>$setting_arr['id']));
        return $res;
    }
    protected function send_email($to,$subject,$message)
    {
       if(EMAIL_GATEWAY == 'phpmailer'){
           $headers = "From: " . "noreply@higherlevelfx.com" . "\r\n";
           $headers .= "Reply-To: "."no-reply@higherlevelfx.com" . "\r\n";
           $headers .= "CC: ".APP_NAME."\r\n";
           $headers .= "MIME-Version: 1.0\r\n";
           $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
           mail($to, $subject, $message, $headers);

          /* $headers = "From: " . $from_email . "\r\n";
           $headers .= "Reply-To: ".$from_email . "\r\n";
           $headers .= "CC: "."HigherLevelFX"."\r\n";
           $headers .= "MIME-Version: 1.0\r\n";
           $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
           $result = mail($to, $subject, $message, $headers);
           return $result;*/

           return true;
       }elseif(EMAIL_GATEWAY=='gmail'){
            $mail = new PHPMailer(); // create a new object
            $mail->IsSMTP(); // enable SMTP
            $mail->SMTPDebug = 0; //debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth = true; // authentication enabled
            $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
            $mail->Host = EHOST;
            $mail->Port = SMTP_PORT;
            $mail->IsHTML(true);
            $mail->Username = EUSERNAME;
            $mail->Password = EPASSWORD;
            $mail->SetFrom(ESENDERNAME);
            $mail->FromName=EUSERNAME;
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->AddAddress($to);
            if(!$mail->Send()) {
                return false;
            } else {
                return true;
            }
        }
        else{
            $config = array(
                'protocol' => 'smtp',
                'smtp_host' => SMTP_HOST,
                'smtp_user' => SMTP_USER,
                'smtp_pass' => SMTP_PASS,
                'smtp_port' => SMTP_PORT,
                'mailtype' => 'html',
                'charset'=>'utf-8',
                'crlf'=>"\r\n",
                'newline'=>"\r\n",
                'smtp_timeout' => 30,
                'smtp_crypto'=>'tls'
            );
            $this->load->library('email',$config);
            $this->email->set_newline("\r\n");
            $this->email->from(SMTP_SENDER, 'GetAppInstall');
            $this->email->to($to, 'Test To');
            $this->email->subject($subject);
            $this->email->message($message);

            if($this->email->send()){
                return;
            }else{
                return;
                //print_r($this->email->print_debugger());
            }
        }

    }

    protected function send_sms($to,$message){ //https://www.twilio.com/console/sms/settings/geo-permissions
        if(empty($to)){
            output_error("Input valid phone number");
        }
        $sid = TWILIO_SID; // Your Account SID from www.twilio.com/console
        $token = TWILIO_TOKEN; // Your Auth Token from www.twilio.com/console

        $client = new Twilio\Rest\Client($sid, $token);

        try{
            $send_result = $client->messages->create(
            // the number you'd like to send the message to
                "+".$to,
                [
                    // A Twilio phone number you purchased at twilio.com/console
                    'from' => TWILIO_PHONE_NUMBER,
                    // the body of the text message you'd like to send
                    'body' => $message
                ]
            );
            //print_r($send_result); die;
            $sms_id=$send_result->sid;
            return array(true, $sms_id);
        }catch (Exception $e){
            $message = $e->getMessage();
            //print_r($message); die;
            return array(false, $message);
        }
    }
}
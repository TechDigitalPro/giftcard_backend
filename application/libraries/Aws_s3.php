<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'third_party/aws-sdk-php/vendor/autoload.php';

class Aws_s3 {
    public $s3;
    function __construct(){
        $s3 = new Aws\S3\S3Client([
            'region'  => AWS_BUCKET_REGION,
            'version' => 'latest',
            'credentials' => [
                'key'    => AWS_ACCESS_KEY_ID,
                'secret' => AWS_SECRET_ACCESS_KEY,
            ]
        ]);
        $this->s3 = $s3;
    }
    function check_exist($key){
        $s3 = $this->s3;
        $result = $s3->doesObjectExist(AWS_BUCKET, $key);
        return $result;
    }
    function mk_dir($dir_name){
        $key = $dir_name;
        $s3 = $this->s3;
        $result = $s3->putObject([
            'Bucket' => AWS_BUCKET,
            'Key'    => $key,
            'Body' => "",
            'ACL'    => 'public-read-write'
        ]);
        if($result && isset($result['ObjectURL'])){
            return $result['ObjectURL'];
        }else{
            return false;
        }
    }
    function upload_file($filepath, $dir = ''){
        $s3 = $this->s3;
        $this->mk_dir($dir);
        // Send a PutObject request and get the result object.
        $key = basename($filepath); //your file name in the bucket
        $result = $s3->putObject([
            'Bucket' => AWS_BUCKET,
            'Key'    => $dir.$key,
            'Body' => fopen($filepath, 'rb'),
            'ContentType' => mime_content_type($filepath),
            'ACL'    => 'public-read-write'
            //'SourceFile' => 'c:\samplefile.png' //-- use this if you want to upload a file from a local location
        ]);

        // Print the body of the result by indexing into the result object.
        if($result && isset($result['ObjectURL'])){
            return $result['ObjectURL'];
        }else{
            return false;
        }
    }
    function upload_file_obj($form_field, $dir = '', $file_prefix='') {
        $s3 = $this->s3;
        $this->mk_dir($dir);
        //print_r($_FILES[$form_field]); die;
        $filepath = $_FILES[$form_field]['tmp_name'];
        $key = $dir.$file_prefix."_".time()."_".mt_rand(10000,99999)."_".$_FILES[$form_field]['name'];
        // Send a PutObject request and get the result object.
        $result = $s3->putObject([
            'Bucket' => AWS_BUCKET,
            'Key'    => $key,
            'Body' => fopen($filepath, 'rb'),
            'ContentType' => mime_content_type($filepath),
            'ACL'    => 'public-read-write'
            //'SourceFile' => 'c:\samplefile.png' //-- use this if you want to upload a file from a local location
        ]);
        //unlink($filepath);
        if($result && isset($result['ObjectURL'])){
            return $result['ObjectURL'];
        }else{
            return false;
        }
    }
    function upload_multi_file_obj($form_field, $dir = '', $file_prefix='') {
        $s3 = $this->s3;
        $this->mk_dir($dir);
        //print_r($_FILES[$form_field]); die;
        $image_urls = array();
        $file_cnt = count($_FILES[$form_field]['tmp_name']);
        for($i=0; $i<$file_cnt; $i++){
            $filepath = $_FILES[$form_field]['tmp_name'][$i];
            $key = $dir.$file_prefix."_".time()."_".mt_rand(10000,99999)."_".$_FILES[$form_field]['name'][$i];
            // Send a PutObject request and get the result object.
            $result = $s3->putObject([
                'Bucket' => AWS_BUCKET,
                'Key'    => $key,
                'Body' => fopen($filepath, 'rb'),
                'ContentType' => mime_content_type($filepath),
                'ACL'    => 'public-read-write'
                //'SourceFile' => 'c:\samplefile.png' //-- use this if you want to upload a file from a local location
            ]);
            if($result && isset($result['ObjectURL'])){
                $image_urls[]=$result['ObjectURL'];
            }
        }
        return $image_urls;
    }
    function delete_obj($key){
        $s3 = $this->s3;
        $result = $s3->deleteObject([
            'Bucket' => AWS_BUCKET,
            'Key'    => $key
        ]);
        return $result;
    }
    function delete_dir($dir_key){
        $rslt = $this->delete_obj($dir_key);
        return $rslt;
    }
    function delete_file($file_url){
        $key = $this->get_key_from_url($file_url);
        $rslt = $this->delete_obj($key);
        return $rslt;
    }
    function get_key_from_url($url){
        $url_obj = parse_url($url);
        //print_r($url_obj); die;
        $path = $url_obj['path'];
        $key = substr($path, 1);
        return $key;
    }
}
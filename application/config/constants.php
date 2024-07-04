<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

define('TIMESTAMP',time());
defined('MD5_KEY')       OR define('MD5_KEY', '012cbe9c3907e225e414294fbefe7994');
defined('HASH_KEY')       OR define('HASH_KEY', 'abcd1234567890');

defined('UPLOAD_IMAGE_WIDTH_MAX')   OR define('UPLOAD_IMAGE_WIDTH_MAX', 4096);
defined('UPLOAD_IMAGE_HEIGHT_MAX')  OR define('UPLOAD_IMAGE_HEIGHT_MAX', 4096);
defined('UPLOAD_FILE_SIZE_MAX')     OR define('UPLOAD_FILE_SIZE_MAX', 40960000000);
defined('PRODUCT_IMAGE_MAX')        OR define('PRODUCT_IMAGE_MAX', 5);


define("APP_NAME", "GiftCard");

///////////////////////////////////////database tables///////////////////////////////////////////////
define('DB_PREFIX','');

define('ADMIN_TB', DB_PREFIX.'admin');
define('USER_TB',DB_PREFIX.'users');
define('MB_TOKEN_TB',DB_PREFIX.'mb_user_token');
define('ACCESS_LOG_TB',DB_PREFIX.'access_log');
define('USER_SETTING_TB',DB_PREFIX.'user_settings');
define('NOTIFICATION_TB',DB_PREFIX.'notifications');

define('STORY_TB',DB_PREFIX.'story');
define('STORY_DES_TB',DB_PREFIX.'story_description');
define('ADDIOPIZZO_TB',DB_PREFIX.'addiopizzo');
define('CONFIGURATION_TB',DB_PREFIX.'configuration');
define('FRANCESE_TB',DB_PREFIX.'francese');
define('STORY_DETAIL_TB',DB_PREFIX.'story_detail');
define('PROJECT_TB',DB_PREFIX.'project');

define('USER_BUSINESS_TB',DB_PREFIX.'business_users');
define('USER_CONSUMER_TB',DB_PREFIX.'consumer_users');
define('QR_INFO_TB',DB_PREFIX.'qr_info');



define('EMAIL_GATEWAY','gmail');//define('EMAIL_GATEWAY','gmail', 'phpmailer', 'cpanel_mailer', 'ses');//

define('SENDER_EMAIL','zimbo@gmail.com');

define('SMTP_HOST','mail.smtp2go.com');
define('SMTP_USER','Zimbo'); /// ses-smtp-user.20200430-010450
define('SMTP_PASS','AxrTpX6yW4xoJDkR');
define('SMTP_PORT',2525);
define('SMTP_SENDER','support@zimbo.com');

define("EHOST",'mail.smtp2go.com');
define("EUSERNAME",'Zimbo');
define("EPASSWORD",'AxrTpX6yW4xoJDkR');
define("ESENDERNAME",'no-reply@zimbo.me');

define("CHOST",'mail.smtp2go.com');
define("CUSERNAME",'Zimbo');
define("CPASSWORD",'AxrTpX6yW4xoJDkR');
define("CSENDERNAME",'support@zimbo.me');


define('SUPPORT_EMAIL','support@zimbo.me');
define('INFO_EMAIL','info@zimbo.me');

define('INAPP_PURCHASE_SECRET_KEY','4005e5c9b6a6459aa9274a1c225159d0');
define('INAPP_VERIFY_LIVE','https://buy.itunes.apple.com/verifyReceipt');
define('INAPP_VERIFY_SAND','https://sandbox.itunes.apple.com/verifyReceipt');

define('ONESIGNAL_APP_ID','243b650b-63e7-42c9-bfb9-9cff5a43afa6');
define('ONESIGNAL_AUTH_KEY','Y2ZhMTFlMTEtNmZlZi00YTY5LTk1NTYtYTJiMjRlYzRkZTdk'); //REST API KEYS

define('FIREBASE_SERVER_KEY','AAAA2xxTJJk:APA91bHugY_fYJss_7dmn3rJ9ps8ZHJ6ZYfnrFbv0ecUeVoVmYqcM7Z492reEv8cvr8NGwMPv0tf5XcxCiPNJdZDcKaTrYWSn4DsajEaCFWtQqV0eAiIlwue5WLDuRSD19VQIRKLgmCD'); //REST API KEYS


//////////////////////         PATH              ///////////////////////////////////////////////////
define('DS','/');
define('ASSETS_PATH', 'assets');
define('UPLOADS_PATH', 'uploads');
define('IMAGE_PATH', 'images');
define('PROFILE_IMAGE_PATH','profile');
define('COMMON_IMAGE_PATH','common');
define('USER_IMAGE_PATH','user');
define('LIBRARY_PATH', APPPATH . 'libraries');

<?php
//print_r($data); die;
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="shortcut icon" href="<?php echo $base_url."uploads/".IMAGE_PATH."/".C('favicon_img'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title><?php echo(C("app_name"));?></title>
    <meta name="description" content="<?php echo(@$data['site_setting']['meta_description']); ?>">
    <meta name="Keywords" content="<?php echo(@$data['site_setting']['seo_keywords']); ?>">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    <meta property="og:title" content="<?php echo(C("app_name"));?>" />
    <meta property="og:type" content='website' />
    <meta property="og:url" content="<?php echo $base_url; ?>" />
    <meta property="og:description" content="<?php echo(C("app_name"));?>"/>
    <meta property="og:image" content="<?php echo $base_url."uploads/".IMAGE_PATH."/".C('logo_img'); ?>"/>
    <meta property="og:site_name" content="<?php echo(C("app_name"));?>" />

    <!-- Bootstrap core CSS     -->
    <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
    <link href="<?php echo($theme_adv_login_url); ?>assets/css/bootstrap.min.css" rel="stylesheet" />
    <!--  Material Dashboard CSS    -->
    <link href="<?php echo($theme_adv_login_url); ?>assets/css/material-dashboard.css" rel="stylesheet" />

    <link rel="stylesheet" href="<?php echo($base_url); ?>assets/global/plugins/font-awesome/css/font-awesome.min.css">

    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/global/css/global.css?v=20170927-3">

    <!--  CSS for Demo Purpose, don't include it in your project     -->
    <link href="<?php echo($base_url); ?>assets/admin/css/login.css?v=20170924-5" rel="stylesheet" />
    <!--     Fonts and icons     -->

    <script type="text/javascript" src="<?php echo $base_url; ?>assets/global/js/config.js?v=20171121-1"></script>
    <!--   Core JS Files   -->
    <script src="<?php echo($theme_adv_login_url); ?>assets/js/jquery-3.1.1.min.js" type="text/javascript"></script>
    <script src="<?php echo($theme_adv_login_url); ?>assets/js/jquery-ui.min.js" type="text/javascript"></script>
    <script src="<?php echo($theme_adv_login_url); ?>assets/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo($theme_adv_login_url); ?>assets/js/material.min.js" type="text/javascript"></script>
    <script src="<?php echo($theme_adv_login_url); ?>assets/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo($theme_adv_login_url); ?>assets/js/function.js?v=20170927-1" type="text/javascript"></script>
    <script type="text/javascript" src="<?php echo $base_url; ?>assets/global/js/captcha.js"></script>
</head>

<body>
<div class="wrapper wrapper-full-page">
    <?php include($view_file_path); ?>
</div>
</body>
<!-- Forms Validations Plugin -->
<script src="<?php echo($theme_adv_login_url); ?>assets/js/jquery.validate.min.js"></script>
<!--  Plugin for Date Time Picker and Full Calendar Plugin-->
<script src="<?php echo($theme_adv_login_url); ?>assets/js/moment.min.js"></script>
<!--  Charts Plugin -->
<script src="<?php echo($theme_adv_login_url); ?>assets/js/chartist.min.js"></script>
<!--  Plugin for the Wizard -->
<script src="<?php echo($theme_adv_login_url); ?>assets/js/jquery.bootstrap-wizard.js"></script>
<!--  Notifications Plugin    -->
<script src="<?php echo($theme_adv_login_url); ?>assets/js/bootstrap-notify.js"></script>
<!-- DateTimePicker Plugin -->
<script src="<?php echo($theme_adv_login_url); ?>assets/js/bootstrap-datetimepicker.js"></script>
<!-- Vector Map plugin -->
<script src="<?php echo($theme_adv_login_url); ?>assets/js/jquery-jvectormap.js"></script>
<!-- Sliders Plugin -->
<script src="<?php echo($theme_adv_login_url); ?>assets/js/nouislider.min.js"></script>
<!--  Google Maps Plugin    -->

<!-- Select Plugin -->
<script src="<?php echo($theme_adv_login_url); ?>assets/js/jquery.select-bootstrap.js"></script>
<!--  DataTables.net Plugin    -->
<script src="<?php echo($theme_adv_login_url); ?>assets/js/jquery.datatables.js"></script>
<!-- Sweet Alert 2 plugin -->
<script src="<?php echo($theme_adv_login_url); ?>assets/js/sweetalert2.js"></script>
<!--	Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
<script src="<?php echo($theme_adv_login_url); ?>assets/js/jasny-bootstrap.min.js"></script>
<!--  Full Calendar Plugin    -->
<script src="<?php echo($theme_adv_login_url); ?>assets/js/fullcalendar.min.js"></script>
<!-- TagsInput Plugin -->
<script src="<?php echo($theme_adv_login_url); ?>assets/js/jquery.tagsinput.js"></script>
<!-- Material Dashboard javascript methods -->
<script src="<?php echo($theme_adv_login_url); ?>assets/js/material-dashboard.js"></script>
<!-- Material Dashboard DEMO methods, don't include it in your project! -->
<script src="<?php echo($theme_adv_login_url); ?>assets/js/demo.js"></script>
<script src="<?php echo $base_url; ?>assets/global/js/ads.js"></script>
<script type="text/javascript">
    if( window.adblockEnabled === undefined ){
        demo.showNotification('You need to disable ad blocker.','warning');
    }
</script>

<script type="text/javascript">
    $(document).ready(function() {
        demo.checkFullPageBackgroundImage();
        setTimeout(function() {
            // after 1000 ms we add the class animated to the login/register card
            $('.card').removeClass('card-hidden');
        }, 400);
    });
</script>

</html>
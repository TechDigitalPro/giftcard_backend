
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<head>
    <title><?php echo(C('site_name')); ?></title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo($base_url); ?>assets/images/favicon.png" />

    <!-- Web Fonts -->


    <!-- CSS Global Compulsory -->
    <link rel="stylesheet" href="<?php echo $theme_url; ?>plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $theme_url; ?>css/style.css">

    <!-- CSS Header and Footer -->
    <link rel="stylesheet" href="<?php echo $theme_url; ?>css/headers/header-default.css">
    <link rel="stylesheet" href="<?php echo $theme_url; ?>css/footers/footer-v1.css">

    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="<?php echo $theme_url; ?>plugins/animate.css">
    <link rel="stylesheet" href="<?php echo $theme_url; ?>plugins/line-icons/line-icons.css">
    <link rel="stylesheet" href="<?php echo $theme_url; ?>plugins/font-awesome/css/font-awesome.min.css">

    <!-- CSS Page Style -->
    <link rel="stylesheet" href="<?php echo $theme_url; ?>css/pages/page_error4_404.css">

    <!-- CSS Theme -->
    <link rel="stylesheet" href="<?php echo $theme_url; ?>css/theme-colors/default.css" id="style_color">
    <link rel="stylesheet" href="<?php echo $theme_url; ?>css/theme-skins/dark.css">

    <!-- CSS Customization -->
    <link rel="stylesheet" href="<?php echo $theme_url; ?>css/custom.css">

    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/seller/css/msg.css?v=1">

    <script type="text/javascript" src="<?php echo $theme_url; ?>plugins/jquery/jquery.min.js"></script>
</head>

<body>


<?php
include($view_file_path);
?>

<!-- JS Global Compulsory -->
<script type="text/javascript" src="<?php echo $theme_url; ?>plugins/jquery/jquery-migrate.min.js"></script>
<script type="text/javascript" src="<?php echo $theme_url; ?>plugins/bootstrap/js/bootstrap.min.js"></script>
<!-- JS Implementing Plugins -->
<script type="text/javascript" src="<?php echo $theme_url; ?>plugins/back-to-top.js"></script>
<script type="text/javascript" src="<?php echo $theme_url; ?>plugins/backstretch/jquery.backstretch.min.js"></script>
<!-- JS Customization -->
<script type="text/javascript" src="<?php echo $theme_url; ?>js/custom.js"></script>
<!-- JS Page Level -->
<script type="text/javascript" src="<?php echo $theme_url; ?>js/app.js"></script>
<script type="text/javascript" src="<?php echo $theme_url; ?>js/plugins/style-switcher.js"></script>
<script type="text/javascript" src="<?php echo $base_url; ?>assets/global/js/form-validation.js"></script>

<script type="text/javascript">
    jQuery(document).ready(function() {
        App.init();
        StyleSwitcher.initStyleSwitcher();
        setTimeout(function () {
            //alert("<?php echo($data['redirect_url']); ?>");
            window.location.href="<?php echo($data['redirect_url']); ?>";
        },<?php echo($data['msg_duration']); ?>);
    });
</script>
<script type="text/javascript">
    $.backstretch([
        "<?php echo $base_url; ?>assets/admin/img/success_msg_background.png"
    ])
</script>
<!--[if lt IE 9]>
<script src="<?php echo $theme_url; ?>plugins/respond.js"></script>
<script src="<?php echo $theme_url; ?>plugins/html5shiv.js"></script>
<script src="<?php echo $theme_url; ?>plugins/placeholder-IE-fixes.js"></script>
<![endif]-->

</body>
</html>

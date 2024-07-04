<html lang="en">
<head>
    <title><?php echo C('app_name')?> | <?php echo $data['page_title']; ?></title>
    <meta charset="utf-8" />
    <link rel="shortcut icon" href="<?php echo $base_url."uploads/".IMAGE_PATH."/".C('favicon_img'); ?>">

    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="keywords" content="Share any photo"/>
    <meta name="description" content="The world’s first user flow diagramming tool tailored for designers. Build and present beautiful user flow diagrams that tell a story." />
    <meta property="og:title" content="<?php echo C('app_name')?>" />
    <meta property="og:description" content="Share any photo" />
    <meta name="robots" content="all" />
    <meta property="og:image" content="<?php echo $base_url."uploads/".IMAGE_PATH."/".C('logo_img'); ?>"/>
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo $base_url; ?>" />
    <meta itemprop="url" content="<?php echo $base_url; ?>" />
    <meta property="fb:app_id" content="" />
    <meta name="theme-color" content="#232D41" />
    <meta name="twitter:card" content="summary_large_image" />

    <meta name="theme-color" content="#000000" />
    <link rel="manifest" href="<?php echo $theme_front_url; ?>dist/n/manifest.json" />
    <link href="<?php echo $theme_front_url; ?>dist/n/static/css/main.e2fa4622.chunk.css?v=1595425269306" rel="stylesheet" />
    <link href="<?php echo $base_url; ?>assets/home/css/login.css?v=<?php echo get_site_version(); ?>" rel="stylesheet" />

    <link href="<?php echo($theme_user_url); ?>assets/css/font-awesome.css" rel="stylesheet" />

    <link rel="stylesheet" href="<?php echo($base_url); ?>assets/global/plugins/jquery-dialog/jquery-confirm.css">

    <link href="<?php echo($base_url); ?>assets/global/css/base.css?v=<?php echo get_site_version(); ?>" rel="stylesheet" />
    <link href="<?php echo($base_url); ?>assets/global/css/global.css?v=<?php echo get_site_version(); ?>" rel="stylesheet" />
    <link href="<?php echo($base_url); ?>assets/home/css/custom.css?v=<?php echo get_site_version(); ?>" rel="stylesheet" />
    <link href="<?php echo($base_url); ?>assets/home/css/desktop.css?v=<?php echo get_site_version(); ?>" rel="stylesheet" />
    <link href="<?php echo($base_url); ?>assets/home/css/mobile.css?v=<?php echo get_site_version(); ?>" rel="stylesheet" />

    <script src="<?php echo($base_url); ?>assets/global/plugins/jquery1.12.4/jquery-1.12.4.min.js" type="text/javascript"></script>

    <link href="<?php echo($base_url); ?>assets/global/plugins/jquery-ui-1.12.1/jquery-ui.css" rel="stylesheet" />
    <script src="<?php echo($base_url); ?>assets/global/plugins/jquery-ui-1.12.1/jquery-ui.js" type="text/javascript"></script>

    <script src="<?php echo($base_url); ?>assets/global/plugins/jquery-dialog/jquery-confirm.js"></script>
    <script>
        var APP_NAME = "<?php echo(C("app_name"));?>";
        var base_url = "<?php echo $base_url; ?>";
    </script>
</head>
<body>
<div id="root">
    <div class="auth flex bind fluid-height">
        <div class="hidden@xs hidden@sm hidden@md">
            <div class="m6 absolute front">
                <a class="textbutton" href="<?php echo $base_url; ?>">
                    <img class="img-fluid footer-logo__image img-fluid" src="<?php echo $base_url; ?>assets/global/img/logo_home.png" style="width: 158px" alt="">
                </a>
                <div class="tagline"><h2 class="">Communicate your designs like never before.</h2></div>
            </div>
            <div class="authpanel"><img src="<?php echo $theme_front_url; ?>dist/n/static/media/illustration-auth.5abc21f6.svg" alt="" /></div>
        </div>
        <div class="bind flex flex-column ph4 fluid-height overlay-y">
            <div class="ml2 mt6 hidden@lg hidden@xl">
                <a class="textbutton" href="<?php echo $base_url; ?>">
                    <svg width="158" height="27.999999999999996" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 203 35">
                        <g fill="none" fill-rule="evenodd">
                            <path
                                fill="#161637"
                                d="M143.3 29.7h4.24c.13 0 .24-.1.24-.23V.28a.25.25 0 00-.26-.25h-4.2a.25.25 0 00-.25.25v29.19c0 .13.1.23.24.23M60.94 25.74a6.54 6.54 0 11.02-13.09 6.54 6.54 0 01-.02 13.1m0-17.76a11.23 11.23 0 00-11.21 11.22c0 6.18 5.03 11.21 11.21 11.21 6.19 0 11.22-5.03 11.22-11.21 0-6.19-5.03-11.22-11.22-11.22M161.52 25.74a6.54 6.54 0 11.02-13.09 6.54 6.54 0 01-.02 13.1m0-17.76a11.23 11.23 0 000 22.43c6.19 0 11.22-5.03 11.22-11.21 0-6.19-5.03-11.22-11.22-11.22M88.38 8.5l-5.92 14.7-5.92-14.7a.25.25 0 00-.23-.15h-4.63c-.17 0-.3.18-.23.35l8.67 21.2c.04.1.13.16.23.16h4.24c.1 0 .19-.06.23-.15L93.49 8.7a.25.25 0 00-.23-.35H88.6c-.1 0-.19.06-.23.16M122.1 9.58V8.55c0-.14-.1-.24-.24-.24h-4.24c-.13 0-.24.1-.24.24V29.8c0 .15.12.26.26.26h4.2c.15 0 .26-.11.26-.26V18.09c0-1.92.47-3.3 1.39-4.13.95-.85 2.2-1.29 3.67-1.29h.83c.14 0 .25-.11.25-.25V8.25a.25.25 0 00-.25-.26h-.83a7.9 7.9 0 00-5.06 1.6M132.91 2.03c-1.66 1.5-2.5 3.7-2.5 6.52v20.9c0 .14.11.25.25.25h4.3c.14 0 .25-.11.25-.25V12.86h5.44c.14 0 .25-.1.25-.24V8.49a.25.25 0 00-.25-.24h-5.43c.04-1.36.38-2.33 1.02-2.87.7-.6 1.89-.84 3.47-.7l.92.07c.15.01.27-.1.27-.25V.36c0-.13-.1-.23-.23-.24l-.79-.05c-2.95-.2-5.3.46-6.97 1.96M197.89 8.53l-3.97 13.42-4.22-13.43a.24.24 0 00-.23-.17h-3.8c-.1 0-.2.07-.23.17l-4.22 13.43-3.96-13.42a.24.24 0 00-.23-.18h-4.41a.24.24 0 00-.23.32l6.51 21.22c.03.1.12.17.23.17h4.03c.1 0 .2-.07.23-.17l4.18-13.1 4.18 13.1c.03.1.13.17.23.17h4.04c.1 0 .2-.07.23-.17l6.5-21.22a.24.24 0 00-.22-.32h-4.41c-.1 0-.2.07-.23.18M99.47 14.09a6.65 6.65 0 014.54-1.59c1.55 0 2.92.5 4.08 1.5a5.93 5.93 0 011.84 2.97H97.59c.36-1.13.99-2.1 1.88-2.88zm10.44 8.98a.26.26 0 00-.34.06 8.81 8.81 0 01-1.95 1.9c-.91.5-2.03.77-3.32.77-1.81 0-3.35-.5-4.57-1.48a5.66 5.66 0 01-1.86-2.76h16.56c.14 0 .25-.1.26-.23l.08-.74a11.59 11.59 0 00-2.86-9.2 9.99 9.99 0 00-7.84-3.4c-3.28 0-6.02 1.09-8.13 3.23a10.99 10.99 0 00-3.16 7.99c0 3.22 1.08 5.92 3.21 8.03 2.13 2.11 4.92 3.18 8.28 3.18 2.07 0 3.96-.45 5.6-1.33 1.41-.76 2.59-1.77 3.5-3.01l.32-.46a.25.25 0 00-.08-.36l-3.7-2.2z"
                                ></path>
                            <path fill="#FB5D2E" d="M39.23 15.1c0-.13-.1-.24-.24-.24h-4.45a14.84 14.84 0 11-29.68 0H.4c-.13 0-.23.1-.24.24v.01a19.56 19.56 0 0019.54 19.3c10.7 0 19.4-8.64 19.53-19.3v-.01"></path>
                            <path fill="#16B4F2" d="M19.7.03C11.5.03 4.86 6.67 4.86 14.86h29.67C34.53 6.67 27.9.03 19.7.03"></path>
                            <path fill="#2C2C96" d="M34.53 14.86a14.83 14.83 0 11-29.67 0h29.67z"></path>
                        </g>
                    </svg>
                </a>
            </div>

            <?php include($view_file_path); ?>
        </div>
    </div>
</div>

<script src="<?php echo($base_url); ?>assets/global/plugins/photoswipe/photoswipe.min.js"></script>
<script src="<?php echo($base_url); ?>assets/global/plugins/photoswipe/photoswipe-ui-default.min.js"></script>
<script src="<?php echo($base_url); ?>assets/global/plugins/jquery-confirm/dist/jquery-confirm.min.js"></script>

<script src="<?php echo($base_url); ?>assets/global/js/toastr.min.js?v=<?php echo get_site_version(); ?>"></script>
<script src="<?php echo($base_url); ?>assets/global/js/photoswipe.js?v=<?php echo get_site_version(); ?>"></script>

<script src="<?php echo($base_url); ?>assets/global/js/function.js?v=<?php echo get_site_version(); ?>"></script>
<script src="<?php echo($base_url); ?>assets/global/js/common.js?v=<?php echo get_site_version(); ?>"></script>
<iframe id="upload_frame" class="visibility-hidden" name="upload_frame" height="0" width="0" frameborder="0" scrolling="yes"></iframe>
<script>
    $('#upload_frame').on('load',function(){
        var res = $(this).contents().find('body').html();
        var obj = JSON.parse(res);
        if(obj.status == '0'){
            var callback = obj.redirect.callback;
            eval(callback +"('"+res+"');");
        }else {
            var callback = obj.data.callback;
            eval(callback +"('"+res+"');");
        }
    });
</script>
</body>
</html>

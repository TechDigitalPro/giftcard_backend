<!DOCTYPE html>
<html class="is-loading" lang="en">

<head>
    <title><?php echo C('app_name')?></title>
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
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <link rel="stylesheet" href="<?php echo $theme_front_url; ?>dist/prod/css/public-site-v2.min.css?v=<?php echo get_site_version(); ?>">

    <link rel="stylesheet" type="text/css" href="<?php echo $theme_front_url; ?>js/bootstrap/bootstrap.min.css">

    <!-- Google fonts  -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Yesteryear" rel="stylesheet">

    <!-- Template's stylesheets -->
    <link rel="stylesheet" href="<?php echo $theme_front_url; ?>js/megamenu/stylesheets/screen.css">
    <link rel="stylesheet" href="<?php echo $theme_front_url; ?>css/theme-default.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $theme_front_url; ?>js/loaders/stylesheets/screen.css">
    <link rel="stylesheet" href="<?php echo $theme_front_url; ?>css/corporate.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $theme_front_url; ?>fonts/font-awesome/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?php echo $theme_front_url; ?>fonts/Simple-Line-Icons-Webfont/simple-line-icons.css" media="screen" />
    <link rel="stylesheet" href="<?php echo $theme_front_url; ?>fonts/et-line-font/et-line-font.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $theme_front_url; ?>js/revolution-slider/css/settings.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $theme_front_url; ?>js/revolution-slider/css/layers.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $theme_front_url; ?>js/revolution-slider/css/navigation.css">
    <link rel="stylesheet" href="<?php echo $theme_front_url; ?>js/parallax/main.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $theme_front_url; ?>js/cubeportfolio/cubeportfolio.min.css">
    <link href="<?php echo $theme_front_url; ?>js/owl-carousel/owl.carousel.css" rel="stylesheet">
    <link href="<?php echo $theme_front_url; ?>js/owl-carousel/owl.theme.css" rel="stylesheet">
    <link href="<?php echo $theme_front_url; ?>js/tabs/css/responsive-tabs.css" rel="stylesheet" type="text/css" media="all" />
    <link rel="stylesheet" href="<?php echo $theme_front_url; ?>js/ytplayer/ytplayer.css" />
    <link href="<?php echo $theme_front_url; ?>js/accordion/css/smk-accordion.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $theme_front_url; ?>js/parallax/main.css">
    <link rel="stylesheet" href="<?php echo $theme_front_url; ?>fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css">
    <link rel="stylesheet" href="<?php echo $theme_front_url; ?>js/ytplayer/ytplayer.css" />
    <link rel="stylesheet" href="<?php echo $theme_front_url; ?>js/magnific-popup/magnific-popup.css">
    <script>
        var APP_NAME = "<?php echo(C("app_name"));?>";
        var base_url = "<?php echo $base_url; ?>";
    </script>
</head>

<body>
    <div class="over-loader loader-live">
        <div class="loader">
            <div class="loader-item style5">
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
            </div>

        </div>
    </div>
    <div class="wrapper-boxed">
        <?php include($view_file_path); ?>
    <div>

        <script src="<?php echo $theme_front_url; ?>js/jquery/jquery.js"></script>
        <script src="<?php echo $theme_front_url; ?>js/bootstrap/bootstrap.min.js"></script>

        <script src="<?php echo $theme_front_url; ?>js/less/less.min.js" data-env="development"></script>

        <!-- Scripts END -->

        <!-- Template scripts -->
        <script src="<?php echo $theme_front_url; ?>js/megamenu/js/main.js"></script>
        <script type="text/javascript" src="<?php echo $theme_front_url; ?>js/ytplayer/jquery.mb.YTPlayer.js"></script>
        <script type="text/javascript" src="<?php echo $theme_front_url; ?>js/ytplayer/elementvideo-custom.js"></script>
        <script type="text/javascript" src="<?php echo $theme_front_url; ?>js/ytplayer/play-pause-btn.js"></script>
        <!-- REVOLUTION JS FILES -->
        <script type="text/javascript" src="<?php echo $theme_front_url; ?>js/revolution-slider/js/jquery.themepunch.tools.min.js"></script>
        <script type="text/javascript" src="<?php echo $theme_front_url; ?>js/revolution-slider/js/jquery.themepunch.revolution.min.js"></script>
        <!-- SLIDER REVOLUTION 5.0 EXTENSIONS
        (Load Extensions only on Local File Systems !
        The following part can be removed on Server for On Demand Loading) -->
        <script type="text/javascript" src="<?php echo $theme_front_url; ?>js/revolution-slider/js/extensions/revolution.extension.actions.min.js"></script>
        <script type="text/javascript" src="<?php echo $theme_front_url; ?>js/revolution-slider/js/extensions/revolution.extension.carousel.min.js"></script>
        <script type="text/javascript" src="<?php echo $theme_front_url; ?>js/revolution-slider/js/extensions/revolution.extension.kenburn.min.js"></script>
        <script type="text/javascript" src="<?php echo $theme_front_url; ?>js/revolution-slider/js/extensions/revolution.extension.layeranimation.min.js"></script>
        <script type="text/javascript" src="<?php echo $theme_front_url; ?>js/revolution-slider/js/extensions/revolution.extension.migration.min.js"></script>
        <script type="text/javascript" src="<?php echo $theme_front_url; ?>js/revolution-slider/js/extensions/revolution.extension.navigation.min.js"></script>
        <script type="text/javascript" src="<?php echo $theme_front_url; ?>js/revolution-slider/js/extensions/revolution.extension.parallax.min.js"></script>
        <script type="text/javascript" src="<?php echo $theme_front_url; ?>js/revolution-slider/js/extensions/revolution.extension.slideanims.min.js"></script>
        <script type="text/javascript" src="j<?php echo $theme_front_url; ?>s/revolution-slider/js/extensions/revolution.extension.video.min.js"></script>
        <script type="text/javascript">
            var tpj=jQuery;
            var revapi4;
            tpj(document).ready(function() {
                if(tpj("#rev_slider").revolution == undefined){
                    revslider_showDoubleJqueryError("#rev_slider");
                }else{
                    revapi4 = tpj("#rev_slider").show().revolution({
                        sliderType:"standard",
                        jsFileLocation:"js/revolution-slider/js/",
                        sliderLayout:"auto",
                        dottedOverlay:"none",
                        delay:9000,
                        navigation: {
                            keyboardNavigation:"off",
                            keyboard_direction: "horizontal",
                            mouseScrollNavigation:"off",
                            onHoverStop:"off",
                            arrows: {
                                style:"uranus",
                                enable:true,
                                hide_onmobile:false,
                                hide_under:100,
                                hide_onleave:true,
                                hide_delay:200,
                                hide_delay_mobile:1200,
                                tmp:'',
                                left: {
                                    h_align:"left",
                                    v_align:"center",
                                    h_offset:80,
                                    v_offset:0
                                },
                                right: {
                                    h_align:"right",
                                    v_align:"center",
                                    h_offset:80,
                                    v_offset:0
                                }
                            }
                            ,
                            touch:{
                                touchenabled:"on",
                                swipe_threshold: 75,
                                swipe_min_touches: 1,
                                swipe_direction: "horizontal",
                                drag_block_vertical: false
                            }
                            ,



                        },
                        viewPort: {
                            enable:true,
                            outof:"pause",
                            visible_area:"80%"
                        },

                        responsiveLevels:[1240,1024,778,480],
                        gridwidth:[1240,1024,778,480],
                        gridheight:[870,750,600,420],
                        lazyType:"smart",
                        parallax: {
                            type:"mouse",
                            origo:"slidercenter",
                            speed:2000,
                            levels:[2,3,4,5,6,7,12,16,10,50],
                        },
                        shadow:0,
                        spinner:"off",
                        stopLoop:"off",
                        stopAfterLoops:-1,
                        stopAtSlide:-1,
                        shuffle:"off",
                        autoHeight:"off",
                        hideThumbsOnMobile:"off",
                        hideSliderAtLimit:0,
                        hideCaptionAtLimit:0,
                        hideAllCaptionAtLilmit:0,
                        disableProgressBar:"on",
                        debugMode:false,
                        fallbacks: {
                            simplifyAll:"off",
                            nextSlideOnWindowFocus:"off",
                            disableFocusListener:false,
                        }
                    });
                }
            });	/*ready*/
        </script>
        <script type="text/javascript">
            var tpj = jQuery;

            var revapi280;
            tpj(document).ready(function() {
                if (tpj("#rev_slider_280_1").revolution == undefined) {
                    revslider_showDoubleJqueryError("#rev_slider_280_1");
                } else {
                    revapi280 = tpj("#rev_slider_280_1").show().revolution({
                        sliderType: "hero",
                        jsFileLocation: "../../revolution/js/",
                        sliderLayout: "auto",
                        dottedOverlay: "none",
                        delay: 9000,
                        navigation: {},
                        responsiveLevels: [1240, 1024, 778, 480],
                        visibilityLevels: [1240, 1024, 778, 480],
                        gridwidth: [1000, 1024, 778, 480],
                        gridheight: [700, 520, 420, 420],
                        lazyType: "none",
                        parallax: {
                            type: "scroll",
                            origo: "slidercenter",
                            speed: 1000,
                            levels: [5, 10, 15, 20, 25, 30, 35, 40, 45, 46, 47, 48, 49, 50, 51, 55],
                            type: "scroll",
                        },
                        shadow: 0,
                        spinner: "spinner2",
                        autoHeight: "off",
                        fullScreenAutoWidth: "off",
                        fullScreenAlignForce: "off",
                        fullScreenOffsetContainer: "",
                        fullScreenOffset: "60",
                        disableProgressBar: "on",
                        hideThumbsOnMobile: "off",
                        hideSliderAtLimit: 0,
                        hideCaptionAtLimit: 0,
                        hideAllCaptionAtLilmit: 0,
                        debugMode: false,
                        fallbacks: {
                            simplifyAll: "off",
                            disableFocusListener: false,
                        }
                    });
                }
            }); /*ready*/
        </script>
        <script>
            $(window).load(function(){
                setTimeout(function(){

                    $('.loader-live').fadeOut();
                },1000);
            })

        </script>
        <script src="<?php echo $theme_front_url; ?>js/parallax/parallax-background.min.js"></script>
        <script>
            (function ($) {
                $('.parallax').parallaxBackground();
            })(jQuery);
        </script>
        <script src="<?php echo $theme_front_url; ?>js/magnific-popup/jquery.magnific-popup.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {

                $('.image-popup-vertical-fit').magnificPopup({
                    type: 'image',
                    closeOnContentClick: true,
                    mainClass: 'mfp-img-mobile',
                    image: {
                        verticalFit: true
                    }

                });

                $('.image-popup-fit-width').magnificPopup({
                    type: 'image',
                    closeOnContentClick: true,
                    image: {
                        verticalFit: false
                    }
                });

                $('.image-popup-no-margins').magnificPopup({
                    type: 'image',
                    closeOnContentClick: true,
                    closeBtnInside: false,
                    fixedContentPos: true,
                    mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
                    image: {
                        verticalFit: true
                    },
                    zoom: {
                        enabled: true,
                        duration: 300 // don't foget to change the duration also in CSS
                    }
                });

            });
        </script>
        <script>
            $(document).ready(function() {
                $('.popup-gallery').magnificPopup({
                    delegate: 'a',
                    type: 'image',
                    tLoading: 'Loading image #%curr%...',
                    mainClass: 'mfp-img-mobile',
                    gallery: {
                        enabled: true,
                        navigateByImgClick: true,
                        preload: [0,1] // Will preload 0 - before current, and 1 after the current image
                    },
                    image: {
                        tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
                        titleSrc: function(item) {
                            return item.el.attr('title') + '<small>by Marsel Van Oosten</small>';
                        }
                    }
                });
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
                    disableOn: 700,
                    type: 'iframe',
                    mainClass: 'mfp-fade',
                    removalDelay: 160,
                    preloader: false,
                    fixedContentPos: false
                });
            });
        </script>

        <script>
            $(document).ready(function() {

                $('.simple-ajax-popup-align-top').magnificPopup({
                    type: 'ajax',
                    alignTop: true,
                    overflowY: 'scroll' // as we know that popup content is tall we set scroll overflow by default to avoid jump
                });

                $('.simple-ajax-popup').magnificPopup({
                    type: 'ajax'
                });

            });
        </script>
        <script src="<?php echo $theme_front_url; ?>js/tabs/js/responsive-tabs.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="<?php echo $theme_front_url; ?>js/cubeportfolio/jquery.cubeportfolio.min.js"></script>
        <script type="text/javascript" src="<?php echo $theme_front_url; ?>js/cubeportfolio/mosaic-flat.js"></script>
        <script src="<?php echo $theme_front_url; ?>js/owl-carousel/owl.carousel.js"></script>
        <script src="<?php echo $theme_front_url; ?>js/owl-carousel/custom.js"></script>
        <script src="<?php echo $theme_front_url; ?>js/owl-carousel/owl.carousel.js"></script>
        <script src="<?php echo $theme_front_url; ?>js/accordion/js/smk-accordion.js"></script>
        <script src="<?php echo $theme_front_url; ?>js/accordion/js/custom.js"></script>
        <script src="<?php echo $theme_front_url; ?>js/progress-circle/raphael-min.js"></script>
        <script src="<?php echo $theme_front_url; ?>js/progress-circle/custom.js"></script>
        <script src="<?php echo $theme_front_url; ?>js/progress-circle/jQuery.circleProgressBar.js"></script>
        <script src="<?php echo $theme_front_url; ?>js/functions/functions.js"></script>




</body>

</html>
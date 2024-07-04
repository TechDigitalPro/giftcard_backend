<style>
    .contacts-item{
        padding: 15px 0;
    }
</style>
<section class="section section-contacts1 -pd_bot_0" id="section-contacts">
    <div class="section-overlay-title -tp-20 rellax" data-rellax-speed="1" data-rellax-percentage="0.5">
        <span>Contact us</span>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-9 col-md-offset-2">
                <div class="section-title">
                    <h2><?php echo lang('Contact Us')?></h2>
                </div>
                <p class="section-descr hidden">

                </p>
                <div class="contacts1">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="contacts-item">
                                <h4 class="contacts-subtitle"><?php echo lang('About')?></h4>
                                <div class="contacts-list">
                                    <p>
                                        <?php echo lang('kerart_about_us')?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="contacts-item">
                                <h4 class="contacts-subtitle"><?php echo lang('Address')?></h4>
                                <ul class="contacts-list">
                                    <li>
                                        <i class="fa fa-home" aria-hidden="true"></i>
                                        <span class="social-descr"><?php echo lang('Shenyang, Liaoning')?>, <?php echo lang('China')?></span>
                                    </li>
                                    <li>
                                        <a href="tel:+971559170107">
                                            <i class="fa fa-phone" aria-hidden="true"></i>
                                            <span class="social-descr">tel. +971559170107, +8613066745550</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="mailto:info@kerart.org">
                                            <i class="fa fa-envelope" aria-hidden="true"></i>
                                            <span class="social-descr">info@kerart.org</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="contacts-item">
                                <h4 class="contacts-subtitle"><?php echo lang('Follow Me')?></h4>
                                <ul class="social-list -black -md">
                                    <li>
                                        <a href="javascript:void(0);">
                                            <i class="fa fa-facebook" aria-hidden="true"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">
                                            <i class="fa fa-twitter" aria-hidden="true"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">
                                            <i class="fa fa-vk" aria-hidden="true"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">
                                            <i class="fa fa-pinterest-p" aria-hidden="true"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="contacts-map">
                        <div id="google-map" class="google-map"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<footer class="footer">
    <div class="footer-copyright text-center">
        www.kerart.cn <?php echo date("Y", TIMESTAMP);?>
    </div>
</footer>
<?php
//print_r($data); die;
$info=isset($data['user'])?$data['user']:array();
$image_url=$base_url."assets/global/img/default-avatar.png";
if(isset($info['user_image']) && !empty($info['user_image']) && file_exists(FCPATH.DS.UPLOADS_PATH.DS.PROFILE_IMAGE_PATH.DS.$info['user_image'])){
    $image_url=$base_url."uploads/".PROFILE_IMAGE_PATH."/".$info['user_image'];
}
?>
<style>
    footer, #section-contacts{
        display: none;
    }
</style>

<section class="section-title_top" style="background-image: linear-gradient(to top , rgba(0,0,0, 0.5) 0%, rgba(0,0,0,0.5) 100%) , url(<?php echo $data['banner']; ?>); ">
    <div class="container-fluid">
        <div class="menu-title_block ">
            <h2 class="hidden"><?php echo $data['page_title'];?></h2>
            <p class="hidden">At vero eos et accusamus et iusto odio dignissimos ducmus qui blanditiis praesentium voluptatum deleolor</p>
        </div>
    </div>
</section>
<section class="section-shop -cart">
    <div class="container-fluid padding-0">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="container-fluid">
                    <div class="thumbnail-4">
                        <div class="media-wrapper"><img src="<?php echo $image_url;?>" alt=""/></div>
                        <div class="checkout-form text-center">
                            <div class="">
                                <div class="">
                                    <h2 class="p-desc"><?php echo(isset($info['user_name'])?$info['user_name']:"");?></h2>
                                </div>
                                <div class="">
                                    <p class="p-desc"><?php echo(isset($info['user_about'])?$info['user_about']:"");?></p>
                                </div>
                                <div class="">
                                    <p class="p-desc">
                                        <a class="hover-text" href="tel:<?php echo(isset($info['user_phone'])?$info['user_phone']:"");?>"><?php echo(isset($info['user_phone'])?$info['user_phone']:"");?></a>
                                    </p>
                                </div>
                                <div class="">
                                    <p class="p-desc">
                                        <a class="hover-text" href="mailto:<?php echo(isset($info['user_email'])?$info['user_email']:"");?>"><?php echo(isset($info['user_email'])?$info['user_email']:"");?></a>
                                    </p>
                                </div>

                                <div class="">
                                    <div class="form-group text-center">
                                        <a class="btn btn-primary btn-md btn-submit" href="<?php echo base_url('user/profile/edit')?>"><span><?php echo lang('Edit Profile')?></span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function () {
        if($(window).scrollTop() >= 50){
            $('.rd-navbar').addClass('move_down');
        }
        $(window).on('scroll', function() {
            if ($(window).scrollTop() >= 50) {
                $('.rd-navbar').addClass('move_down');
            } else {
                $('.rd-navbar').removeClass('move_down');
            }
        });
    });
</script>
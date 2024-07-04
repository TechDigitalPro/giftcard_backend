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
            <div class="col-md-6 col-md-offset-3">
                <div class="container-fluid">
                    <form class="text-left profile-form" action="<?php echo base_url('user/profile/edit').'?callback=kt_profile_submitted'; ?>" method="post" enctype="multipart/form-data" target="upload_frame">
                        <input type="hidden" name="action" value="submit">
                        <input type="hidden" name="user_id" value="<?php echo(isset($info['id'])?$info['id']:"");?>">
                        <div class="thumbnail-4">
                            <div class="media-wrapper">
                                <a href="javascript:void(0);" id="avatar-btn">
                                    <img class="avatar-preview" src="<?php echo $image_url;?>" alt=""/>
                                </a>
                            </div>
                            <div class="checkout-form">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group form-wrap-validation">
                                            <label for="forms-name"><?php echo lang('First name')?></label>
                                            <input class="form-control" id="forms-name" type="text" name="first_name" data-field-property="@Required" value="<?php echo(isset($info['user_first_name'])?$info['user_first_name']:"");?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-wrap-validation">
                                            <label for="forms-last-name"><?php echo lang('Last name')?></label>
                                            <input class="form-control" id="forms-last-name" type="text" name="last_name" data-field-property="@Required" value="<?php echo(isset($info['user_last_name'])?$info['user_last_name']:"");?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group form-wrap-validation">
                                            <label for="forms-email"><?php echo lang('E-mail')?></label>
                                            <input class="form-control" id="forms-email" type="email" name="email" data-field-property="@Email @Required" value="<?php echo(isset($info['user_email'])?$info['user_email']:"");?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-wrap-validation">
                                            <label for="forms-phone"><?php echo lang('Phone')?></label>
                                            <input class="form-control" id="forms-phone" type="text" name="phone" data-field-property="@Numeric @Required" value="<?php echo(isset($info['user_phone'])?$info['user_phone']:"");?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12 padding-left-20 padding-right-20">
                                        <div class="form-group form-wrap-validation">
                                            <label for="forms-about-us"><?php echo lang('About me')?></label>
                                            <textarea class="form-control" id="forms-about-us" name="about"><?php echo(isset($info['user_about'])?$info['user_about']:"");?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-wrap-validation">
                                            <label for="forms-password"><?php echo lang('Password')?></label>
                                            <input class="form-control" id="forms-password" type="password" name="password">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-wrap-validation">
                                            <label for="forms-password-confirm"><?php echo lang('Confirm Password')?></label>
                                            <input class="form-control" id="forms-password-confirm" type="password" name="password-confirm">
                                        </div>
                                    </div>
                                    <div class="visibility-hidden">
                                        <input type="file" name="upload_file">
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group text-center">
                                            <a href="javascript:void(0);" class="btn btn-primary btn-md  btn-submit"><?php echo lang('Update')?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
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

<!--for ajax callback-->
<script class="kt-ajax-callback-func">
    function kt_profile_submitted(res){
        var obj = JSON.parse(res);
        console.log(obj);
        if(obj.status == '1'){
            showToast(obj.message, 'success');
            showLoading(false);

        }else{
            showLoading(false);
            showToast(obj.message, 'error');
        }
    }
</script>
<!--for ajax callback-->
<script>
    $(document).ready(function(){
        $("body").on("click", ".btn-submit", function(){
            var form = $(this).closest('form');
            var form_is_valid = checkFromValidation(form);
            console.log(form_is_valid);
            if(form_is_valid){
                if(form.find("#forms-password").val() != form.find("#forms-password-confirm").val()){
                    showToast(lang('Invalid confirm password'), 'error');
                    return false;
                }
                showLoading(true);
                form.submit();
            }
            //$(this).closest("form").submit();
        });
        $("body").on("click","#avatar-btn",function(){
            $(".profile-form [name='upload_file']").trigger('click');
        });
        $("body").on("change", "[name='upload_file']",function(){
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $(".avatar-preview").attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
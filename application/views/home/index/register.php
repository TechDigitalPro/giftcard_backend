<style>
    footer, #section-contacts{
        display: none;
    }
</style>

<div class="has-top-fixed-menu"></div>
<section class="section">
    <div class="container-fluid">
        <div class="title text-center">
            <h2><?php echo $data['page_title'];?></h2>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <form class="text-left" action="<?php echo base_url('register').'?callback=kt_register_submitted'; ?>" method="post" enctype="multipart/form-data" target="upload_frame">
                    <input type="hidden" name="action" value="submit">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3">
                            <h5 class="checkout-title"> </h5>
                            <div class="checkout-form">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group form-wrap-validation">
                                            <label for="forms-name"><?php echo lang('First name')?></label>
                                            <input class="form-control" id="forms-name" type="text" name="first_name" data-field-property="@Required">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-wrap-validation">
                                            <label for="forms-last-name"><?php echo lang('Last name')?></label>
                                            <input class="form-control" id="forms-last-name" type="text" name="last_name" data-field-property="@Required">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group form-wrap-validation">
                                            <label for="forms-email"><?php echo lang('E-mail')?></label>
                                            <input class="form-control" id="forms-email" type="email" name="email" data-field-property="@Email @Required">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-wrap-validation">
                                            <label for="forms-phone"><?php echo lang('Phone')?></label>
                                            <input class="form-control" id="forms-phone" type="text" name="phone" data-field-property="@Numeric @Required">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-wrap-validation">
                                            <label for="forms-password"><?php echo lang('Password')?></label>
                                            <input class="form-control" id="forms-password" type="password" name="password" data-field-property="@Required">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-wrap-validation">
                                            <label for="forms-password-confirm"><?php echo lang('Confirm Password')?></label>
                                            <input class="form-control" id="forms-password-confirm" type="password" name="password-confirm" data-field-property="@Required">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group text-center">
                                            <a href="javascript:void(0);" class="btn btn-primary btn-md  btn-submit"><?php echo lang('Register')?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-10 col-md-offset-1 text-center">
                <div class="group-button-lg text-center">
                    <span><?php echo lang('Already have an account')?>? &nbsp;</span><a class="" href="<?php echo base_url('login'); ?>"><?php echo lang('Log in')?></a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function () {
        $('header.header').addClass('-active');
    });
</script>

<!--for ajax callback-->
<script class="kt-ajax-callback-func">
    function kt_register_submitted(res){
        var obj = JSON.parse(res);
        console.log(obj);
        if(obj.status == '1'){
            showToast(obj.message, 'success');
            setTimeout(function(){
                window.location.href = "<?php echo base_url('login')?>";
            }, 500);
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
                    showToast('Invalid confirm password', 'error');
                    return false;
                }
                showLoading(true);
                form.submit();
            }
            //$(this).closest("form").submit();
        });
    });
</script>
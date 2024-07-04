<div class="self-center max-width-408 pv12 mtauto mbauto fluid">
    <div class="text-center mb10"><h1 class="">Sign in</h1></div>
    <form class="form" action="<?php echo base_url('login').'?callback=kt_login_submitted'; ?>" method="post" enctype="multipart/form-data" target="upload_frame">
        <input type="hidden" name="action" value="submit">
        <div class="input">
            <label class="above"><span class="descriptor">Email</span><input name="email" type="email" placeholder="alex@example.com" value="" /></label>
        </div>
        <div class="relative flex mv10">
            <div class="input">
                <label class="above"><span class="descriptor">Password</span><input name="password" type="password" placeholder="" autocomplete="current-password" value="" /></label>
            </div>
            <div class="absolute top right"><a class="textbutton secondary" href="<?php echo base_url('forgot_password'); ?>">Forgot password?</a></div>
        </div>
<!--        <div class="group buttons"><button class="button" type="submit">Sign in</button></div>-->
        <div class="group buttons">
            <a href="javascript:void(0);" class="button btn btn-primary btn-md  btn-submit"><?php echo lang('Log in')?></a>
        </div>
    </form>
    <div class="mt4 flex justify-center">
        <small class="dark60@text">Don't have an account? <a class="textbutton" href="https://play.google.com/store/apps/" target="_blank" rel="noopener noreferrer">Download the app</a> to get started.</small>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('header.header').addClass('-active');
    });
</script>



<!--for ajax callback-->
<script class="kt-ajax-callback-func">
    function kt_login_submitted(res){
        var obj = JSON.parse(res);
        var return_url = "";
        <?php
        if(isset($_GET['return_url']) && !empty($_GET['return_url'])){
        ?>
        return_url = "<?php echo $_GET['return_url']; ?>";
        <?php
        }
        ?>
        if(obj.status == '1'){
            showToast(obj.message, 'success');
            setTimeout(function(){
                if(return_url!=""){
                    window.location.href = return_url;
                }else{
                    window.location.href = "<?php echo base_url('/')?>";
                }
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
            if(form_is_valid){
                showLoading(true);
                form.submit();
            }
            //$(this).closest("form").submit();
        });
    });
</script>
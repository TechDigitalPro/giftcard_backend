<?php
$return_url=@$data['return_url'];
?>
<div class="full-page login-page" filter-color="black" data-image="<?php echo($base_url); ?>assets/global/img/Univs-100kb.jpg">
    <!--   you can change the color of the filter page using: data-color="blue | purple | green | orange | red | rose " -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-6 col-md-offset-4 col-sm-offset-3">
                    <form method="post" action="" id="login-form">
                        <div class="card card-login card-hidden">
                            <div class="card-avatar" style="margin: 20px auto 5px; max-width: 75px;">
                                <a href="javascript:void(0)" style="font-size: 26px;">
                                    <img class="hidden avatar" src="<?php echo($base_url); ?>assets/global/img/corona-logo-1.png" alt="logo">
                                    <img class="avatar" src="<?php echo $base_url."uploads/".IMAGE_PATH."/".C('logo_img'); ?>" alt="logo">
                                </a>
                            </div>
                            <div class="card-content" style="padding: 15px">
                                <?php
                                $class = ($data['status']=='1'?'text-success':'text-danger');
                                ?>
                                <div class="text-center">
                                    <h3 class="<?php echo $class; ?>" style="font-size: 24px"><?php echo $data['message']; ?></h3>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        setTimeout(function() {
            // after 1000 ms we add the class animated to the login/register card
            $('.card').removeClass('card-hidden');
        }, 400)
    });

</script>

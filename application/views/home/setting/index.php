<?php
//print_r($data); die;
$info=isset($data['setting_info'])?$data['setting_info']:array();

?>

<link rel="stylesheet" href="<?php echo($base_url); ?>assets/bower_components/bootstrap-jasny/dist/css/jasny-bootstrap.min.css">

<div id="container">
    <!-- start: FORM VALIDATION EXAMPLE 1 -->
    <div class="container-fluid container-fullw">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <form action="" role="form" id="form" method="post" enctype="multipart/form-data" class="form-horizontal">
                        <input type="hidden" name="action" value="submit">
                        <div class="card-header card-header-text" data-background-color="rose">
                            <h4 class="card-title"> <i class="material-icons">settings_applications</i>  General Setting</h4>
                        </div>
                        <div class="card-content">
                            <div class="row">
                                <label class="col-sm-2 label-on-left">App Name</label>
                                <div class="col-sm-10">
                                    <div class="form-group label-floating is-empty">
                                        <label class="control-label"></label>
                                        <input type="text" placeholder="Input website name" class="form-control" id="app_name" name="app_name" value="<?php echo(C("app_name"));?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="col-sm-4 label-on-left">Logo</label>
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <div class="fileinput-box">
                                                <div class="thumbnail margin-0 inline-block" style="vertical-align: middle">
                                                    <?php
                                                    $image_url=$base_url."assets/global/img/placeholder.png";
                                                    if(C('logo_img') != "" && file_exists(FCPATH.DS.UPLOADS_PATH.DS.IMAGE_PATH.DS.C('logo_img'))){
                                                        $image_url = $base_url."uploads/".IMAGE_PATH."/".C('logo_img');
                                                    }
                                                    ?>
                                                    <img src="<?php echo $image_url; ?>" style="height: 100px;width: auto" alt="">
                                                </div>
                                                <div class="file-input-buttons" style="display: inline-block; margin-left: 30px;">
                                                    <button type="button" class="btn btn-round btn-success fileinput-btn"><i class="fa fa-picture"></i> Select image</button>
                                                    <input type="file" class="file-handler visibility-hidden" name="upload_logo_file">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-4 label-on-left">Favicon</label>
                                        <div class="col-sm-8">
                                            <div class="form-group">
                                                <div class="fileinput-box">
                                                    <div class="thumbnail margin-0 inline-block" style="vertical-align: middle">
                                                        <?php
                                                        $image_url=$base_url."assets/global/img/placeholder.png";
                                                        if(C('favicon_img') != "" && file_exists(FCPATH.DS.UPLOADS_PATH.DS.IMAGE_PATH.DS.C('favicon_img'))){
                                                            $image_url = $base_url."uploads/".IMAGE_PATH."/".C('favicon_img');
                                                        }
                                                        ?>
                                                        <img src="<?php echo $image_url; ?>" style="height: 40px;width: auto" alt="">
                                                    </div>
                                                    <div class="file-input-buttons" style="display: inline-block; margin-left: 30px;">
                                                        <button type="button" class="btn btn-round btn-success fileinput-btn"><i class="fa fa-picture"></i> Select image</button>
                                                        <input type="file" class="file-handler visibility-hidden" name="upload_fav_file">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="button-group" style="text-align: center;float: right;">
                                            <button type="submit" class="btn btn-md btn-primary" style="margin-left: 15px;">Submit</button>
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
    <!-- end: FORM VALIDATION EXAMPLE 1 -->
</div>
<script>
    $("body").on("click",".btn-add-attribute",function(){
        //show_alert("aaaaa");
        var html = $(".add-attribute-item-content").html();
        $(".attribute-box").append(html);
    });
    $("body").on("click", ".btn-del-item", function(){
        $(this).closest(".row").remove();
    });
    $("body").on("click", ".fileinput-btn",function(){
        $(this).closest(".fileinput-box").find(".file-handler").trigger("click");
    });
    $("body").on("change", ".file-handler",function(){
        var preview_node = $(this).closest(".fileinput-box").find(".thumbnail img");
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview_node.attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
</script>

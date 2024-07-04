
<div class="visibility-hidden">
    <div class="upload-post-content">
        <div class="dialog-content" style="margin-top: 0px">
            <div class="block" style="padding: 0 20px">
                <!-- RD Mailform-->
                <form class="text-left add-post-form" action="<?php echo base_url('user/post/add').'?callback=kt_post_submitted'; ?>" method="post" enctype="multipart/form-data" target="upload_frame">
                    <input type="hidden" name="action" value="submit">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-wrap form-wrap-validation">
                                <input class="form-control forms-title" type="text" name="title" data-field-property="@Required" placeholder="<?php echo lang('Title'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-wrap form-wrap-validation">
                                <select class="form-control forms-category_id" name="category_id">
                                    <option value="" disabled selected><?php echo lang('Select category')?></option>
                                    <?php
                                    $post_type_list = array(
                                        array(
                                            'id'=>'1',
                                            'name'=>lang('Art')
                                        ),
                                        array(
                                            'id'=>'2',
                                            'name'=>lang('Cooking')
                                        ),
                                        array(
                                            'id'=>'3',
                                            'name'=>lang('Culture')
                                        ),
                                        array(
                                            'id'=>'4',
                                            'name'=>lang('Philosophy')
                                        )
                                    );
                                    foreach($post_type_list as $post_type){
                                        ?>
                                        <option value="<?php echo $post_type['id']?>"><?php echo $post_type['name']?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-wrap form-wrap-validation">
                                <select class="form-control forms-type" name="type">
                                    <!--<option value="" disabled selected>Select media type</option>-->
                                    <?php
                                    $post_type_list = array(
                                        array(
                                            'id'=>'picture',
                                            'name'=>lang('Picture')
                                        ),
                                        array(
                                            'id'=>'video',
                                            'name'=>lang('Video')
                                        )
                                    );
                                    foreach($post_type_list as $post_type){
                                        ?>
                                        <option value="<?php echo $post_type['id']?>"><?php echo $post_type['name']?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row media-block picture-block">
                        <div class="col-sm-12 text-center">
                            <div class="file-input-box">
                                <div class="form-wrap form-wrap-validation">
                                    <img src="<?php echo get_placeholder('normal'); ?>" class="picture-preview">
                                </div>
                                <div class="form-wrap form-wrap-validation" style="margin: 15px 0">
                                    <input type="file" name="picture" class="hidden-file-obj visibility-hidden" accept="image/*">
                                    <button class="button btn btn-primary button-primary-outline btn-choose-file" type="button"><span><?php echo lang('Choose Picture')?></span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row media-block video-block hidden">
                        <div class="col-sm-6 text-center">
                            <div class="file-input-box" data-media="video">
                                <video width="210" height="157" controls="controls" preload="metadata" poster="<?php echo get_placeholder('video'); ?>">
                                    <source src="" type="video/mp4">
                                    <source src="" type="video/ogg">
                                    <source src="" type="video/webm">
                                    <object data="" width="470" height="255">
                                        <embed src="" width="470" height="255">
                                    </object>
                                </video>
                                <div class="form-wrap form-wrap-validation" style="margin: 15px 0">
                                    <input type="file" name="video" class="hidden-file-obj visibility-hidden" accept="video/*">
                                    <button class="button btn btn-primary button-primary-outline btn-choose-file" type="button"><span><?php echo lang('Choose Video')?></span></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 text-center">
                            <div class="file-input-box">
                                <div class="form-wrap form-wrap-validation">
                                    <img src="<?php echo get_placeholder('normal'); ?>" class="picture-preview">
                                </div>
                                <div class="form-wrap form-wrap-validation" style="margin: 15px 0">
                                    <input type="file" name="thumbnail" class="hidden-file-obj visibility-hidden" accept="image/*">
                                    <button class="button btn btn-primary btn-primary-outline btn-choose-file" type="button"><span><?php echo lang('Choose Thumbnail')?></span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-wrap form-wrap-validation">
                                <textarea class="form-control forms-description" rows="3" style="height: auto" name="description" data-field-property="" placeholder="<?php echo lang('Description')?>"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="group-button-lg text-center" style="margin-top: 15px">
                                <button class="button btn btn-primary button-primary-outline btn-post-submit" type="button"><span><?php echo lang('Upload Now')?></span></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="video-player-dlg-content">
        <div class="dialog-content" style="margin-top: -30px">
            <div class="video-viewer">
                <video controls="controls" preload="metadata" autoplay>
                    <source src="" type="video/mp4">
                </video>
            </div>
        </div>
    </div>
</div>

<script class="kt-ajax-callback-func-post">
    function kt_post_submitted(res){
        var obj = JSON.parse(res);
        console.log(obj);
        if(obj.status == '1'){
            showToast(obj.message, 'success');
            showLoading(false);
            $(".jconfirm-closeIcon").trigger("click");
            window.location.reload(true);
        }else{
            showLoading(false);
            showToast(obj.message, 'error');
        }
    }
</script>

<script>
    $(document).ready(function() {
        $("body").on("click", ".btn-add-item",  function(){
            var url      = window.location.href;
            $.ajax({
                url:url,
                type:"POST",
                data:{
                    is_ajax:1
                },
                success:function(res){
                    try{
                        var obj = JSON.parse(res);
                        if(obj.status == '0'){
                            window.location.href = obj.redirect;
                        }
                    }catch(e){

                    }
                }
            });

            var category_id = $(this).attr('data-category_id');
            $("select.forms-category_id option").removeAttr("selected");
            $("select.forms-category_id option[value='"+category_id+"']").attr("selected", "selected");

            var html = $(".upload-post-content").html();
            show_dialog(lang("Upload Picture/Video"), html, "large");
            //showLoading(true);
        });
        $("body").on("change", ".jconfirm .forms-type", function(){
            var type = $(this).val();
            var form = $(this).closest("form");
            form.find(".media-block").addClass("hidden");
            form.find("."+type+"-block").removeClass("hidden");
        });
        $("body").on("click", ".btn-choose-file", function(){
            $(this).closest(".file-input-box").find(".hidden-file-obj").trigger("click");
        });
        /*$("body").on("click", ".picture-preview", function(){
         $(this).closest(".file-input-box").find(".hidden-file-obj").trigger("click");
         });*/
        $("body").on("change", ".hidden-file-obj", function(){
            var ths = $(this);
            var media_type = ths.closest(".file-input-box").attr("data-media");
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    if(media_type == 'video'){
                        /*var file = e.target.result;
                         ths.closest(".file-input-box").find("video").attr('src', file);*/
                    }else{
                        ths.closest(".file-input-box").find(".picture-preview").attr('src', e.target.result).fadeOut(500).fadeIn(500);
                    }
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        $("body").on("click", ".btn-post-submit", function(){
            var form = $(this).closest('form');
            var form_is_valid = checkFromValidation(form);
            console.log(form_is_valid);
            if(form_is_valid){
                showLoading(true);
                form.submit();
            }
        });


        /*script video player part*/
        $("body").on("click", ".video-controller", function(){
            var video_link = $(this).attr("data-src");
            var video_title = $(this).attr("data-title");
            if(video_link != "" && video_link != undefined && video_link != null){
                console.log(video_link);
                $(".video-player-dlg-content video source").attr("src", video_link);
                var html = $(".video-player-dlg-content").html();
                if(video_title==''){
                    video_title = "Video View"
                }
                show_dialog(video_title, html, "xlarge");
                setTimeout(function(){
                    var mediaVideo = $(".jconfirm .video-viewer video").get(0);
                    mediaVideo.load();
                }, 100);
                $(".slick-lightbox-close").trigger('click');
            }
            /*var video_wrapper = $(this).closest('.video-wrapper');
             //video_wrapper.find("video").play();
             var mediaVideo = video_wrapper.find("video").get(0);
             if (mediaVideo.paused) {
             mediaVideo.play();
             video_wrapper.addClass("playing");
             } else {
             mediaVideo.pause();
             video_wrapper.removeClass("playing");
             }*/
        });
        /*$("body").on("mouseover", ".video-wrapper", function(){
         var video_wrapper = $(this);
         var mediaVideo = video_wrapper.find("video").get(0);
         if (mediaVideo.paused) {
         video_wrapper.removeClass("playing");
         }
         });*/
        /*script video player part*/

    });
</script>
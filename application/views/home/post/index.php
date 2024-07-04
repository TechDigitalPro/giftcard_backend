<section class="section-title_top" style="background-image: linear-gradient(to top , rgba(0,0,0, 0.5) 0%, rgba(0,0,0,0.5) 100%) , url(<?php echo $data['banner']; ?>); ">
    <div class="container-fluid">
        <div class="menu-title_block ">
            <h2><?php echo $data['page_title'];?></h2>
            <p class="hidden">At vero eos et accusamus et iusto odio dignissimos ducmus qui blanditiis praesentium voluptatum deleolor</p>
        </div>
    </div>
</section>
<section class="section-shop -cart post-page">
    <div class="container-fluid padding-0">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="container-fluid">
                    <div class="btn-add-item-wrapper text-right">
                        <a href="javascript:void(0);" class="btn-add-item icon icon-sm icon-steel-green" data-category_id="1">
                            <img src="<?php echo $base_url; ?>assets/user/images/add-button.png?v=1">
                        </a>
                    </div>
                    <div class="galley clearfix">
                        <div class="row">
                            <div class="col-md-9 col-md-offset-2">
                                <div class="gallery-menu" id="gallery-filter">
                                </div>
                            </div>
                        </div>
                        <div class="gallery-content" id="gallery-content">
                            <?php
                            $post_list = isset($data['post_list'])?$data['post_list']:array();
                            foreach($post_list as $i => $post_info){
                                if($post_info['type'] == 'picture'){
                                    ?>
                                    <a href="<?php echo ($post_info['image_url']);?>" class="gallery-item -width33 -height100" data-groups='["Interior"]'>
                                        <div class="gallery-overlay">
                                            <div class="gallery-overlay-content">
                                                <h3><?php echo ($post_info['title']);?></h3>
                                                <p><?php echo ($post_info['description']);?></p>
                                            </div>
                                        </div>
                                        <div class="gallery-img">
                                            <div class="img" style="background-image: url(<?php echo ($post_info['image_url']);?>);"></div>
                                        </div>
                                    </a>
                                    <?php
                                }else{
                                    ?>
                                    <a href="<?php echo ($post_info['image_url']);?>" class="gallery-item -width33 -height100" data-groups='["Interior"]'>
                                        <div class="gallery-overlay">
                                            <div class="gallery-overlay-content">
                                                <h3 class="hidden"><?php echo ($post_info['title']);?></h3>
                                                <p class="hidden"><?php echo ($post_info['description']);?></p>
                                                <span  class="video-controller" data-src="<?php echo get_media($post_info['video']); ?>" data-title="<?php echo ($post_info['title']);?>">
                                                    <i class="fa fa-play-circle" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="gallery-img">
                                            <div class="img" style="background-image: url(<?php echo ($post_info['image_url']);?>);"></div>
                                        </div>
                                    </a>
                                    <div class="video-wrapper video-wrapper1 col-xs-12 col-sm-6 col-lg-4 isotope-item" data-filter="*">

                                    </div>
                                    <?php
                                }
                            }
                            ?>
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
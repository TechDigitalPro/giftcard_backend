<?php include_once(APPPATH.'views/'.$current_directory.'includes/menu.php'); ?>
<header class="header -scroll_white">
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main_navbar" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo $base_url; ?>">
                    <img src="<?php echo $base_url; ?>assets/global/img/kerart-logo.png" alt="" class="logo_white">
                    <img src="<?php echo $base_url; ?>assets/global/img/kerart-logo.png" alt="" class="logo_black">
                </a>
            </div>
            <div class="collapse navbar-collapse" id="main_navbar">
                <ul class="nav navbar-nav navbar-right">
                    <?php
                    $current_uri=strtolower($current_c_directory.$current_controller.'/'.$current_method);
                    foreach ($menu_arr as $top_index => $top_item){
                        $top_active_class="";
                        $current_sub_index=0;
                        $sub_menu_list=$top_item['sub_menu_list'];
                        foreach ($sub_menu_list as $sub_index => $sub_item) {
                            if(contain_current_url($current_uri, $sub_item['tab_list'])){
                                $top_active_class="active";
                                $current_sub_index=$sub_index;
                            }
                        }
                        if(count($sub_menu_list)==1){
                            ?>
                            <li class="<?php echo($top_active_class); ?>" data-current-url="<?php echo $current_uri; ?>">
                                <a href="<?php echo(make_url(@$sub_menu_list[0]['tab_list'][0])); ?>">
                                    <svg version="1.1" class="ico-square" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                         viewBox="0 0 9.2 9.2" enable-background="new 0 0 9.2 9.2" xml:space="preserve">
						<path fill="#FFD03B" d="M5.8,8.7C5.5,9,5,9.2,4.6,9.2S3.7,9,3.4,8.7L0.5,5.8c-0.7-0.7-0.7-1.7,0-2.4l2.9-2.9c0.7-0.7,1.7-0.7,2.4,0
							l2.9,2.9c0.7,0.7,0.7,1.7,0,2.4L5.8,8.7z"/>
						</svg>
                                    <span><?php echo($top_item['text']); ?></span>
                                    <svg version="1.1" class="ico-square" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                         viewBox="0 0 9.2 9.2" enable-background="new 0 0 9.2 9.2" xml:space="preserve">
						<path fill="#FFD03B" d="M5.8,8.7C5.5,9,5,9.2,4.6,9.2S3.7,9,3.4,8.7L0.5,5.8c-0.7-0.7-0.7-1.7,0-2.4l2.9-2.9c0.7-0.7,1.7-0.7,2.4,0
							l2.9,2.9c0.7,0.7,0.7,1.7,0,2.4L5.8,8.7z"/>
						</svg>
                                </a>
                            </li>
                            <?php
                        }elseif(count($sub_menu_list)>1){
                            ?>
                            <li class="<?php echo($top_active_class); ?>" data-has-icon="<?php echo(empty($top_item['letter_text'])?"0":"1"); ?>">
                                <a data-toggle="collapse" data-has-icon="<?php echo(empty($top_item['letter_text'])?"0":"1"); ?>" href="javascript:void(0);" <?php echo($top_active_class=="active open"?'aria-expanded="true"':''); ?>>
                                    <svg version="1.1" class="ico-square" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                         viewBox="0 0 9.2 9.2" enable-background="new 0 0 9.2 9.2" xml:space="preserve">
						<path fill="#FFD03B" d="M5.8,8.7C5.5,9,5,9.2,4.6,9.2S3.7,9,3.4,8.7L0.5,5.8c-0.7-0.7-0.7-1.7,0-2.4l2.9-2.9c0.7-0.7,1.7-0.7,2.4,0
							l2.9,2.9c0.7,0.7,0.7,1.7,0,2.4L5.8,8.7z"/>
						</svg>
                                    <?php
                                    if(empty($top_item['letter_text'])) {
                                        echo('<span>'.$top_item['text'].'</span>');
                                    } else {
                                        echo('<i class="'.$top_item['letter_text'].'"></i>');
                                    }
                                    ?>
                                    <svg version="1.1" class="ico-square" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                         viewBox="0 0 9.2 9.2" enable-background="new 0 0 9.2 9.2" xml:space="preserve">
						<path fill="#FFD03B" d="M5.8,8.7C5.5,9,5,9.2,4.6,9.2S3.7,9,3.4,8.7L0.5,5.8c-0.7-0.7-0.7-1.7,0-2.4l2.9-2.9c0.7-0.7,1.7-0.7,2.4,0
							l2.9,2.9c0.7,0.7,0.7,1.7,0,2.4L5.8,8.7z"/>
						</svg>
                                </a>
                                <ul class="navbar-dropdown">
                                    <?php
                                    foreach ($sub_menu_list as $key => $sub_item){
                                        if(empty($sub_item['text'])){
                                            continue;
                                        }
                                        $active_class="";
                                        if($current_sub_index === $key && $top_active_class!="") {
                                            $active_class='active';
                                        }
                                        $url=make_url($sub_item['tab_list'][0]);
                                        ?>
                                        <li class="<?php echo($active_class); ?>">
                                            <a href="<?php echo($url); ?>"><?php echo($sub_item['text']); ?></a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </li>
                            <?php
                        }
                    }
                    ?>
                    <li>
                        <a href="javascript:void(0);" class="audio-play-btn volume-off" id="audio-play-btn-desktop" style="margin-right: 30px"><i class="fa fa-volume-off"></i></a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>

    <a href="javascript:void(0);" class="audio-play-btn volume-off" id="audio-play-btn-mobile" style="margin-right: 30px"><i class="fa fa-volume-off"></i></a>
</header>
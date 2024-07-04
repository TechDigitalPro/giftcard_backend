<div class="sidebar" data-active-color="rose" data-background-color="black" data-image="<?php echo($theme_user_url); ?>assets/img/sidebar.png">
    <!--
Tip 1: You can change the color of active element of the sidebar using: data-active-color="purple | blue | green | orange | red | rose"
Tip 2: you can also add an image using data-image tag
Tip 3: you can change the color of the sidebar with data-background-color="white | black"
-->
    <div class="logo">
        <a href="<?php echo $base_url;?>" class="simple-text">
            <img class="hidden" src="<?php echo $base_url."assets/admin/images/Logo.jpeg";?>" />
            <b style="display: block; padding: 30px; font-size: 30px; font-family: serif; color: #ffe400"><?php echo C('app_name'); ?></b>
        </a>
    </div>
    <div class="logo logo-mini">
        <a href="<?php echo $base_url;?>" class="simple-text">
            CA
        </a>
    </div>
    <div class="sidebar-wrapper">
        <div class="block sidebar-content">
            <div class="vertical-middle-content">
                <?php include_once(APPPATH.'views/'.$current_directory.'includes/menu.php'); ?>
                <ul class="nav" style="margin-top: 20px">
                    <?php
                    $current_uri=strtolower($current_directory.$current_controller.'/'.$current_method);
                    foreach ($menu_arr as $top_index => $top_item){
                        $top_active_class="";
                        $current_sub_index=0;
                        $sub_menu_list=$top_item['sub_menu_list'];
                        foreach ($sub_menu_list as $sub_index => $sub_item) {
                            if(contain_current_url($current_uri, $sub_item['tab_list'])){
                                $top_active_class="active open";
                                $current_sub_index=$sub_index;
                            }
                        }
                        if(count($sub_menu_list)==1){
                            ?>
                            <li class="<?php echo($top_active_class); ?>">
                                <a href="<?php echo(make_url(@$sub_menu_list[0]['tab_list'][0])); ?>">
                                    <i class="material-icons"><?php echo($top_item['letter_text']); ?></i>
                                    <p><?php echo($top_item['text']); ?></p>
                                </a>
                            </li>
                            <?php
                        }elseif(count($sub_menu_list)>1){
                            ?>
                            <li class="<?php echo($top_active_class); ?>">
                                <a data-toggle="collapse" href="#pagesExamples" <?php echo($top_active_class=="active open"?'aria-expanded="true"':''); ?>>
                                    <i class="material-icons"><?php echo($top_item['letter_text']); ?></i>
                                    <p><?php echo($top_item['text']); ?>
                                        <b class="caret"></b>
                                    </p>
                                </a>
                                <div class="<?php echo($top_active_class=="active open"?'collapse in':'collapse'); ?>" id="pagesExamples">
                                    <ul class="nav">
                                        <?php
                                        foreach ($sub_menu_list as $key => $sub_item){
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
                                </div>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>
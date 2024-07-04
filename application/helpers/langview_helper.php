<?php
function languageSwitcherHtml(){
    $current_language=get_instance()->session->userdata('site_lang');
    ?>
    <li class="dropdown <?php echo $current_language; ?>">
        <?php
        if(empty($current_language) || $current_language=='english'){
            ?>
            <a href class="dropdown-toggle" data-toggle="dropdown">
                <i class="flag-icon flag-icon-us"></i>English
            </a>
            <?php
        }elseif($current_language=='chinese'){
            ?>
            <a href class="dropdown-toggle" data-toggle="dropdown">
                <i class="flag-icon flag-icon-cn"></i>中文
            </a>
            <?php
        }elseif($current_language=='russian'){
            ?>
            <a href class="dropdown-toggle" data-toggle="dropdown">
                <i class="flag-icon flag-icon-ru"></i>русский
            </a>
            <?php
        }
        ?>

        <ul role="menu" class="dropdown-menu dropdown-light fadeInUpShort">
            <li>
                <a href="<?php echo(base_url('LanguageSwitcher/switchLang/english')); ?>" class="language-item menu-toggler <?php echo($current_language=='english'?'selected':''); ?>"> <i class="flag-icon flag-icon-us"></i> English  </a>
            </li>
            <li>
                <a href="<?php echo(base_url('LanguageSwitcher/switchLang/chinese')); ?>" class="language-item menu-toggler <?php echo($current_language=='chinese'?'selected':''); ?>"> <i class="flag-icon flag-icon-cn"></i> 中文 </a>
            </li>
            <li class="hidden">
                <a href="<?php echo(base_url('LanguageSwitcher/switchLang/russian')); ?>" class="language-item menu-toggler <?php echo($current_language=='russian'?'selected':''); ?>"> <i class="flag-icon flag-icon-ru"></i> русский </a>
            </li>
            <li class="hidden">
                <a href="<?php echo(base_url('LanguageSwitcher/switchLang/indonesia')); ?>" class="language-item menu-toggler <?php echo($current_language=='indonesia'?'selected':''); ?>"> <i class="flag-icon flag-icon-id"></i> indonesia </a>
            </li>
            <li class="hidden">
                <a href="<?php echo(base_url('LanguageSwitcher/switchLang/brazil')); ?>" class="language-item menu-toggler <?php echo($current_language=='brazil'?'selected':''); ?>"> <i class="flag-icon flag-icon-br"></i> brazil </a>
            </li>
        </ul>
    </li>
    <?php
}


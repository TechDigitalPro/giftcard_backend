<nav class="navbar navbar-transparent navbar-absolute">
    <div class="container-fluid">
        <div class="navbar-minimize">
            <button id="minimizeSidebar" class="btn btn-round btn-white btn-fill btn-just-icon">
                <i class="material-icons visible-on-sidebar-regular">more_vert</i>
                <i class="material-icons visible-on-sidebar-mini">view_list</i>
            </button>
        </div>
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"> <?php echo (isset($data['page_title'])?$data['page_title']:"")?> </a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li class="">
                    <a href="javascript:void(0);">
                        <p class="top-user-name-status">
                            <span class="hi-text">Hi,</span>
                            <span class="name-text"><?php echo($data['admin']['admin_name']);?></span>
                            <span class="rank-text">Admin</span>
                        </p>
                    </a>
                </li>
                <li class="hidden dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="material-icons">notifications</i>
                        <span class="notification">3</span>
                        <p class="hidden-lg hidden-md">
                            Notifications
                            <b class="caret"></b>
                        </p>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#">Mike John responded to your email</a>
                        </li>
                        <li>
                            <a href="#">You have 5 new tasks</a>
                        </li>
                        <li>
                            <a href="#">Another One</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="btn-logout" data-original-title="Expand" rel="tooltip111" data-placement="bottom" data-trigger="hover" href="javascript:void(0);" data-href="<?php echo base_url('admin/dashboard/logout')?>" title="logout">
                        <i class="material-icons">person</i>
                        <p class="hidden-lg hidden-md">Profile</p>
                    </a>
                </li>
                <li class="separator hidden-lg hidden-md"></li>
            </ul>
        </div>
    </div>
</nav>

<script>
    $(document).ready(function(){
        $(".btn-logout").click(function(){
            var url = $(this).attr('data-href');
            show_confirmDlg("Are you sure to logout?",function(){
                window.location.href = url;
            });
        });
    });
</script>
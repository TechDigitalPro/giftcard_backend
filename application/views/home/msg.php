<?php
//print_r($data);die;
?>
<div class="container content valign__middle">
    <div class="logo-container" style="color: #ff1800; font-size: 24px; font-weight: bold;">
        商家管理平台
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="msg-content text-center">
                <h1 class="msg <?php echo($data['msg_type']=='succ' || $data['msg_type']=='success' ? 'msg-success' : 'msg-error'); ?>"><?php echo $data['msg']; ?></h1>
                <span class="msg-desc">如果此页面没有在<?php echo $data['msg_duration']; ?>ms内转到重定向网址，请点击下面的“返回上一页”按钮 </span>
                <div class="action-wrap">
                    <a class="btn-u btn-u-lg btn-u-blue" href="<?php echo $data['redirect_url']; ?>">返回上一页</a>
                </div>
            </div>
        </div>
    </div>
</div>





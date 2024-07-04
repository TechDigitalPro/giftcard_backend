/**
 * Created by Administrator on 7/26/2019.
 */
function showNotification(message, type, icon, from, align){
    if(type=='error'){
        type='danger';
    }
    var type_list = ['','info','success','warning','danger','rose','primary'];
    var icon_list=['notifications','error','warning'];
    if(type=="" || type==undefined){
        type="success";
    }
    if(from=="" || from==undefined){
        from="top";
    }
    if(align=="" || align==undefined){
        align="right";
    }
    if(icon=="" || icon==undefined){
        icon="notifications";
    }

    $.notify({
        icon: icon,
        message: message
    },{
        type: type,
        timer: 1000,
        placement: {
            from: from,
            align: align
        }
    });
}
function checkFromValidation(form){
    var is_valid = true;
    form.find('[name]').each(function() {
        console.log($(this).attr("name"));
        var val = $(this).val();
        var constraints = $(this).attr('data-field-property');
        if(is_empty(constraints)) constraints = "";
        $(this).closest(".form-group").find('.form-validation').remove();
        if(constraints.indexOf("@Email") !== -1){
            if(!validateEmail(val)){
                is_valid = false;
                $(this).closest(".form-group").find('.form-validation').remove();
                $(this).after('<span class="form-validation">'+lang('Invalid email format')+'</span>');
            }
        }
        if(constraints.indexOf("@Required") !== -1){
            if(is_empty(val)){
                is_valid = false;
                $(this).closest(".form-group").find('.form-validation').remove();
                $(this).after('<span class="form-validation">'+lang('This field is required')+'</span>');
            }
        }
        $(this).unbind("change").bind("change", function(){
            if($(this).val()!=''){
                $(this).closest(".form-group").find('.form-validation').remove();
            }
        });
    });
    return is_valid;
}
function showToast(msg, type, callback) {
    if (!msg) msg = '';
    if (!type) type = 'info'; /*info error success*/
    if (typeof toastr !== "undefined") {
        if (typeof msg === 'object') msg = msg[0];
        toastr[type](msg, "Notice");
        setTimeout(function() {
            callback && callback();
        }, 2000);
    } else {
        alert(msg);
    }
}
function showLoading(flag){
    if(flag){
        $("body").removeClass("loaded");
    }else{
        $("body").addClass("loaded");
    }
}
function show_loading(flag){
    if(flag){
        $(".loading-overlayer").show();
    }else{
        $(".loading-overlayer").hide();
    }
}
/////////////////////////////////////////
function show_dialog(title, content,d_size){
    /*
     * d_size: xsmall,small,medium,large,xlarge
     * */
    if(d_size == undefined || d_size == null) d_size = "medium";
    var dlg = $.confirm({
        title: title,
        closeIcon: true,
        closeIconClass: 'fa fa-close',
        content: content,
        columnClass: d_size,
        buttons: false
    });
    return dlg;
}
function show_dialog1(title, content,d_size){
    /*
     * d_size: xsmall,small,medium,large,xlarge
     * */
    if(d_size == undefined || d_size == null) d_size = "medium";
    var dlg = $.confirm({
        title: title,
        closeIcon: false,
        closeIconClass: 'fa fa-close',
        content: content,
        columnClass: d_size,
        buttons: false
    });
    return dlg;

}
function show_dialog2(title, content,d_size){
    /*
     * d_size: xsmall,small,medium,large,xlarge
     * */
    if(d_size == undefined || d_size == null) d_size = "medium";
    var dlg = $.confirm({
        title: title,
        closeIcon: true,
        backgroundDismiss: true,
        closeIconClass: 'fa fa-close',
        content: content,
        columnClass: d_size,
        buttons: false
    });
    return dlg;

}
function show_alert(content ,callback_yes){
    $.confirm({
        title: APP_NAME,/*false*/
        closeIcon: true,
        closeIconClass: 'fa fa-close',
        content: content,
        buttons: {
            yes: {
                text: 'OK',
                /*isHidden: true, */// hide the button
                keys: ['y'],
                btnClass:'btn-blue',
                action: function () {
                    if(callback_yes !== undefined && callback_yes!==null){
                        callback_yes();
                    }
                }
            }
        }
    });
}
function show_confirmDlg(content,callback_yes){
    $.confirm({
        title: APP_NAME,/*false*/
        closeIcon: true,
        closeIconClass: 'fa fa-close',
        content: content,
        buttons: {
            no: {
                text:'No',
                keys: ['N'],
                btnClass:'btn-default',
                action: function () {

                }
            },
            yes: {
                text: 'Yes',
                /*isHidden: true, */// hide the button
                keys: ['y'],
                btnClass:'btn-blue',
                action: function () {
                    if(callback_yes !== undefined && callback_yes!==null){
                        callback_yes();
                    }
                }
            }
        }
    });
}

/*showConfirmDlg('aaa',function(){
 alert('dddd');
 });*/
function is_empty(value){
    if (value===undefined || value === null || value == '') {
        return true;
    } else {
        return false;
    }
}
function is_null(value){
    if (value===undefined || value === null) {
        return true;
    } else {
        return false;
    }
}
function priceFormat(num) {
    return num.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
}
$(document).ready(function(){
   /* $('.js-select-basic-multiple').select2();
    $('.js-select2').select2();*/

    $("body").on("click",".btn-close-dlg", function(){
        $(this).closest(".jconfirm-box-container").find(".jconfirm-closeIcon").click();
    });


});
$(document).ready(function () {
    $("body").on("change","select.tab-selector",function () {
        var val=$(this).val();
        $(".tab-select-item-wraper").addClass("hidden");
        $(".tab-select-item-wraper").removeClass("current");
        $(".tab-select-item-wraper[data-value='"+val+"']").removeClass("hidden");
        $(".tab-select-item-wraper[data-value='"+val+"']").addClass("current");
    });
});

var sign_out=function () {
    $.cookie("token", "", {expires:3650, path:'/'});
    $.cookie("memberInfo", "", {expires:3650, path:'/'});
    location.href=base_url;
};
var update_memberInfo=function (memberInfo) {
    member_info = memberInfo;
    memberInfo=JSON.stringify(memberInfo);
    $.cookie("memberInfo", memberInfo, {expires:3650, path:'/'});
    try {
        $(".user .photo img").attr("src",member_info.user_image);
        var user_name=member_info.user_name;
        if(user_name==""){
            user_name=member_info.user_email;
        }
        if(user_name.length>8){
            user_name=user_name.substring(0,7)+"...";
        }
        $(".user .info a[data-toggle='collapse']").html(user_name+'<b class="caret"></b>');
    }catch (e){
        console.log(e);
    }
};

var form_ajax_post=function (api_url,form,callback) {
    var post_data={};
    var data_array=form.serializeArray();
    for(var i in data_array){
        var data=data_array[i];
        post_data[data.name]=data.value;
    }
    post_data['is_ajax']='1';
    $.ajax({
        url:api_url,
        data:post_data,
        type:"POST",
        success:function (res) {
            callback(res);
        }
    });
};

var ajax_post=function (api_url,post_data,callback) {
    $.ajax({
        url:api_url,
        data:post_data,
        type:"POST",
        success:function (res) {
            var obj=$.parseJSON(res);
            if(obj.result=='WRONG_MEMBER_KEY'){
                sign_out();
            }else{
                callback(res);
            }
        }
    });
};

var ajax_raw_post=function (post_data,callback) {
    $.ajax({
        url:api_url,
        data:JSON.stringify(post_data),
        type:"POST",
        success:function (res) {
            var obj=$.parseJSON(res);
            if(obj.result=='WRONG_MEMBER_KEY'){
                sign_out();
            }else{
                callback(res);
            }
        }
    });
};

var disp_val=function (value) {
    if(value==undefined || value==null){
        value="";
    }
    return value;
};
var str_encrypt=function (str) {
    var encrypted = CryptoJS.AES.encrypt(str, secret_passphrase);
    return encrypted;
};
var str_decrypt=function (encrypted_str) {
    var decrypted = CryptoJS.AES.decrypt(encrypted_str, secret_passphrase);
    var str = decrypted.toString(CryptoJS.enc.Utf8);
    return str;
};
var validateEmail = function(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
};
var validatePhoneNumber = function(phone) {
    var phoneNum = phone.replace(/[^\d]/g, '');
    if(phoneNum.length > 6 && phoneNum.length < 11) {
        return true;
    }else{
        return false;
    }
};
var validatePassword = function(value) {
    var msg="";
    if(value.length < 6) {
        msg = "Password must contain at least 8  characters!";
    }
    var re = /[0-9]/;
    if(!re.test(value)) {
        msg="Please ensure that you have at least one lower case letter, one upper case letter, one digit, and one special character";
    }
    re = /[a-z]/;
    if(!re.test(value)) {
        msg="Please ensure that you have at least one lower case letter, one upper case letter, one digit, and one special character";
    }
    re = /[A-Z]/;
    if(!re.test(value)) {
        msg="Please ensure that you have at least one lower case letter, one upper case letter, one digit, and one special character";
    }
    re=/^[a-zA-Z0-9- ]*$/;
    if (re.test(value) != false) {
        msg="Please ensure that you have at least one lower case letter, one upper case letter, one digit, and one special character";
    }
    return msg;
};
function get_date_time(timestamp,type,pattern) {
    timestamp=parseInt(timestamp);
        // Create a new JavaScript Date object based on the timestamp
    // multiplied by 1000 so that the argument is in milliseconds, not seconds.
    var date = new Date(timestamp*1000);
    var year=date.getFullYear();
    var month="0" +date.getMonth();
    var day="0" +date.getDay();
    // Hours part from the timestamp
    var hours = "0" +date.getHours();
    // Minutes part from the timestamp
    var minutes = "0" + date.getMinutes();
    // Seconds part from the timestamp
    var seconds = "0" + date.getSeconds();

    var formattedTime='';
    if(pattern=='Y-m-d'){
        formattedTime =year+'-'+month.substr(-2)+'-'+day.substr(-2)+' '+ hours.substr(-2) + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
        if(type=='time'){
            formattedTime =hours.substr(-2) + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
        }else if(type=='date'){
            formattedTime =year+'-'+month.substr(-2)+'-'+day.substr(-2);
        }
    }else if(pattern=='MM/dd/yyyy'){
        formattedTime =month.substr(-2)+'/'+day.substr(-2)+'/'+year +' '+ hours.substr(-2) + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
        if(type=='time'){
            formattedTime =hours.substr(-2) + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
        }else if(type=='date'){
            formattedTime =month.substr(-2)+'/'+day.substr(-2)+'/'+year;
        }
    }
    return formattedTime;

}
function formatcurrency(value) {
    var val=parseFloat(value);
    var formatedVal= val.toFixed(2);
    return '$'+formatedVal;
}
function formatPrice(value) {
    var val=parseFloat(value);
    var formatedVal= val.toFixed(2);
    return formatedVal;
}
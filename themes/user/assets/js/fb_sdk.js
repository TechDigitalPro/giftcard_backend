/******************************************facebook login**********************************************/
$(document).ready(function() {
    window.fbAsyncInit = function() {
        FB.init({
            appId      : "948399935291377",
            xfbml      : true,
            version    : 'v2.8'
        });
    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));


    function basicAPIRequest() {
        FB.api('/me',
            {fields: "id,about,age_range,picture.type(large),birthday,context,email,first_name,gender,hometown,link,location,middle_name,name,timezone,website,work"},
            function(response) {
                console.log('API response:', response);
                if(response.id!=undefined && response.id!=null){
                    console.log(response);
                    var post_data=
                        {
                            method:"login_facebook",
                            user_name:response.name,
                            user_email:response.email,
                            fb_id:response.id,
                            user_phone:"",
                            user_image:response.picture.data.url
                        };
                    ajax_post(post_data,function (res) {
                        console.log(res);
                        try {
                            var obj=$.parseJSON(res);
                            console.log(obj);
                            if(obj.result=="OK"){
                                $("button.submit").prop("disabled",true);
                                $.cookie("token", obj.token, {expires:3650, path:'/'});
                                $.cookie("memberInfo", JSON.stringify(obj.memberInfo), {expires:3650, path:'/'});
                                demo.showNotification('Congratulations! <br/> Login successful.');
                                setTimeout(function () {
                                    location.href=base_url+"user/subscribed_services";
                                },2000);
                            }else{
                                demo.showNotification(obj.message,'warning','error');//var type_list = ['','info','success','warning','danger','rose','primary'];
                            }
                        }catch(e){
                            demo.showNotification(res,'danger','error');//var type_list = ['','info','success','warning','danger','rose','primary'];
                        }
                    });
                }
            }
        );
    }
    $(".facebook-login").click(function(e){
        if(typeof(FB) == "undefined") {
            //alert("Facebook SDK not yet loaded please wait.");
            swal({
                title: "Facebook SDK not yet loaded please wait.",
                buttonsStyling: false,
                confirmButtonClass: "btn btn-success"
            });
        }
        FB.getLoginStatus(function(response) {
            console.log("login status response:");
            console.log(response);

            if (response.status === 'connected') {
                console.log('Logged in.');
                basicAPIRequest();
            }
            else {
                FB.login(function (res) {
                    console.log('login result:');
                    console.log(res);
                    if (res.status === 'connected') {
                        console.log('Logged in.');
                        basicAPIRequest();
                    }
                });
            }
        });
    });
});
/******************************************************************************************************/
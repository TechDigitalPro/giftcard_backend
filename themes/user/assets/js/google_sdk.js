$(document).ready(function () {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/client.js?onload=onLoadCallback';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);

    $("body").on("click",".google-login",function () {
        google_login();
    });
});

var google_logout = function() {
    gapi.auth.signOut();
    location.reload();
};

var google_login = function() {
    var myParams = {
        /*'clientid' : '276914971844-rv82llia37ri52vh1lmhum7ci5iia4u1.apps.googleusercontent.com', //your client id*/
        'clientid' : '545217365620-4bn435vv77rr5r6ct7ml9b5r1b38kad4.apps.googleusercontent.com', //your client id
        'cookiepolicy' : 'single_host_origin',
        'callback' : 'loginCallback',
        'approvalprompt':'force',
        'scope' : 'https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.profile.emails.read'
    };
    gapi.auth.signIn(myParams);
};

var loginCallback = function (result) {
    if(result['status']['signed_in'])
    {
        var request = gapi.client.plus.people.get(
            {
                'userId': 'me'
            });
        request.execute(function (resp)
        {
            var email = '';
            if(resp['emails'])
            {
                for(i = 0; i < resp['emails'].length; i++)
                {
                    if(resp['emails'][i]['type'] == 'account')
                    {
                        email = resp['emails'][i]['value'];
                    }
                }
            }
            console.log(resp);
            if(resp.id!=undefined && resp.id!=null){
                var user_image = resp.image.url;
                if(user_image==undefined || user_image==null){
                    user_image="";
                }else{
                    user_image=user_image.split("?")[0] + "?sz=300";
                }
                var post_data=
                    {
                        method:"login_google",
                        user_name:resp.displayName,
                        user_email:email,
                        google_id:resp.id,
                        user_phone:"",
                        user_image:user_image
                    };
                console.log(post_data);
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
        });

    }

};

var onLoadCallback = function ()
{
    //gapi.client.setApiKey('AIzaSyCYvLRXDApicXW6u1aYpwgHZYffooGR_Xw'); //your api key
    gapi.client.setApiKey('AIzaSyAebq_rTCZTydG0h0a4ZZqAxBwET7Sq8zs'); //your api key
    gapi.client.load('plus', 'v1',function(){});
};

function getLoaderHtml(bool) {
    var ajax_loading_div;
    if (typeof bool === "undefined") {
        bool = true;
    }
    if (bool) {
        ajax_loading_div = '<div class="loader-wrap">' + '<div class="content">' + '</div>' + '</div>';
    } else {
        ajax_loading_div = '';
    }
    //console.log(ajax_loading_div);
    return ajax_loading_div;
}
/**
 * @class loading
 * @parameter {Function} bool
 */
function loading(bool) {
    if (bool) {
        if($("body").find(".loader-wrap").length>0){
            $('.loader-wrap').remove();
        }
        $('body').append(getLoaderHtml());
    } else {
        $('.loader-wrap').remove();
    }
}
function AjaxCall(url, data, type ,checkLogin, showloader,successC) {
    if (!url) return;
    data = data || {};
    type = type || 'get';

    if (typeof successC !== "function") return;
    $.ajax({
        type: type,
        url: url,
        data: data,
        beforeSend: function() {
            try {
                if (showloader) {
                    loading(true);
                }
            } catch (e) {
            }
        },
        success: function(result) {
            var obj=JSON.parse(result);
            if(checkLogin){
                if(obj.status=='0'){
                    if(obj.redirect!="" && obj.redirect!=undefined && obj.redirect!=null){
                        var login_url=obj.redirect;
                        var current_url=window.location.href;
                        login_url+="&return_url="+encodeURIComponent(current_url);
                        console.log(login_url);
                        window.location.href=login_url;
                    }else{
                        alert(obj.message);
                    }
                }else{
                    successC && successC(obj);
                }
            }else{
                successC && successC(obj);
            }
        },
        complete: function() {
            try {
                if (showloader) {
                    loading(false);
                }
            } catch (e) {

            }
        }
    });
}

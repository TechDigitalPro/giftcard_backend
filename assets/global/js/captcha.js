$(document).ready(function () {
    $("body").on("click",".captcha-action-close",function () {
        $(this).closest(".captcha-body").removeClass("in");
    });
    $("body").on("focus","#captcha",function () {
        $(this).closest(".captcha-wrap").find(".captcha-body").addClass("in");
    });
    $("body").on("click",".captcha-action-refresh",function () {
        $(this).closest(".captcha-wrap").find(".captcha-img").trigger("click");
    });
    $("body").on("click",".captcha-img",function () {
        var seccode_url=$(this).attr("src");
        $(this).closest(".captcha-wrap").find(".captcha-img").attr("src",seccode_url);
    });
});
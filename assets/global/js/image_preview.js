$("body").on("change",".previewable_image",function () {
    var id=$(this).attr('id');
    var preview_default_class=$(this).attr('preview_default');
    var preview_default=$("."+preview_default_class);
    if($(this).hasClass('imidiate-upload')){
        $(this).closest('form').submit();
        return false;
    }
    var oFreader=new FileReader();
    try{
        oFreader.readAsDataURL(document.getElementById(id).files[0]);
        oFreader.onload = function (oFREvent) {
            preview_default.fadeOut();
            preview_default.attr('src',oFREvent.target.result).fadeIn();
        }
    }catch(e){

    }
});

$("body").on("click",".preview-img-wrap",function () {
    var class_name=$(this).find("img").attr("class").split(" ")[1];
    $("input[preview_default='"+class_name+"']").trigger("click");
});

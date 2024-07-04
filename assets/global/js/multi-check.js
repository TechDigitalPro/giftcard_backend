var multi_check_handler={
    init:function () {
        var self=this;
        self.bindEvents();
    },
    bindEvents:function () {
        var self=this;
        $("body").on("change",".multi-check-all",function () {
            var status=$(this).prop('checked');
            if(status){
                $(".multi-check-item").prop('checked',true);
            }else{
                $(".multi-check-item").prop('checked',false);
            }
        });
        $("body").on("change",".multi-check-item",function () {
            var status=true;
            $(".multi-check-item").each(function () {
                if(!$(this).prop('checked')){
                    status=false;
                }
            });
            if(status){
                $(".multi-check-all").prop('checked',true);
            }else{
                $(".multi-check-all").prop('checked',false);
            }
        });
    }
};
$(document).ready(function () {
    multi_check_handler.init();
});


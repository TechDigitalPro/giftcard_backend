var datatable_handler={
    init:function () {
        var self=this;
        self.bindEvents();
    },
    bindEvents:function () {
        var self=this;
        $("body").on("click",".multi-table .row-total-selector",function () {
            var status=$(this).prop('checked');
            if(status){
                $(".multi-table .row-selector").prop('checked',true);
            }else{
                $(".multi-table .row-selector").prop('checked',false);
            }
        });
        $("body").on("click",".multi-table .row-selector",function () {
            var status=true;
            $(".multi-table .row-selector").each(function () {
                if(!$(this).prop('checked')){
                    status=false;
                }
            });
            if(status){
                $(".multi-table .row-total-selector").prop('checked',true);
            }else{
                $(".multi-table .row-total-selector").prop('checked',false);
            }
        });
    }
};
$(document).ready(function () {
    datatable_handler.init();
});


var treeview_controller={
    level_depth:3,
    init:function (initial_status) {
        var self=this;
        if(initial_status==1){

        }else{

        }
        self.bindEvents();
        self.collapse_view(0);
        //self.expand_view(0);
    },
    collapse_view:function (current_id) {
        var self=this;
        var current_level=0;
        if(current_id!=0){
            current_level = $(".treeview-table tr[data-id='"+current_id+"']").attr('data-level');
            $(".treeview-table tr[data-id='"+current_id+"']").addClass('collapsed');

            $(".treeview-table tr[data-parent_id='"+current_id+"']").addClass("hidden_"+current_level);
            $(".treeview-table tr[data-parent_id='"+current_id+"']").each(function () {
                var current_level1=$(this).attr('data-level');
                current_id=$(this).attr('data-id');
                $(".treeview-table tr[data-parent_id='"+current_id+"']").addClass("hidden_"+current_level);
                $(".treeview-table tr[data-parent_id='"+current_id+"']").each(function () {
                    var current_level2=$(this).attr('data-level');
                    current_id=$(this).attr('data-id');
                    $(".treeview-table tr[data-parent_id='"+current_id+"']").addClass("hidden_"+current_level);
                    $(".treeview-table tr[data-parent_id='"+current_id+"']").each(function () {
                        var current_level3=$(this).attr('data-level');
                        current_id=$(this).attr('data-id');
                        $(".treeview-table tr[data-parent_id='"+current_id+"']").addClass("hidden_"+current_level);
                    });
                });
            });
        }else{
            $(".treeview-table tr").addClass("collapsed");
            $(".treeview-table tr[data-parent_id='"+current_id+"']").each(function () {
                current_level=$(this).attr('data-level');
                current_id=$(this).attr('data-id');
                $(".treeview-table tr[data-parent_id='"+current_id+"']").addClass("hidden_"+current_level);
                $(".treeview-table tr[data-parent_id='"+current_id+"']").each(function () {
                    var current_level1=$(this).attr('data-level');
                    current_id=$(this).attr('data-id');
                    $(".treeview-table tr[data-parent_id='"+current_id+"']").addClass("hidden_"+current_level);
                    $(".treeview-table tr[data-parent_id='"+current_id+"']").addClass("hidden_"+current_level1);
                    $(".treeview-table tr[data-parent_id='"+current_id+"']").each(function () {
                        var current_level2=$(this).attr('data-level');
                        current_id=$(this).attr('data-id');
                        $(".treeview-table tr[data-parent_id='"+current_id+"']").addClass("hidden_"+current_level);
                        $(".treeview-table tr[data-parent_id='"+current_id+"']").addClass("hidden_"+current_level1);
                        $(".treeview-table tr[data-parent_id='"+current_id+"']").addClass("hidden_"+current_level2);
                        $(".treeview-table tr[data-parent_id='"+current_id+"']").each(function () {
                            var current_level3=$(this).attr('data-level');
                            current_id=$(this).attr('data-id');
                            $(".treeview-table tr[data-parent_id='"+current_id+"']").addClass("hidden_"+current_level);
                            $(".treeview-table tr[data-parent_id='"+current_id+"']").addClass("hidden_"+current_level1);
                            $(".treeview-table tr[data-parent_id='"+current_id+"']").addClass("hidden_"+current_level2);
                            $(".treeview-table tr[data-parent_id='"+current_id+"']").addClass("hidden_"+current_level3);
                        });
                    });
                });
            });
        }

    },
    expand_view:function (current_id) {
        var self=this;
        var current_level=0;
        if(current_id!=0){
            current_level = $(".treeview-table tr[data-id='"+current_id+"']").attr('data-level');
            $(".treeview-table tr[data-id='"+current_id+"']").removeClass('collapsed');

            $(".treeview-table tr[data-parent_id='"+current_id+"']").removeClass("hidden_"+current_level);
            $(".treeview-table tr[data-parent_id='"+current_id+"']").each(function () {
                var current_level1=$(this).attr('data-level');
                current_id=$(this).attr('data-id');
                $(".treeview-table tr[data-parent_id='"+current_id+"']").removeClass("hidden_"+current_level);
                $(".treeview-table tr[data-parent_id='"+current_id+"']").each(function () {
                    var current_level2=$(this).attr('data-level');
                    current_id=$(this).attr('data-id');
                    $(".treeview-table tr[data-parent_id='"+current_id+"']").removeClass("hidden_"+current_level);
                    $(".treeview-table tr[data-parent_id='"+current_id+"']").each(function () {
                        var current_level3=$(this).attr('data-level');
                        current_id=$(this).attr('data-id');
                        $(".treeview-table tr[data-parent_id='"+current_id+"']").removeClass("hidden_"+current_level);
                    });
                });
            });
        }else{
            $(".treeview-table tr").removeClass("collapsed");
            $(".treeview-table tr[data-parent_id='"+current_id+"']").each(function () {
                current_level=$(this).attr('data-level');
                current_id=$(this).attr('data-id');
                $(".treeview-table tr[data-parent_id='"+current_id+"']").removeClass("hidden_"+current_level);
                $(".treeview-table tr[data-parent_id='"+current_id+"']").each(function () {
                    var current_level1=$(this).attr('data-level');
                    current_id=$(this).attr('data-id');
                    $(".treeview-table tr[data-parent_id='"+current_id+"']").removeClass("hidden_"+current_level);
                    $(".treeview-table tr[data-parent_id='"+current_id+"']").removeClass("hidden_"+current_level1);
                    $(".treeview-table tr[data-parent_id='"+current_id+"']").each(function () {
                        var current_level2=$(this).attr('data-level');
                        current_id=$(this).attr('data-id');
                        $(".treeview-table tr[data-parent_id='"+current_id+"']").removeClass("hidden_"+current_level);
                        $(".treeview-table tr[data-parent_id='"+current_id+"']").removeClass("hidden_"+current_level1);
                        $(".treeview-table tr[data-parent_id='"+current_id+"']").removeClass("hidden_"+current_level2);
                        $(".treeview-table tr[data-parent_id='"+current_id+"']").each(function () {
                            var current_level3=$(this).attr('data-level');
                            current_id=$(this).attr('data-id');
                            $(".treeview-table tr[data-parent_id='"+current_id+"']").removeClass("hidden_"+current_level);
                            $(".treeview-table tr[data-parent_id='"+current_id+"']").removeClass("hidden_"+current_level1);
                            $(".treeview-table tr[data-parent_id='"+current_id+"']").removeClass("hidden_"+current_level2);
                            $(".treeview-table tr[data-parent_id='"+current_id+"']").removeClass("hidden_"+current_level3);
                        });
                    });
                });
            });
        }
    },

    bindEvents:function () {
        var self=this;
        $("body").on("click",".treeview-table .tree-controller",function () {
            var ths=$(this).closest('tr');
            var current_id=ths.attr('data-id');
            if(ths.hasClass('collapsed')){
                self.expand_view(current_id);
            }else{
                self.collapse_view(current_id);
            }
        });
    }
};
treeview_controller.init(0);
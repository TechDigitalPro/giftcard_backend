$(document).ready(function () {
    $('.tag-multi-select2').select2({
        tags: true,
        tokenSeparators: [','],
        language:{
            noResults:function (params) {
                return '没有找到结果';
            }
        }
    });
    $('.multi-select2').select2({

    });
});
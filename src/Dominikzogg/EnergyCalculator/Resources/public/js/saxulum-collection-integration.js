(function($){
    var addSelectPicker = function($selector){
        $selector.select2({
            multiple: $selector.attr('multiple'),
            ajax: {
                url: $selector.attr('data-route'),
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        page: $selector.attr('data-property')
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            templateSelection: function (result) {
                if(result.default) {
                    var $formGroup = $selector.closest('div[id*="comestiblesWithinDay"]');
                    if($formGroup.length == 1) {
                        $formGroup.find('input[id*="amount"]').val(result.default);
                    }
                }

                return result.text;
            },
            minimumInputLength: 1
        });
    };
    $(document).ready(function(){
        $('form').saxulumCollection('init', {});
        $('select').each(function(i, element){
            addSelectPicker($(element));
        });

    });
    $(document).on('saxulum-collection.add', function(e, $element){
        addSelectPicker($('select', $element));
    });
})(jQuery);
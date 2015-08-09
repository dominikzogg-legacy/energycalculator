(function($){
    var addAjaxSelect = function($selector){
        $selector.select2({
            multiple: $selector.attr('multiple'),
            ajax: {
                url: $selector.attr('data-route'),
                dataType: 'json',
                delay: 150,
                data: function (params) {
                    return {
                        q: params.term,
                        choice_label: $selector.attr('data-choice-label')
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
    var addSelect = function($selector) {
        $selector.select2({
            multiple: $selector.attr('multiple')
        });
    };
    $(document).ready(function(){
        $('form').saxulumCollection('init', {});
        $('select:not([data-ajax])').each(function(i, element){
            addSelect($(element));
        });
        $('select[data-ajax]').each(function(i, element){
            addAjaxSelect($(element));
        });
    });
    $(document).on('saxulum-collection.add', function(e, $element){
        $('select:not([data-ajax])', $element).each(function(i, element){
            addSelect($(element));
        });
        $('select[data-ajax]', $element).each(function(i, element){
            addAjaxSelect($(element));
        });
    });
})(jQuery);

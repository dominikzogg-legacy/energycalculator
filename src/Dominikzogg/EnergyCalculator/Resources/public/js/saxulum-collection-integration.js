(function($){
    var addSelectPicker = function($selector){
        $selector.selectpicker();
    };
    $(document).ready(function(){
        $('form').saxulumCollection('init', {});
        addSelectPicker($('select'));
    });
    $(document).on('saxulum-collection.add', function(e, $element){
        addSelectPicker($('select', $element));
    });
})(jQuery);
(function($){
    var addSelectPicker = function($selector){
        $selector.select2();
    };
    $(document).ready(function(){
        $('form').saxulumCollection('init', {});
        addSelectPicker($('select'));
    });
    $(document).on('saxulum-collection.add', function(e, $element){
        addSelectPicker($('select', $element));
    });
})(jQuery);
(function($){
    var addSelectPicker = function($selector){
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
            $selector.selectpicker('mobile');
        } else {
            $selector.selectpicker();
        }
    };
    $(document).ready(function(){
        $('form').saxulumCollection('init', {});
        addSelectPicker($('select'));
    });
    $(document).on('saxulum-collection.add', function(e, $element){
        addSelectPicker($('select', $element));
    });
})(jQuery);
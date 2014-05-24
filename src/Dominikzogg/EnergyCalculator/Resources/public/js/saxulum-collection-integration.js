(function($){
    $(document).ready(function(){
        $('form').saxulumCollection('init', {});
        $('select').selectpicker();
    });
    $(document).on('saxulum-collection.add', function(e, $element){
        $('select', $element).selectpicker();
    });
})(jQuery);
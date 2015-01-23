(function($){
    var $doc = $(document);
    $doc.ready(function(){
        $doc.on('change', 'select[id*="comestible"]', function(e){
            var $comestibleSelect = $(e.target);
            var $comestibleSelectedOption = $comestibleSelect.find(":selected");
            var comestibleDefaultAmount = $comestibleSelectedOption.data('default-value');
            if(comestibleDefaultAmount !== '') {
                var $formGroup = $comestibleSelect.closest('div[id*="comestiblesWithinDay"]');
                var $comestibleAmount = $formGroup.find('input[id*="amount"]');
                $comestibleAmount.val(comestibleDefaultAmount);
            }
        });
    });
})(jQuery);
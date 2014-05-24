(function($){
    $(document).ready(function(){
        $('a[data-delete-link]').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var $deleteLink = $(this);
            var $confirmDeleteModal = $('#confirm-delete');
            $confirmDeleteModal.modal();
            $('[data-delete-confirm-button]', $confirmDeleteModal).click(function(e){
                document.location.href = $deleteLink.attr('href');
            });
        });
    });
})(jQuery);
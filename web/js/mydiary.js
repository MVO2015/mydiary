// http://stackoverflow.com/questions/8982295/confirm-delete-modal-dialog-with-twitter-bootstrap
$('#confirm-delete').on('show.bs.modal', function(e) {
    $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
});

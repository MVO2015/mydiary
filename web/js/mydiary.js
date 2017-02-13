// http://stackoverflow.com/questions/8982295/confirm-delete-modal-dialog-with-twitter-bootstrap
$(document).ready(function () {
    $('#confirm-delete').on('show.bs.modal', function (e) {
        $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
    });

    $("#base_diary_entry_category").select2({
        tags: true,
        ajax: {
            url: "/tag/?",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    //results: data.items,
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return  markup;
        },
        minimumInputLength: 1,
        //templateResult: formatData,
        //templateSelection: formatDataSelection
    })
})

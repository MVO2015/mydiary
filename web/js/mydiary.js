
function formatRepo (repo) {
  if (repo.loading) {
    return repo.text;
  }

  return repo.full_name;
}

function formatRepoSelection (repo) {
  return repo.full_name || repo.text;
}

// http://stackoverflow.com/questions/8982295/confirm-delete-modal-dialog-with-twitter-bootstrap
$(document).ready(function () {
    $('#confirm-delete').on('show.bs.modal', function(e) {
        $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
    });

    //$("#base_diary_entry_category").select2({
    //  tags: true
    //});
    //
    $("#base_diary_entry_category").select2({
      tags: true,
      ajax: {
        url: "/api/categories",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            q: params.term, // search term
            page: params.page
          };
        },
        processResults: function (data, params) {
          // parse the results into the format expected by Select2
          // since we are using custom formatting functions we do not need to
          // alter the remote JSON data, except to indicate that infinite
          // scrolling can be used
          params.page = params.page || 1;

          return {
            results: data.items,
            pagination: {
              more: (params.page * 30) < data.total_count
            }
          };
        },
        cache: true
      },
      minimumInputLength: 1,
      templateResult: formatRepo, // omitted for brevity, see the source of this page
      templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
    });
});





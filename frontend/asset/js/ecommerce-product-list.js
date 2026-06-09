$(document).ready(function() {
    // responsive datatable
    $('#ProductListTable').DataTable({
        language: {
            searchPlaceholder: 'Search...',
            sSearch: '',
        },
        // columnDefs: [{ orderable: false, targets: [0, 4, 5] }],
        "pageLength": 5,
        lengthMenu: [5, 10, 20, 50, 100, 200, 500],
    });
    // responsive datatable
     // SELECT2
     $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity
    });
} );

    
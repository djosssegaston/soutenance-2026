$(document).ready(function () {
    var table = $('#orderListTable').DataTable({
        language: {
            searchPlaceholder: 'Search...',
            sSearch: '',
        },
        // columnDefs: [{ orderable: false, targets: [0, 4, 5] }],
        "pageLength": 5,
        lengthMenu: [5, 10, 20, 50, 100, 200, 500],

        // buttons: {
        //     dom: {
        //         button: {
        //             className: "btn btn-outline-primary"
        //         }
        //     },
        //     buttons: [ 'copy', 'excel', 'pdf', 'print']
        // },

    });

    table.buttons().container()
        .appendTo('#orderListTable_wrapper .col-md-6:eq(1)');

    // SELECT2
     $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity
    });
});
    
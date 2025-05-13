$(document).ready(function () {
    var ajaxConfig = {
        url: "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}",
        type: "GET",
        data: function (d) {
            d.month = $('#month').val();
            d.year = $('#year').val();
        }
    };

    var columnsConfig = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
        { data: 'dweller_id', defaultContent: '' },
        { data: 'nro_confirmation', defaultContent: '' },
        { data: 'amount', defaultContent: '', className: 'text-end' },
        { data: 'date_pay', defaultContent: '', className: 'text-center', orderData: [4] },
        { data: 'date_confirm', defaultContent: '', className: 'text-center', orderData: [4] },
        { data: 'conciliated', defaultContent: '', className: 'text-center' },
        { data: 'action', orderable: false, searchable: false, className: 'text-center' }
    ];

    // Initialize DataTable with complete AJAX configuration
    initializeDataTable(ajaxConfig, columnsConfig);

    // Add month/year filtering functionality
    let timeout = null;

    $('#month, #year').on('change', function () {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            var table = $('#datatable').DataTable();
            table.ajax.reload();
        }, 500);
    });
});

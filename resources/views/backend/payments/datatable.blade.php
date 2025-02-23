$(document).ready(function () {
    $('#datatable').DataTable({
        searchDelay: 2000,
        responsive: true,
        lengthChange: true,
        searching: true,
        processing: true,
        serverSide: true,
        lengthMenu: [[10, 25, 50, 100 ,200 , 500, -1], [10, 25, 50, 100 ,200 , 500, "All"]],
        ajax: {
            url: "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}",
            type: "GET",
            data: function (d) {
                d.month = $('#month').val(); // Obtiene el valor del mes desde un input con id="month"
                d.year = $('#year').val(); // Obtiene el valor del año desde un input con id="year"
            }
        },
        language: {
            url: "{{ asset(config('master.app.web.template').'/assets/vendor_components/datatable/spanish.json') }}"
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'dweller_id', 'defaultContent': '' },
            { data: 'nro_confirmation', 'defaultContent': '' },
            { data: 'amount', 'defaultContent': '', className: 'text-end' },
            { data: 'date_pay', 'defaultContent': '', className: 'text-center', orderData: [4] },
            { data: 'date_confirm', 'defaultContent': '', className: 'text-center', orderData: [4] },
            { data: 'conciliated', 'defaultContent': '', className: 'text-center' },
            { data: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[4, 'desc']], // Orden descendente por defecto en la columna date_pay
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'csv',
                text: 'CSV',
                className: 'btn btn-success btn-xs ms-10',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excel',
                text: 'Excel',
                className: 'btn btn-info btn-xs',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                text: 'PDF',
                className: 'btn btn-warning btn-xs',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'print',
                text: "{{ trans('Print') }}",
                className: 'btn btn-danger btn-xs me-10',
                exportOptions: {
                    columns: ':visible'
                }
            }
        ]
    });

    // Recargar la tabla cuando cambien los valores de mes o año
    let timeout = null;

    $('#month, #year').on('change', function () {
        clearTimeout(timeout); // Limpia el timeout anterior
        timeout = setTimeout(function() {
            $('#datatable').DataTable().ajax.reload(); // Recarga la tabla después de un retraso
        }, 500); // 500 milisegundos de retraso
    });
});

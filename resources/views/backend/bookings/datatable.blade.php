$(document).ready(function () {
	var ajaxUrl = "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}";
    var columnsConfig = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
        { data: 'unit_id', 'defaultContent':''},
        { data: 'common_area_id', 'defaultContent':''},
        { data: 'booking_date', 'defaultContent':''},
        { data: 'start_time', 'render': function(data, type, row) {
            return data + ' - ' + row.end_time;
        }},
        { data: 'amount_paid', 'defaultContent':''},
        { data: 'status' , 'defaultContent':'', className: 'text-center'},
        { data: 'action', orderable: false, searchable: false, className: 'text-center'}
    ];

    initializeDataTable(ajaxUrl, columnsConfig);
})

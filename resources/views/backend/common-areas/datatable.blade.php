$(document).ready(function () {
	var ajaxUrl = "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}";
    var columnsConfig = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
        { data: 'name', 'defaultContent':''},
        { data: 'capacity', 'defaultContent':'0', className: 'text-center'},
        { data: 'booking_fee', 'defaultContent':''},
        { data: 'is_bookable', 'render': function(data) {
            return data ? '<i class="mdi mdi-check-circle text-success"></i>' : '<i class="mdi mdi-close-circle text-danger"></i>';
        }, className: 'text-center'},
        { data: 'status' , 'defaultContent':'', className: 'text-center'},
        { data: 'action', orderable: false, searchable: false, className: 'text-center'}
    ];

    initializeDataTable(ajaxUrl, columnsConfig);
})

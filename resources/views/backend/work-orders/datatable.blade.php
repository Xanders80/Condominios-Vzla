$(document).ready(function () {
	var ajaxUrl = "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}";
    var columnsConfig = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
        { data: 'title', 'defaultContent':''},
        { data: 'supplier_id', 'defaultContent':''},
        { data: 'estimated_cost', 'defaultContent':''},
        { data: 'status' , 'defaultContent':'', className: 'text-center'},
        { data: 'scheduled_date', 'defaultContent':'N/A'},
        { data: 'action', orderable: false, searchable: false, className: 'text-center'}
    ];

    initializeDataTable(ajaxUrl, columnsConfig);
})

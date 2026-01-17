$(document).ready(function () {
	var ajaxUrl = "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}";
    var columnsConfig = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
        { data: 'unit_id', 'defaultContent':''},
        { data: 'receipt_id', 'defaultContent':'N/A'},
        { data: 'amount', 'defaultContent':''},
        { data: 'due_date', 'defaultContent':''},
        { data: 'grace_period_days', 'defaultContent':'0', className: 'text-center'},
        { data: 'status' , 'defaultContent':'', className: 'text-center'},
        { data: 'action', orderable: false, searchable: false, className: 'text-center'}
    ];

    initializeDataTable(ajaxUrl, columnsConfig);
})

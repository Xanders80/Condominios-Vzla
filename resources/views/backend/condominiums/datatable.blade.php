$(document).ready(function () {
	var ajaxUrl = "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}";
    var columnsConfig = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false, orderable: false, className: 'text-center' },
        { data: 'name' , 'defaultContent':''},
        { data: 'name_incharge' , 'defaultContent':''},
        { data: 'reserve_found' , 'defaultContent':'', className: 'text-center'},
        { data: 'rate_percentage' , 'defaultContent':'', className: 'text-center'},
        { data: 'billing_date' , 'defaultContent':'', className: 'text-center'},
        { data: 'active' , 'defaultContent':'', className: 'text-center'},
        { data: 'action', orderable: false, searchable: false, className: 'text-center'}
    ];

    initializeDataTable(ajaxUrl,  columnsConfig);
})

$(document).ready(function () {
	var ajaxUrl = "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}";
    var columnsConfig = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
        { data: 'session_type', 'defaultContent':''},
        { data: 'session_date', 'defaultContent':''},
        { data: 'location', 'defaultContent':''},
        { data: 'quorum_percentage', 'defaultContent':'0 %', className: 'text-center'},
        { data: 'status' , 'defaultContent':'', className: 'text-center'},
        { data: 'action', orderable: false, searchable: false, className: 'text-center'}
    ];

    initializeDataTable(ajaxUrl, columnsConfig);
})

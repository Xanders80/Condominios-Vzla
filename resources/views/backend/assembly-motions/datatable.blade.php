$(document).ready(function () {
	var ajaxUrl = "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}";
    var columnsConfig = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
        { data: 'assembly_session_id', 'defaultContent':''},
        { data: 'title', 'defaultContent':''},
        { data: 'voting_type', 'defaultContent':''},
        { data: 'status' , 'defaultContent':'', className: 'text-center'},
        { data: 'action', orderable: false, searchable: false, className: 'text-center'}
    ];

    initializeDataTable(ajaxUrl, columnsConfig);
})

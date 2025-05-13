$(document).ready(function () {
	var ajaxUrl = "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}";
    var columnsConfig = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false, orderable: false, className: 'text-center' },
        { data: 'name', name: 'name' },
        { data: 'email', name: 'email' },
        { data: 'level.name', name: 'level_id', defaultContent: '' },
        { data: 'access_group.name', name: 'access_group_id', defaultContent: '' },
        { data: 'email_verified_at', name: 'email_verified_at',  className: 'text-center'},
        { data: 'action', orderable: false, searchable: false , className: 'text-center'}
    ];

    initializeDataTable(ajaxUrl,  columnsConfig);
})

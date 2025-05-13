$(document).ready(function () {
	var ajaxUrl = "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}";
    var columnsConfig = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false, orderable: false, className: 'text-center' },
        { data: 'title', name: 'title',orderable: false, },
        { data: 'menu.title', name: 'menu.title',orderable: false, },
        { data: 'action', orderable: false, searchable: false , className: 'text-center',orderable: false}
    ];

    initializeDataTable(ajaxUrl,  columnsConfig);
})

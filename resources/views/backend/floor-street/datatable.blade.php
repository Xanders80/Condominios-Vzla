$(document).ready(function () {
	var ajaxUrl = "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}";
    var columnsConfig = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false, orderable: false, className: 'text-center' },
        { data: 'name' , 'defaultContent':''},
        { data: 'tower_sector_id' , 'defaultContent':''},
        { data: 'action', orderable: false, searchable: false , className: 'text-center'}
    ];

    initializeDataTable(ajaxUrl,  columnsConfig);
})

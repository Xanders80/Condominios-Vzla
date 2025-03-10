$(document).ready(function () {
	var ajaxUrl = "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}";
    var columnsConfig = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false, orderable: false, className: 'text-center' },
        { data: 'name', name: 'name' },
        { data: 'email' , 'defaultContent':''},
        { data: 'cell_phone_number' , 'defaultContent':''},
        { data: 'dweller_type_id' , 'defaultContent':''},
        { data: 'action', orderable: false, searchable: false , className: 'text-center'}
    ];

    initializeDataTable(ajaxUrl,  columnsConfig);
})

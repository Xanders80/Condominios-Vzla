$(document).ready(function () {
	var ajaxUrl = "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}";
    var columnsConfig = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false, orderable: false, className: 'text-center' },
        { data: 'name' , 'defaultContent':''},
        { data: 'unit_type_id' , 'defaultContent':''},
        { data: 'dweller_name', 'defaultContent':''},
        { data: 'tower_sector_id' , 'defaultContent':''},
        { data: 'floor_street_id' , 'defaultContent':''},
        { data: 'status' , 'defaultContent':'', className: 'text-center'},
        { data: 'action', orderable: false, searchable: false, className: 'text-center'}
    ];

    initializeDataTable(ajaxUrl,  columnsConfig);
})

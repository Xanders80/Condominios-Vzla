$(document).ready(function () {
	var ajaxUrl = "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}";
    var columnsConfig = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false, className: 'text-center' },
        { data: 'title' , 'defaultContent':''},
        { data: 'visitors' , 'defaultContent':'', orderable: false, className: 'text-center'},
        { data: 'like' , 'defaultContent':'', orderable: false, className: 'text-center'},
        { data: 'dislike' , 'defaultContent':'', orderable: false, className: 'text-center'},
        { data: 'publish' , 'defaultContent':'', orderable: false, className: 'text-center'},
        { data: 'action', orderable: false, searchable: false, className: 'text-center'}
    ];

    initializeDataTable(ajaxUrl,  columnsConfig);
})

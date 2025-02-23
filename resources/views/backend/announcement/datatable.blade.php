$(document).ready(function () {
    var ajaxUrl = "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}";
    var columnsConfig = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false, orderable: false, className: 'text-center' },
        { data: 'menu.title' , 'defaultContent':''},
        { data: 'title' , 'defaultContent':''},
        { data: 'start' , 'defaultContent':''},
        { data: 'end' , 'defaultContent':''},
        { data: 'urgency' , 'defaultContent':''},
        { data: 'publish' , 'defaultContent':'',className: 'text-center'},
        { data: 'action', orderable: false, searchable: false , className: 'text-center'}
    ];

    initializeDataTable(ajaxUrl,  columnsConfig);
})

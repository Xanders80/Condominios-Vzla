$(document).ready(function () {
	var ajaxUrl = "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}";
    var columnsConfig = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
        { data: 'unit_id', 'defaultContent':''},
        { data: 'month', 'render': function(data, type, row) {
            return data + '/' + row.year;
        }},
        { data: 'amount_bs', 'defaultContent':''},
        { data: 'amount_usd', 'defaultContent':''},
        { data: 'status', 'defaultContent':'', className: 'text-center'},
        { data: 'action', orderable: false, searchable: false, className: 'text-center'}
    ];

    initializeDataTable(ajaxUrl, columnsConfig);
})

$(document).ready(function () {
var ajaxUrl = "{{ url(config('master.app.url.backend') . '/' . $url . '/data') }}";
var columnsConfig = [
{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
{ data: 'unit_id', 'defaultContent':''},
{ data: 'common_area_id', 'defaultContent':''},
{ data: 'start_time', 'defaultContent':''},
{ data: 'end_time', 'defaultContent':''},
{ data: 'total_amount', 'defaultContent':''},
{ data: 'status' , 'defaultContent':'', className: 'text-center'},
{ data: 'action', orderable: false, searchable: false, className: 'text-center'}
];

initializeDataTable(ajaxUrl, columnsConfig);
})
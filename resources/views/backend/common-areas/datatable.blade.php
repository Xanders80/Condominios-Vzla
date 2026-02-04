$(document).ready(function () {
var ajaxUrl = "{{ url(config('master.app.url.backend') . '/' . $url . '/data') }}";
var columnsConfig = [
{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
{ data: 'name', 'defaultContent':''},
{ data: 'max_occupancy', 'defaultContent':'0', className: 'text-center'},
{ data: 'booking_fee', 'defaultContent':''},
{ data: 'pricing_type', 'defaultContent':'', className: 'text-center'},
{ data: 'is_active', 'render': function(data) {
return data ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
}, className: 'text-center'},
{ data: 'action', orderable: false, searchable: false, className: 'text-center'}
];

initializeDataTable(ajaxUrl, columnsConfig);
})
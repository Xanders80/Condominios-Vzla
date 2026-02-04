$(document).ready(function () {
var ajaxUrl = "{{ url(config('master.app.url.backend') . '/' . $url . '/data') }}";
var columnsConfig = [
{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
{ data: 'condominium_id', name: 'condominium_id' },
{ data: 'period', name: 'period' },
{ data: 'total_amount', name: 'total_amount' },
{ data: 'status', name: 'status', className: 'text-center' },
{ data: 'action', orderable: false, searchable: false, className: 'text-center' }
];

initializeDataTable(ajaxUrl, columnsConfig);
});
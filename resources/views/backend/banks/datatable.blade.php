$(document).ready(function () {
var ajaxUrl = "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}";
    var columnsConfig = [
          { data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false, orderable: false, className: 'text-center' },
            { data: 'code_sudebank' , 'defaultContent':'', className: 'text-center'},
			{ data: 'name_ibp' , 'defaultContent':''},
			{ data: 'rif' , 'defaultContent':''},
			{ data: 'website' , 'defaultContent':''},
			{ data: 'active' , 'defaultContent':'', className: 'text-center'},
			{ data: 'action', orderable: false, searchable: false , className: 'text-center'}
      ];

    initializeDataTable(ajaxUrl,  columnsConfig);
})

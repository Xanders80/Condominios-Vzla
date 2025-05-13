<div class="row col-md-12">
    <label class="control-label h6">{{ trans('Are You Sure You Want to Delete This Data?') }}</label>
    <div class="info-data">
        <div class="panel panel-dark bg-dark" style="border-radius: 10px;">
            <div class="panel-body">
                {{ $slot }}
                <div class="m-2">
                    <span class="text-danger">{{ trans('Attention!') }}</span>
                    <span class="text-info">{{ trans('Deleted data cannot be recovered') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <span class="message"></span>
    </div>
</div>
{!! html()->hidden('table-id', 'datatable')->id('table-id') !!}
{!! html()->form()->close() !!}
<script>
    $('.modal-title').html(
        '<i class="mdi mdi-delete-forever mdi-24px text-danger"></i> - {{ trans('Delete Data') }} {{ $page->title }}'
    );
    $('.submit-data').html('<i class="mdi mdi-delete "></i> {{ trans('Delete') }} ');
</script>

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body text-center">
        <i class="mdi mdi-alert-circle-outline mdi-48px text-danger"></i>
        <h4>{{ trans('Are you sure you want to delete this record?') }}</h4>
        <div class="m-2">
            <strong class="text-danger">{{ trans('Attention!') }}</strong>
            <span class="text-info">{{ trans('Deleted data cannot be recovered') }}</span>
        </div>
        <div class="alert alert-info" style="border-radius: 8px;">
            {{ $slot }}
        </div>
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
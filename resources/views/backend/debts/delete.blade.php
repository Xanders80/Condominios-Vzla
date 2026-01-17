{{ html()->form('DELETE', route($page->url . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() }}

<div class="row">
    <div class="col-md-12 text-center">
        <p class="text-danger"><i class="mdi mdi-alert mdi-48px"></i></p>
        <p>{{ trans('Are you sure you want to delete this debt?') }}</p>
        <p><strong>{{ __('Unit') }}:</strong> {{ $data->unit->name }}</p>
        <p><strong>{{ __('Amount') }}:</strong> {{ number_format($data->amount, 2, ',', '.') }} Bs</p>
        <div class="alert alert-warning">
            {{ trans('This action will also delete historical interest calculations associated with this debt.') }}
        </div>
    </div>
</div>

{!! html()->hidden('table-id', 'datatable')->id('table-id') !!}
{!! html()->form()->close() !!}

<script>
    $(document).ready(function() {
        $('.modal-title').html('<i class="mdi mdi-delete mdi-24px text-danger"></i> - {{ trans('Delete Debt') }}');
        $('.submit-data').html('<i class="mdi mdi-delete "></i> {{ trans('Delete') }} ');
    });
</script>

{{ html()->form('DELETE', route($page->url . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() }}

<div class="row text-center">
    <div class="col-md-12">
        <p class="text-danger"><i class="mdi mdi-delete-forever mdi-48px"></i></p>
        <p>{{ trans('Are you sure you want to delete this supplier?') }}</p>
        <p><strong>{{ __('Name') }}:</strong> {{ $data->name }}</p>
        <p><strong>{{ __('RIF') }}:</strong> {{ $data->tax_id }}</p>
    </div>
</div>

{!! html()->hidden('table-id', 'datatable')->id('table-id') !!}
{!! html()->form()->close() !!}

<script>
    $(document).ready(function() {
        $('.modal-title').html('<i class="mdi mdi-delete mdi-24px text-danger"></i> - {{ trans('Delete Supplier') }}');
        $('.submit-data').html('<i class="mdi mdi-delete "></i> {{ trans('Delete') }} ');
    });
</script>

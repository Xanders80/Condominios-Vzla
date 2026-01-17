{{ html()->form('DELETE', route($page->url . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() }}

<div class="row">
    <div class="col-md-12">
        <p>{{ trans('Are you sure you want to delete this receipt?') }}</p>
        <p><strong>{{ __('Unit') }}:</strong> {{ $data->unit->name }}</p>
        <p><strong>{{ __('Month/Year') }}:</strong> {{ $data->month }}/{{ $data->year }}</p>
        <p><strong>{{ __('Amount') }}:</strong> {{ number_format($data->amount_bs, 2, ',', '.') }} Bs / $ {{ number_format($data->amount_usd, 2) }}</p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <span class="message"></span>
    </div>
</div>

{!! html()->hidden('table-id', 'datatable')->id('table-id') !!}
{!! html()->form()->close() !!}

<script>
    $(document).ready(function() {
        $('.modal-title').html('<i class="mdi mdi-delete mdi-24px text-danger"></i> - {{ trans('Delete Receipt') }}');
        $('.submit-data').html('<i class="mdi mdi-delete "></i> {{ trans('Delete') }} ');
    });
</script>

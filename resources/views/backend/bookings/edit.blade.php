{{ html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-edit-' . $page->code)->class('form form-horizontal')->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class='form-group col-4'>
                <x-input-text name="booking_date" label="{{ trans('Date') }}" id="booking_date" isRequired=true
                    type="date" />
            </div>
            <div class="row">
                <div class='form-group col-6'>
                    <x-input-text name="total_amount" label="{{ trans('Total Amount') }}" id="total_amount"
                        isRequired=true type="number" step="0.01" />
                </div>
                <div class='form-group col-3'>
                    <x-input-text name="currency" label="{{ trans('Currency') }}" id="currency" readonly />
                </div>
                <div class='form-group col-3'>
                    <x-input-text name="exchange_rate" label="{{ trans('Exch. Rate') }}" id="exchange_rate" readonly />
                </div>
            </div>
            <div class='form-group col-4'>
                <x-input-select id="status" label="{{ trans('Status') }}" isRequired=true>
                    {!! html()->select('status', [
    'pending' => trans('Pending'),
    'confirmed' => trans('Confirmed'),
    'cancelled' => trans('Cancelled'),
    'completed' => trans('Completed')
])->class('form-control select2')->id('status') !!}
                </x-input-select>
            </div>
        </div>
    </div>
</div>

{!! html()->hidden('table-id', 'datatable')->id('table-id') !!}
{!! html()->form()->close() !!}

<script>
    $(document).ready(function () {
        $('.modal-title').html('<i class="mdi mdi-pencil mdi-24px text-warning"></i> - {{ trans('Edit Booking') }}');
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Update') }} ');
        $('.select2').select2().parent().css('z-index', 9999);
    });
</script>
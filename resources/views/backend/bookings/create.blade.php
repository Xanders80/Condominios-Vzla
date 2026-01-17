{{ html()->form('POST', route($page->url . '.store'))->id('form-create-' . $page->code)->class('form form-horizontal')->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class='form-group col-6'>
                <x-input-select id="unit_id" label="{{ trans('Unit') }}" isRequired=true>
                    {!! html()->select('unit_id', $units)->placeholder(trans('Choose Unit'))->class('form-control select2')->id('unit_id') !!}
                </x-input-select>
            </div>
            <div class='form-group col-6'>
                <x-input-select id="common_area_id" label="{{ trans('Common Area') }}" isRequired=true>
                    {!! html()->select('common_area_id', $areas)->placeholder(trans('Choose Area'))->class('form-control select2')->id('common_area_id') !!}
                </x-input-select>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-4'>
                <x-input-text name="booking_date" label="{{ trans('Date') }}" id="booking_date" isRequired=true type="date" />
            </div>
            <div class='form-group col-4'>
                <x-input-text name="start_time" label="{{ trans('Start Time') }}" id="start_time" isRequired=true type="time" />
            </div>
            <div class='form-group col-4'>
                <x-input-text name="end_time" label="{{ trans('End Time') }}" id="end_time" isRequired=true type="time" />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-6'>
                <x-input-text name="amount_paid" label="{{ trans('Amount Paid ($)') }}" id="amount_paid" isRequired=true type="number" step="0.01" />
            </div>
            <div class='form-group col-6'>
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
    $(document).ready(function() {
        $('.modal-title').html('<i class="mdi mdi-calendar-check mdi-24px text-success"></i> - {{ trans('Add Booking') }}');
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');
        $('.select2').select2().parent().css('z-index', 9999);
    });
</script>

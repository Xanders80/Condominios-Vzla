{{ html()->form('POST', route($page->url . '.store'))->id('form-create-' . $page->code)->class('form form-horizontal')->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class='form-group col-6'>
                <x-input-select id="condominiums_id" label="{{ trans('Condominium') }}" isRequired=true>
                    {!! html()->select('condominiums_id', $condominiums)->placeholder(trans('Choose Condominium'))->class('form-control select2')->id('condominiums_id') !!}
                </x-input-select>
            </div>
            <div class='form-group col-6'>
                <x-input-text name="name" label="{{ trans('Name') }}" plHolder="{{ trans('Pool, Gym, Ballroom...') }}"
                    id="name" isRequired=true />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-4'>
                <x-input-text name="max_occupancy" label="{{ trans('Max Occupancy') }}" plHolder="0" id="max_occupancy"
                    isRequired=true type="number" />
            </div>
            <div class='form-group col-8'>
                <x-input-text name="description" label="{{ trans('Description') }}"
                    plHolder="{{ trans('Details about the area...') }}" id="description" />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-4'>
                <x-input-text name="booking_fee" label="{{ trans('Booking Fee') }}" plHolder="0.00" id="booking_fee"
                    isRequired=true type="number" step="0.01" />
            </div>
            <div class='form-group col-4'>
                <x-input-select id="pricing_type" label="{{ trans('Pricing Type') }}" isRequired=true>
                    {!! html()->select('pricing_type', [
    'fixed' => trans('Fixed (Per Event)'),
    'hourly' => trans('Hourly')
])->class('form-control select2')->id('pricing_type') !!}
                </x-input-select>
            </div>
            <div class='form-group col-4'>
                <x-input-select id="currency" label="{{ trans('Currency') }}" isRequired=true>
                    {!! html()->select('currency', [
    'USD' => 'USD ($)',
    'BS' => 'BS (Bs.)'
])->class('form-control select2')->id('currency') !!}
                </x-input-select>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-4'>
                <x-input-text name="min_anticipation_hours" label="{{ trans('Min. Anticipation (Hours)') }}"
                    plHolder="24" id="min_anticipation_hours" isRequired=true type="number" />
            </div>
            <div class='form-group col-4'>
                <x-input-text name="max_booking_hours" label="{{ trans('Max. Duration (Hours)') }}" plHolder="0"
                    id="max_booking_hours" type="number" />
            </div>
            <div class='form-group col-4'>
                <x-input-text name="cancellation_penalty_percentage" label="{{ trans('Cancel. Penalty (%)') }}"
                    plHolder="0" id="cancellation_penalty_percentage" type="number" step="0.01" />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-6 mt-4'>
                <x-input-checkbox id="is_active" name="is_active" label="{{ trans('Is Active') }}" class="checkbox"
                    valor="1" checked />
            </div>
        </div>
    </div>
</div>

{!! html()->hidden('table-id', 'datatable')->id('table-id') !!}
{!! html()->form()->close() !!}

<script>
    $(document).ready(function () {
        $('.modal-title').html('<i class="mdi mdi-tree mdi-24px text-success"></i> - {{ trans('Add Common Area') }}');
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');
        $('.select2').select2().parent().css('z-index', 9999);
    });
</script>
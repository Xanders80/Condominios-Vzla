{{ html()->form('POST', route($page->url . '.store'))->id('form-create-' . $page->code)->class('form form-horizontal')->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class='form-group col-6'>
                <x-input-text name="name" label="{{ trans('Name') }}" plHolder="{{ trans('Pool, Gym, Ballroom...') }}" id="name" isRequired=true />
            </div>
            <div class='form-group col-6'>
                <x-input-text name="capacity" label="{{ trans('Capacity (Persons)') }}" plHolder="0" id="capacity" isRequired=true type="number" />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <x-input-text name="description" label="{{ trans('Description') }}" plHolder="{{ trans('Details about the area...') }}" id="description" />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-4'>
                <x-input-text name="booking_fee" label="{{ trans('Booking Fee ($)') }}" plHolder="0.00" id="booking_fee" isRequired=true type="number" step="0.01" />
            </div>
            <div class='form-group col-4 mt-4'>
                <x-input-checkbox id="is_bookable" name="is_bookable" label="{{ trans('Is Bookable') }}" class="checkbox" valor="1" checked />
            </div>
            <div class='form-group col-4'>
                <x-input-select id="status" label="{{ trans('Status') }}" isRequired=true>
                    {!! html()->select('status', [
                        'active' => trans('Active'),
                        'under_maintenance' => trans('Under Maintenance'),
                        'closed' => trans('Closed')
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
        $('.modal-title').html('<i class="mdi mdi-tree mdi-24px text-success"></i> - {{ trans('Add Common Area') }}');
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');
        $('.select2').select2().parent().css('z-index', 9999);
    });
</script>

{{ html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-edit-' . $page->code)->class('form form-horizontal')->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class='form-group col-4'>
                <x-input-text name="percentage" label="{{ trans('Percentage (%)') }}" id="percentage" isRequired=true type="number" step="0.01" />
            </div>
            <div class='form-group col-4'>
                <x-input-text name="start_date" label="{{ trans('Start Date') }}" id="start_date" isRequired=true type="date" />
            </div>
            <div class='form-group col-4'>
                <x-input-text name="end_date" label="{{ trans('End Date') }}" id="end_date" type="date" />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-4 mt-4'>
                <x-input-checkbox id="is_active" name="is_active" label="{{ trans('Is Active') }}" class="checkbox" valor="1" />
            </div>
        </div>
    </div>
</div>

{!! html()->hidden('table-id', 'datatable')->id('table-id') !!}
{!! html()->form()->close() !!}

<script>
    $(document).ready(function() {
        $('.modal-title').html('<i class="mdi mdi-percent mdi-24px text-warning"></i> - {{ trans('Edit Interest Rate') }}');
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Update') }} ');
    });
</script>

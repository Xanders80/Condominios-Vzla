{{ html()->form('POST', route($page->url . '.store'))->id('form-create-' . $page->code)->class('form form-horizontal')->acceptsFiles()->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class='form-group col-6'>
                <x-input-select id="supplier_id" label="{{ trans('Supplier') }}" isRequired=true>
                    {!! html()->select('supplier_id', $suppliers)->placeholder(trans('Choose Supplier'))->class('form-control select2')->id('supplier_id') !!}
                </x-input-select>
            </div>
            <div class='form-group col-6'>
                <x-input-select id="incident_report_id" label="{{ trans('Incident (Optional)') }}">
                    {!! html()->select('incident_report_id', $incidents)->placeholder(trans('None / General Maintenance'))->class('form-control select2')->id('incident_report_id') !!}
                </x-input-select>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <x-input-text name="title" label="{{ trans('Task Title') }}" plHolder="{{ trans('Paint lobby, Fix pump #2...') }}" id="title" isRequired=true />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <x-input-text name="description" label="{{ trans('Detailed Instructions') }}" plHolder="{{ trans('Step by step guide for the supplier...') }}" id="description" isRequired=true />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-4'>
                <x-input-text name="estimated_cost" label="{{ trans('Est. Cost ($)') }}" plHolder="0.00" id="estimated_cost" isRequired=true type="number" step="0.01" />
            </div>
            <div class='form-group col-4'>
                <x-input-text name="scheduled_date" label="{{ trans('Scheduled Date') }}" id="scheduled_date" type="date" />
            </div>
            <div class='form-group col-4'>
                <x-input-select id="status" label="{{ trans('Status') }}" isRequired=true>
                    {!! html()->select('status', [
                        'draft' => trans('Draft'),
                        'assigned' => trans('Assigned'),
                        'in_progress' => trans('In Progress'),
                        'on_hold' => trans('On Hold')
                    ])->class('form-control select2')->id('status') !!}
                </x-input-select>
            </div>
        <div class="row">
            <div class='form-group col-12'>
                <label>{{ trans('Attachments') }}</label>
                {!! html()->file('attachments[]')->class('form-control')->multiple() !!}
                <small class="text-muted">{{ trans('You can upload multiple images or documents.') }}</small>
            </div>
        </div>
    </div>
</div>

{!! html()->hidden('table-id', 'datatable')->id('table-id') !!}
{!! html()->form()->close() !!}

<script>
    $(document).ready(function() {
        $('.modal-title').html('<i class="mdi mdi-tasks mdi-24px text-success"></i> - {{ trans('Add Work Order') }}');
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');
        $('.select2').select2().parent().css('z-index', 9999);
    });
</script>

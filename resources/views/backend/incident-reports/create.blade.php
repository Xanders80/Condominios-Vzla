{{ html()->form('POST', route($page->url . '.store'))->id('form-create-' . $page->code)->class('form form-horizontal')->acceptsFiles()->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class='form-group col-6'>
                <x-input-select id="unit_id" label="{{ trans('Unit') }}" isRequired=true>
                    {!! html()->select('unit_id', $units)->placeholder(trans('Choose Unit'))->class('form-control select2')->id('unit_id') !!}
                </x-input-select>
            </div>
            <div class='form-group col-6'>
                <x-input-text name="title" label="{{ trans('Incident Title') }}" plHolder="{{ trans('Broken elevator, water leak...') }}" id="title" isRequired=true />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <x-input-text name="description" label="{{ trans('Description') }}" plHolder="{{ trans('Details about the incident...') }}" id="description" isRequired=true />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-6'>
                <x-input-text name="location" label="{{ trans('Specific Location') }}" plHolder="{{ trans('Hallway B, Floor 4...') }}" id="location" />
            </div>
            <div class='form-group col-3'>
                <x-input-select id="priority" label="{{ trans('Priority') }}" isRequired=true>
                    {!! html()->select('priority', [
                        'low' => trans('Low'),
                        'medium' => trans('Medium'),
                        'high' => trans('High'),
                        'critical' => trans('Critical')
                    ])->class('form-control select2')->id('priority') !!}
                </x-input-select>
            </div>
            <div class='form-group col-3'>
                <x-input-select id="status" label="{{ trans('Status') }}" isRequired=true>
                    {!! html()->select('status', [
                        'open' => trans('Open'),
                        'in_progress' => trans('In Progress'),
                        'resolved' => trans('Resolved'),
                        'closed' => trans('Closed')
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
        $('.modal-title').html('<i class="mdi mdi-exclamation-triangle mdi-24px text-danger"></i> - {{ trans('Add Incident Report') }}');
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');
        $('.select2').select2().parent().css('z-index', 9999);
    });
</script>

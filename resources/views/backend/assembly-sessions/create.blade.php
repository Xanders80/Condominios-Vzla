{{ html()->form('POST', route($page->url . '.store'))->id('form-create-' . $page->code)->class('form form-horizontal')->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class='form-group col-6'>
                <x-input-select id="session_type" label="{{ trans('Session Type') }}" isRequired=true>
                    {!! html()->select('session_type', ['ordinary' => trans('Ordinary'), 'extraordinary' => trans('Extraordinary')])->placeholder(trans('Choose Type'))->class('form-control select2')->id('session_type') !!}
                </x-input-select>
            </div>
            <div class='form-group col-6'>
                <x-input-text name="session_date" label="{{ trans('Date & Time') }}" id="session_date" isRequired=true type="datetime-local" />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-8'>
                <x-input-text name="location" label="{{ trans('Location') }}" plHolder="{{ trans('Community Room, Parking Lot...') }}" id="location" isRequired=true />
            </div>
            <div class='form-group col-4'>
                <x-input-text name="quorum_percentage" label="{{ trans('Min. Quorum (%)') }}" plHolder="50" id="quorum_percentage" isRequired=true type="number" step="0.1" valor="50" />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-4'>
                <x-input-select id="status" label="{{ trans('Status') }}" isRequired=true>
                    {!! html()->select('status', [
                        'scheduled' => trans('Scheduled'),
                        'in_progress' => trans('In Progress'),
                        'completed' => trans('Completed'),
                        'cancelled' => trans('Cancelled')
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
        $('.modal-title').html('<i class="mdi mdi-users mdi-24px text-success"></i> - {{ trans('Add Assembly') }}');
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');
        $('.select2').select2().parent().css('z-index', 9999);
    });
</script>

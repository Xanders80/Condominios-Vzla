{{ html()->form('POST', route($page->url . '.store'))->id('form-create-' . $page->code)->class('form form-horizontal')->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class='form-group col-6'>
                <x-input-select id="assembly_session_id" label="{{ trans('Assembly Session') }}" isRequired=true>
                    {!! html()->select('assembly_session_id', $sessions)->placeholder(trans('Choose Session'))->class('form-control select2')->id('assembly_session_id') !!}
                </x-input-select>
            </div>
            <div class='form-group col-6'>
                <x-input-text name="title" label="{{ trans('Motion Title') }}" plHolder="{{ trans('Renovate lobby, Change administrator...') }}" id="title" isRequired=true />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <x-input-text name="description" label="{{ trans('Detailed Description') }}" plHolder="{{ trans('Full text of the proposal...') }}" id="description" isRequired=true />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-6'>
                <x-input-select id="voting_type" label="{{ trans('Voting Type') }}" isRequired=true>
                    {!! html()->select('voting_type', [
                        'public' => trans('Public (Show of hands)'),
                        'secret' => trans('Secret (Closed ballot)')
                    ])->class('form-control select2')->id('voting_type') !!}
                </x-input-select>
            </div>
            <div class='form-group col-6'>
                <x-input-select id="status" label="{{ trans('Status') }}" isRequired=true>
                    {!! html()->select('status', [
                        'proposed' => trans('Proposed'),
                        'open' => trans('Open for Voting'),
                        'closed' => trans('Voting Closed'),
                        'approved' => trans('Approved'),
                        'rejected' => trans('Rejected')
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
        $('.modal-title').html('<i class="mdi mdi-vote mdi-24px text-success"></i> - {{ trans('Add Motion') }}');
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');
        $('.select2').select2().parent().css('z-index', 9999);
    });
</script>

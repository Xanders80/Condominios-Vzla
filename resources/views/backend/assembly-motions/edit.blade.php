{{ html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-edit-' . $page->code)->class('form form-horizontal')->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
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
        <div class="row">
            <div class='form-group col-12'>
                <label>{{ trans('Result Summary') }}</label>
                {!! html()->textarea('result_summary')->class('form-control')->rows(3)->placeholder(trans('Summary of votes and final decision...')) !!}
            </div>
        </div>
    </div>
</div>

{!! html()->hidden('table-id', 'datatable')->id('table-id') !!}
{!! html()->form()->close() !!}

<script>
    $(document).ready(function() {
        $('.modal-title').html('<i class="mdi mdi-pencil mdi-24px text-warning"></i> - {{ trans('Update Motion Status') }}');
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Update') }} ');
        $('.select2').select2().parent().css('z-index', 9999);
    });
</script>

{{ html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-edit-' . $page->code)->class('form form-horizontal')->acceptsFiles()->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class='form-group col-6'>
                <x-input-select id="priority" label="{{ trans('Priority') }}" isRequired=true>
                    {!! html()->select('priority', [
                        'low' => trans('Low'),
                        'medium' => trans('Medium'),
                        'high' => trans('High'),
                        'critical' => trans('Critical')
                    ])->class('form-control select2')->id('priority') !!}
                </x-input-select>
            </div>
            <div class='form-group col-6'>
                <x-input-select id="status" label="{{ trans('Status') }}" isRequired=true>
                    {!! html()->select('status', [
                        'open' => trans('Open'),
                        'in_progress' => trans('In Progress'),
                        'resolved' => trans('Resolved'),
                        'closed' => trans('Closed')
                    ])->class('form-control select2')->id('status') !!}
                </x-input-select>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <p><strong>{{ trans('Existing Attachments') }}:</strong></p>
                @if($data->attachments->count() > 0)
                    <div class="list-group mb-3">
                        @foreach($data->attachments as $attachment)
                            <a href="{{ $attachment->url }}" target="_blank" class="list-group-item list-group-item-action">
                                <i class="mdi mdi-file"></i> {{ $attachment->file_name }}
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">{{ trans('No attachments yet.') }}</p>
                @endif
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <label>{{ trans('Add New Attachments') }}</label>
                {!! html()->file('attachments[]')->class('form-control')->multiple() !!}
            </div>
        </div>
    </div>
</div>

{!! html()->hidden('table-id', 'datatable')->id('table-id') !!}
{!! html()->form()->close() !!}

<script>
    $(document).ready(function() {
        $('.modal-title').html('<i class="mdi mdi-pencil mdi-24px text-warning"></i> - {{ trans('Update Incident Status') }}');
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Update') }} ');
        $('.select2').select2().parent().css('z-index', 9999);
    });
</script>

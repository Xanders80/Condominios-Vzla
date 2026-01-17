{{ html()->form('DELETE', route($page->url . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() }}

<div class="row text-center">
    <div class="col-md-12">
        <p class="text-danger"><i class="mdi mdi-delete-alert mdi-48px"></i></p>
        <p>{{ trans('Are you sure you want to delete this assembly session?') }}</p>
        <p><strong>{{ __('Type') }}:</strong> {{ ucfirst($data->session_type) }}</p>
        <p><strong>{{ __('Date') }}:</strong> {{ $data->session_date }}</p>
        <div class="alert alert-danger">
            {{ trans('This will also delete all motions and votes associated with this session.') }}
        </div>
    </div>
</div>

{!! html()->hidden('table-id', 'datatable')->id('table-id') !!}
{!! html()->form()->close() !!}

<script>
    $(document).ready(function() {
        $('.modal-title').html('<i class="mdi mdi-delete mdi-24px text-danger"></i> - {{ trans('Delete Assembly') }}');
        $('.submit-data').html('<i class="mdi mdi-delete "></i> {{ trans('Delete') }} ');
    });
</script>

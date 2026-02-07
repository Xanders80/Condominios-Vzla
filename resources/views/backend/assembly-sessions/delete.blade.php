{{ html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() }}
<x-body-delete>
    <strong>{{ trans('Type') }}:</strong> {{ $data->session_type }}<br>
    <strong>{{ trans('Date') }}:</strong> {{ $data->session_date }}
</x-body-delete>

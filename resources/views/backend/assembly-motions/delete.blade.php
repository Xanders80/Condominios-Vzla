{{ html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() }}
<x-body-delete>
    <strong>{{ trans('Title') }}:</strong> {{ $data->title }}<br>
    <strong>{{ trans('Session Date') }}:</strong> {{ $data->assemblySession->session_date }}
</x-body-delete>

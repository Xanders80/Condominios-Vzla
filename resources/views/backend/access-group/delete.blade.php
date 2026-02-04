{!! html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() !!}
<x-body-delete>
    <strong>{{ trans('Name') }}:</strong> {{ $data->name }}<br>
    <strong>{{ trans('Code | Alias') }}:</strong> {{ $data->code }}
</x-body-delete>
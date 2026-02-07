{!! html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() !!}
<x-body-delete>
    <strong>{{ trans('Name') }}:</strong> {{ $data->name }}
</x-body-delete>

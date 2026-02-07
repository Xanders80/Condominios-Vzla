{{ html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() }}
<x-body-delete>
    <strong>{{ trans('Title') }}:</strong> {{ $data->title }}<br>
    <strong>{{ trans('Reported by Unit') }}:</strong> {{ $data->unit->name }}
</x-body-delete>

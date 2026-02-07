{!! html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() !!}
<x-body-delete>
    <strong>{{ trans('Title / Group Name / FAQ Name') }}:</strong> {{ $data->title }}
</x-body-delete>

{!! html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-create-' . $page->code)->class('form form-horizontal')->open() !!}
<x-body-delete>
    <strong>{{ trans('Name') }}:</strong> {{ $data->name }} <br>
    <strong>{{ trans('Condominiums') }}:</strong> {{ $data->condominiums->name }}
</x-body-delete>

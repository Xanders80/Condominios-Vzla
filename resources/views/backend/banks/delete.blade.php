{!! html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() !!}
<x-body-delete>
    <strong>{{ trans('Code Sudeban') }}:</strong> {{ $data->code_sudebank }}<br>
    <strong>{{ trans('Name') }}:</strong> {{ $data->name_ibp }}
</x-body-delete>

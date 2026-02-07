{!! html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() !!}
<x-body-delete>
    <strong>{{ trans('Condominiums') }}:</strong> {{ $data->condominiums->name }}<br>
    <strong>{{ trans('Account Number') }}:</strong> {{ $data->account_number }}<br>
    <strong>{{ trans('Banks') }}:</strong> {{ $data->banks->name }}
</x-body-delete>

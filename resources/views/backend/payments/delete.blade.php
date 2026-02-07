{!! html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-create-' . $page->code)->class('form form-horizontal')->open() !!}
<x-body-delete>
    <strong>{{ trans('Nro Confirmation') }}:</strong> {{ $data->nro_confirmation }} <br>
    <strong>{{ trans('Amount') }}:</strong> {{ $data->amount }}
</x-body-delete>

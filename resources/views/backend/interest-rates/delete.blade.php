{{ html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() }}
<x-body-delete>
    <strong>{{ trans('Percentage') }}:</strong> {{ $data->percentage }} %<br>
    <strong>{{ trans('Period') }}:</strong> {{ $data->start_date }} / {{ $data->end_date ?? __('Current') }}
</x-body-delete>

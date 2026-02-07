{{ html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() }}
<x-body-delete>
    <strong>{{ trans('Unit') }}:</strong> {{ $data->unit->name }}<br>
    <strong>{{ trans('Area') }}:</strong> {{ $data->commonArea->name }}<br>
    <strong>{{ trans('Date') }}:</strong> {{ $data->booking_date }}
</x-body-delete>

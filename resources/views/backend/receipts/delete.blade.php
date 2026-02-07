{{ html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() }}
<x-body-delete>
    <strong>{{ trans('Unit') }}:</strong> {{ $data->unit->name }}<br>
    <strong>{{ trans('Month/Year') }}:</strong> {{ $data->month }}/{{ $data->year }}<br>
    <strong>{{ trans('Amount') }}:</strong> {{ number_format($data->amount_bs, 2, ',', '.') }} Bs / $
    {{ number_format($data->amount_usd, 2) }}
</x-body-delete>

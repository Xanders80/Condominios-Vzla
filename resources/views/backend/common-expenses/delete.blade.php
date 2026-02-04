{{ html()->form('DELETE', route($page->url . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form-horizontal')->open() }}
<x-body-delete>
    <strong>{{ trans('Period') }}:</strong> {{ $data->period->format('m/Y') }}<br>
    <strong>{{ trans('Amount') }}:</strong> {{ number_format($data->total_amount, 2, ',', '.') }} Bs
</x-body-delete>
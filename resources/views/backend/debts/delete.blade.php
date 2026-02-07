{{ html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() }}
<x-body-delete>
    <strong>{{ trans('Unit') }}:</strong> {{ $data->unit->name }}<br>
    <strong>{{ trans('Amount') }}:</strong> {{ number_format($data->amount, 2, ',', '.') }} Bs<br>
    <div class="alert alert-warning">
        {{ trans('This action will also delete historical interest calculations associated with this debt.') }}
    </div>
</x-body-delete>

{{ html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-edit-' . $page->code)->acceptsFiles()->class('form form-horizontal')->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class='form-group col-4'>
                <x-input-text name="amount" label="{{ trans('Amount (Bs)') }}" id="amount" isRequired=true type="number" step="0.01" />
            </div>
            <div class='form-group col-4'>
                <x-input-text name="due_date" label="{{ trans('Due Date') }}" id="due_date" isRequired=true type="date" />
            </div>
            <div class='form-group col-4'>
                <x-input-text name="grace_period_days" label="{{ trans('Grace Period (Days)') }}" id="grace_period_days" isRequired=true type="number" />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-4'>
                <x-input-select id="status" label="{{ trans('Status') }}" isRequired=true>
                    {!! html()->select('status', [
                        'current' => trans('Current'),
                        'pre_delinquent' => trans('Pre-delinquent'),
                        'delinquent' => trans('Delinquent'),
                        'paid' => trans('Paid'),
                        'judicial' => trans('Judicial')
                    ])->class('form-control select2')->id('status') !!}
                </x-input-select>
            </div>
        </div>
    </div>
</div>

{!! html()->hidden('table-id', 'datatable')->id('table-id') !!}
{!! html()->form()->close() !!}

<script>
    $(document).ready(function() {
        $('.modal-title').html('<i class="mdi mdi-pencil mdi-24px text-warning"></i> - {{ trans('Edit Debt') }}');
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Update') }} ');
        $('.select2').select2().parent().css('z-index', 9999);
    });
</script>

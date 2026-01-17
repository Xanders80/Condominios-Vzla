{{ html()->form('POST', route($page->url . '.store'))->id('form-create-' . $page->code)->acceptsFiles()->class('form form form-horizontal')->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class='form-group col-6'>
                <x-input-select id="unit_id" label="{{ trans('Unit') }}" isRequired=true>
                    {!! html()->select('unit_id', $units)->placeholder(trans('Choose Unit'))->class('form-control select2')->id('unit_id') !!}
                </x-input-select>
            </div>
            <div class='form-group col-6'>
                <x-input-select id="receipt_id" label="{{ trans('Original Receipt') }}">
                    {!! html()->select('receipt_id', $receipts)->placeholder(trans('None / Historical'))->class('form-control select2')->id('receipt_id') !!}
                </x-input-select>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-4'>
                <x-input-text name="amount" label="{{ trans('Amount (Bs)') }}" plHolder="0.00" id="amount" isRequired=true type="number" step="0.01" />
            </div>
            <div class='form-group col-4'>
                <x-input-text name="due_date" label="{{ trans('Due Date') }}" id="due_date" isRequired=true type="date" />
            </div>
            <div class='form-group col-4'>
                <x-input-text name="grace_period_days" label="{{ trans('Grace Period (Days)') }}" id="grace_period_days" isRequired=true type="number" valor="0" />
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
        $('.modal-title').html('<i class="mdi mdi-alert mdi-24px text-danger"></i> - {{ trans('Add Debt') }}');
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');
        $('.select2').select2().parent().css('z-index', 9999);
    });
</script>

{{ html()->form('POST', route($page->url . '.store'))->id('form-create-' . $page->code)->acceptsFiles()->class('form form form-horizontal')->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class='form-group col-6'>
                <x-input-select id="unit_id" label="{{ trans('Unit') }}" isRequired=true>
                    {!! html()->select('unit_id', $units)->placeholder(trans('Choose Unit'))->class('form-control select2')->id('unit_id') !!}
                </x-input-select>
            </div>
            <div class='form-group col-3'>
                <x-input-text name="month" label="{{ trans('Month') }}" plHolder="1-12" id="month" isRequired=true type="number" />
            </div>
            <div class='form-group col-3'>
                <x-input-text name="year" label="{{ trans('Year') }}" plHolder="202X" id="year" isRequired=true type="number" />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-4'>
                <x-input-text name="amount_bs" label="{{ trans('Amount (Bs)') }}" plHolder="0.00" id="amount_bs" isRequired=true type="number" step="0.01" />
            </div>
            <div class='form-group col-4'>
                <x-input-text name="amount_usd" label="{{ trans('Amount (USD)') }}" plHolder="0.00" id="amount_usd" isRequired=true type="number" step="0.01" />
            </div>
            <div class='form-group col-4'>
                <x-input-select id="status" label="{{ trans('Status') }}" isRequired=true>
                    {!! html()->select('status', ['pending' => 'Pending', 'paid' => 'Paid', 'partial' => 'Partial', 'cancelled' => 'Cancelled'])->placeholder(trans('Choose Status'))->class('form-control select2')->id('status') !!}
                </x-input-select>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-6'>
                <x-input-text name="due_date" label="{{ trans('Due Date') }}" id="due_date" isRequired=true type="date" />
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <span class="message"></span>
        <div class="progress" style="display: none;">
            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                <div id="statustxt">{{ __('0%') }}</div>
            </div>
        </div>
    </div>
</div>

{!! html()->hidden('table-id', 'datatable')->id('table-id') !!}
{!! html()->form()->close() !!}

<style>
    .select2-container { z-index: 9999 !important; width: 100% !important; }
    .modal-lg { max-width: 1000px !important; }
</style>

<script>
    $(document).ready(function() {
        $('.modal-title').html('<i class="mdi mdi-file-plus mdi-24px text-success"></i> - {{ trans('Add Receipt') }}');
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');
        $('.select2').select2().parent().css('z-index', 9999);
    });
</script>

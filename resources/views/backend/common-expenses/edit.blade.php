{!! html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-create-' . $page->code)->acceptsFiles()->class('form form form-horizontal')->open() !!}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class='form-group col-12'>
                <x-input-select id="condominium_id" label="{{ trans('Condominium') }}" isRequired=true>
                    {!! html()->select('condominium_id', $condominiums, $data->condominium_id)->placeholder(trans('Choose Condominium'))->class('form-control select2')->id('condominium_id') !!}
                </x-input-select>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-6'>
                <x-input-text name="period" label="{{ trans('Period (MM-YYYY)') }}" plHolder="YYYY-MM" id="period"
                    dataUser="{{ $data->period->format('Y-m') }}" isRequired=true type="month" />
            </div>
            <div class='form-group col-6'>
                <x-input-text name="total_amount" label="{{ trans('Total Amount (Bs)') }}" plHolder="0.00"
                    dataUser="{{ $data->total_amount }}" id="total_amount" isRequired=true type="number" step="0.01" />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <x-input-area name="notes" label="{{ trans('Notes') }}" plHolder="{{ trans('Optional notes...') }}"
                    dataUser="{{ $data->notes }}" id="notes" />
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
    .select2-container {
        z-index: 9999 !important;
        width: 100% !important;
    }

    .modal-lg {
        max-width: 800px !important;
    }
</style>

<script>
    $(document).ready(function () {
        $('.modal-title').html('<i class="mdi mdi-cash-multiple mdi-24px text-warning"></i> - {{ trans('Edit Common Expense') }}');
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Update') }} ');
        $('.select2').select2().parent().css('z-index', 9999);
    });
</script>
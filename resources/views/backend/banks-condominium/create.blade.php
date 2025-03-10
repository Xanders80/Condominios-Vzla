{{ html()->form('POST', route($page->url . '.store'))->id('form-create-' . $page->code)->acceptsFiles()->class('form form-horizontal')->open() }}
<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class='form-group col-8'>
            <x-input-select icon="mdi mdi-city" id="condominiums_id" label="{{ trans('Condominiums') }}" isRequired=true>
                {!! html()->select('condominiums_id', $condominiums)->placeholder(trans('Choose Menu'))->class('form-control select2')->id('condominiums_id') !!}
            </x-input-select>
        </div>
        <div class="row"> {{-- - Torre Sector - --}}
            <div class='form-group col-8'>
                <x-input-select icon="mdi mdi-bank" id="banks_id" label="{{ trans('Bank') }}" isRequired=true>
                    {!! html()->select('banks_id', $banks)->placeholder(trans('Choose Menu'))->class('form-control select2')->id('banks_id') !!}
                </x-input-select>
            </div>

            <div class='form-group col-4'>
                <x-input-text name="account_number" label="{{ trans('Account Number') }}"
                    plHolder="{{ trans('Type here...') }}" icon="mdi mdi-wallet" id="account_number" />
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <span class="message"></span>
        <div class="progress" style="display: none;">
            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                <div id="statustxt">0%</div>
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
        max-width: 1000px !important;
    }
</style>
<script>
    $('.select2').select2();
    $('.modal-title').html(
        '<i class="mdi mdi-book-plus mdi-24px text-success"></i> - {{ trans('Add Data') }} {{ $page->title }}');
    $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Add') }} ');

    document.getElementById('account_number').addEventListener('input', function(e) {
        // Remover caracteres no numéricos
        let value = e.target.value.replace(/\D/g, '');

        // Formatear el valor
        let formattedValue = '';

        if (value.length > 0) {
            formattedValue += value.substring(0, 4); // 4 dígitos de la entidad
        }
        if (value.length > 4) {
            formattedValue += '-' + value.substring(4, 8); // 4 dígitos de la sucursal
        }
        if (value.length > 8) {
            formattedValue += '-' + value.substring(8, 10); // 2 dígitos de control
        }
        if (value.length > 10) {
            formattedValue += '-' + value.substring(10, 20); // 10 dígitos del número de cuenta
        }

        e.target.value = formattedValue;
    });
</script>

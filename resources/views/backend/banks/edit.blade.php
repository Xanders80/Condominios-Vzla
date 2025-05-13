{!! html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-update-' . $page->code)->acceptsFiles()->class('form form form-horizontal')->open() !!}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row"> {{-- - Código & Nombre & Activo- --}}
            <div class='form-group col-2'>
                <x-input-text name="code_sudebank" label="{{ trans('Code Sudeban') }}"
                    dataUser="{{ $data->code_sudebank }}" plHolder="{{ trans('Type here...') }}" icon="mdi mdi-barcode "
                    id="code_sudebank" isRequired=true autofocus />
            </div>

            <div class='form-group col-8'>
                <x-input-text name="name_ibp" label="{{ trans('Name') }}" dataUser="{{ $data->name_ibp }}"
                    plHolder="{{ trans('Type here...') }}" icon="mdi mdi-bank " id="name_ibp" isRequired=true
                    autofocus />
            </div>
            <div class='form-group col-2 mt-4'>
                <x-input-checkbox id="md_checkbox" dataUser="{{ $data->active }}" name="active"
                    label="{{ trans('Active') }}" class="checkbox" />
            </div>
        </div>
        <div class="row"> {{-- - Rif - --}}
            <div class='form-group col-3'>
                <x-input-text name="rif" label="{{ trans('Rif') }}" dataUser="{{ $data->rif }}"
                    plHolder="{{ trans('Type here...') }}" icon="mdi mdi-account-card-details " id="rif"
                    isRequired=true autofocus />
            </div>

            <div class='form-group col-9'> {{-- - Website - --}}
                <x-input-text name="website" label="{{ trans('Web Site') }}" dataUser="{{ $data->website }}"
                    plHolder="{{ trans('Type here...') }}" icon="mdi mdi-web " id="website" isRequired=true
                    autofocus />
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
{{-- {!! html()->hidden('function','loadMenu,sidebarMenu')->id('function') !!} --}}
{{-- {!! html()->hidden('redirect',url('/dashboard'))->id('redirect') !!} --}}
{!! html()->closeModelForm() !!}
<style>
    .modal-lg {
        max-width: 1000px !important;
    }
</style>
<script>
    $('.modal-title').html(
        '<i class="mdi mdi-tooltip-edit mdi-24px text-warning"></i> - {{ trans('Edit Data') }} {{ $page->title }}'
        );
    $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');

    document.getElementById('rif').addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^VJEPG0-9]/g, ''); // Eliminar caracteres no válidos

        // Formatear el valor
        if (value.length > 0) {
            // Agregar el primer carácter (V, J, E, P, G)
            let firstChar = value.charAt(0);
            if (!['V', 'J', 'E', 'P', 'G'].includes(firstChar)) {
                firstChar = ''; // Si no es válido, reiniciar
            }
            value = firstChar + value.slice(1);
        }

        // Agregar guiones y limitar la longitud
        if (value.length > 1) {
            value = value.slice(0, 1) + '-' + value.slice(1); // Agregar guion después del primer carácter
        }
        if (value.length > 11) {
            value = value.slice(0, 11); // Limitar a 10 caracteres
        }
        if (value.length > 9) {
            value = value.slice(0, 10) + '-' + value.slice(10); // Agregar guion antes del último carácter
        }

        e.target.value = value; // Actualizar el campo de entrada
    });
</script>

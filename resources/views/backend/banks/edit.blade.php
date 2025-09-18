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

    // Asigna el formateador de RIF al campo de entrada correspondiente.
    // La función formatRifInput se encuentra en public/js/app-helpers.js
    document.getElementById('rif').addEventListener('input', formatRifInput);
</script>

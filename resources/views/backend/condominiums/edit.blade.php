{{ html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-update-' . $page->code)->acceptsFiles()->class('form form form-horizontal')->open() }}
<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row"> {{-- - Nombre & Rif - --}}
            <div class='form-group col-6'>
                <x-input-text name="name" label="{{ trans('Name') }}" plHolder="{{ trans('Type here...') }}"
                    dataUser="{{ $data->name }}" icon="mdi mdi-city " id="name" isRequired=true autofocus />
            </div>
            <div class='form-group col-6'>
                <x-input-text name="rif" label="{{ trans('Rif') }}" plHolder="{{ trans('Type here...') }}"
                    dataUser="{{ $data->rif }}" icon="mdi mdi-account-card-details " id="rif" isRequired=true
                    autofocus />
            </div>
        </div>

        <div class="row"> {{-- - Encargado - --}}
            <div class='form-group col-6'>
                <x-input-text name="name_incharge" label="{{ trans('Name In Charge') }}"
                    plHolder="{{ trans('Type here...') }}" dataUser="{{ $data->name_incharge }}"
                    icon="mdi mdi-account-settings-variant " id="name_incharge" isRequired=true autofocus />
            </div>
            <div class='form-group col-6'>
                <x-input-text name="jobs_incharge" label="{{ trans('Jobs In Charge') }}"
                    plHolder="{{ trans('Type here...') }}" dataUser="{{ $data->jobs_incharge }}" icon="mdi mdi-tie "
                    id="jobs_incharge" isRequired=true autofocus />
            </div>
        </div>

        <div class="row col"> {{-- - Correo & Teléfono - --}}
            <div class="form-group col-6">
                <x-input-text name="email" label="{{ trans('Email') }}" plHolder="{{ trans('Type here...') }}"
                    dataUser="{{ $data->email }}" icon="mdi mdi-email " id="email" isRequired=true autofocus />
            </div>

            <div class="form-group col-6">
                <x-input-text name="phone" label="{{ trans('Number') }}" icon="mdi mdi-phone-classic "
                    plHolder="{{ trans('Type Phone') }}" dataUser="{{ $data->phone }}" id="phone" isRequired=true
                    autofocus />
            </div>
        </div>

        <div class="address-group"
            style="border: 1px solid #ccc; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <h5>{{ trans('Address Information') }}</h5> {{-- - Dirección - --}}

            <div class="row">
                <div class='form-group col-6'>
                    <x-input-text name="address_line" label="{{ trans('Address: Zone / Sector / Urbanism / Neighborhood') }}"
                        plHolder="{{ trans('Type here... and press Enter to Search') }}" dataUser="{{ $data->address_line }}" icon="mdi mdi-home-map-marker " id="address_line"
                        isRequired=true autofocus />
                </div>

                <div class="col-6">
                    <x-input-select icon="mdi mdi-map-marker-radius" id="postal_code_address" label="{{ trans('Full Address') }}" isRequired=true>
                        {!! html()->select('postal_code_address', $fullAddress, $data->postal_code_address)->class('form-control select2')->id('postal_code_address')->placeholder(trans('Choose Menu'))->required() !!}
                    </x-input-select>
                </div>
            </div>
        </div>

        <div class="row"> {{-- - Porcentajes Cobro y Actividad - --}}
            <div class="row col-9">
                @foreach ([['label' => 'Reserve Found', 'name' => 'reserve_found', 'icons' => 'mdi mdi-cash-multiple ', 'varData' => $data->reserve_found], ['label' => 'Rate Percentage', 'name' => 'rate_percentage', 'icons' => 'mdi mdi-percent ', 'varData' => $data->rate_percentage], ['label' => 'Billing Date', 'name' => 'billing_date', 'icons' => 'mdi mdi-calendar-today ', 'varData' => $data->billing_date]] as $input)
                    <x-input-number icon="{{ $input['icons'] }}" name="{{ $input['name'] }}"
                        dataUser="{{ $input['varData'] }}" label="{{ trans($input['label']) }}"
                        id="{{ $input['name'] }}" broadCol="col-4" isRequired=true />
                @endforeach
            </div>
            <div class='form-group col-sm-6 col-md-3 mt-4'>
                <x-input-checkbox id="md_checkbox" dataUser="{{ $data->active }}" name="active"
                    label="{{ trans('Active') }}" class="checkbox" />
            </div>
        </div>

        <div class='form-group'> {{-- - Observaciones - --}}
            <x-input-area name="observations" label="{{ trans('Observations') }}" dataUser="{{ $data->observations }}"
                plHolder="{{ trans('Type here...') }}" id="observations" isRequired=true />
        </div>

        <div class='form-group'> {{-- - Logo - --}}
            <x-input-text name="logo" label="{{ trans('logo') }}" dataUser="{{ $data->logo }}"
                plHolder="{{ trans('Type here...') }}" icon="mdi mdi-image " id="logo" isRequired=true
                autofocus />
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
    .select2-container {
        z-index: 9999 !important;
        width: 100% !important;
    }

    .modal-lg {
        max-width: 1000px !important;
    }
</style>
<script>
    $(document).ready(function() {
        // Define el titulo y los botones del modal
        $('.modal-title').html(
            '<i class="mdi mdi-tooltip-edit mdi-24px text-warning"></i> - {{ trans('Edit Data') }} {{ $page->title }}'
        );
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');

        $('.select2').select2();

        // Asigna los formateadores de RIF y teléfono a los campos de entrada correspondientes.
        // Las funciones se encuentran en public/js/app-helpers.js
        document.getElementById('rif').addEventListener('input', formatRifInput);
        document.getElementById('phone').addEventListener('input', formatPhoneInput);
    });

    $("#address_line").keydown(function(event) {
        if (event.key === "Enter") {
            $(this).blur(); // Trigger the blur event to reuse the existing logic
        }
    });

    $("#address_line").change(function() {
        var zipName = this.value;
        var postaCodeSelect = document.getElementById('postal_code_address');

        // Limpiar el combo de Zona Postal
        postaCodeSelect.disabled = true;
        postaCodeSelect.innerHTML = '<option value="">' + '{{ trans('Select Address') }}' + '</option>';

        if (zipName) {
            fetch(`${window.location.href}/full-address/${zipName}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status !== false) {
                        // Llenar el combo de ciudades
                        for (var id in data) {
                            var option = document.createElement('option');
                            option.value = id;
                            option.text = data[id];
                            postaCodeSelect.add(option);
                        }
                        postaCodeSelect.disabled = false;
                    } else {
                        console.error(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });
</script>

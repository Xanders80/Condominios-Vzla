{!! html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-create-' . $page->code)->acceptsFiles()->class('form form form-horizontal')->open() !!}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class="row">
                <div class='form-group col-4'> {{-- - Nombre - --}}
                    <x-input-text name="name" label="{{ trans('Name') }}" dataUser="{{ $data->name }}"
                        plHolder="{{ trans('Type here...') }}" icon="mdi mdi-home-modern " id="name" isRequired=true
                        autofocus />
                </div>
                <div class='form-group col-4'> {{-- - Tipo de Unidad - --}}
                    <x-input-select id="unit_type_id" label="{{ trans('Unit Type') }}" isRequired=true>
                        {!! html()->select('unit_type_id', $unitTypes, $data->unit_type_id)->placeholder(trans('Choose Menu'))->class('form-control select2')->id('unit_type_id') !!}
                    </x-input-select>
                </div>
                <div class='form-group col-4 mt-4'> {{-- - Estado de Unidad - --}}
                    <x-input-checkbox id="md_checkbox" dataUser="{{ $data->status }}" name="status"
                        label="{{ trans('Inhabited') }}" class="checkbox" />
                </div>
            </div>
            <div class="row">
                <div class='form-group col-4'> {{-- - Propietario de la Unidad - --}}
                    <x-input-select id="dweller_id" label="{{ trans('Unit Owner') }}" isRequired=true>
                        {!! html()->select('dweller_id', $dweller, $data->dweller_id)->placeholder(trans('Choose Menu'))->class('form-control select2')->id('dweller_id') !!}
                    </x-input-select>
                </div>
                <div class='form-group col-4'> {{-- - Piso o Calle - --}}
                    <x-input-select id="tower_sector_id" label="{{ trans('Tower Sector') }}" isRequired=true>
                        {!! html()->select('tower_sector_id', $towerSector, $data->tower_sector_id)->placeholder(trans('Choose Menu'))->class('form-control select2')->id('tower_sector_id') !!}
                    </x-input-select>
                </div>
                <div class='form-group col-4'> {{-- - Piso o Calle - --}}
                    <x-input-select id="floor_street_id" label="{{ trans('Floor Street') }}" isRequired=true>
                        {!! html()->select('floor_street_id', $floorStreet, $data->floor_street_id)->placeholder(trans('Choose Menu'))->class('form-control select2')->id('floor_street_id') !!}
                    </x-input-select>
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
            // Initialize select2
            $('.select2').select2().parent().css('z-index', 9999);
        });

        $("#tower_sector_id").change(function() {
            var floorSectorId = this.value;
            var floorSectorSelect = document.getElementById('floor_street_id');

            // Limpiar el combo de Municipios
            floorSectorSelect.disabled = true;
            floorSectorSelect.innerHTML = '<option value="">' + '{{ trans('Select a Floor Sector') }}' +
                '</option>';

            if (floorSectorId) {

                fetch(`${window.location.href}/floor-streets/${floorSectorId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status !== false) {
                            // Llenar el combo de ciudades
                            for (var id in data) {
                                var option = document.createElement('option');
                                option.value = id;
                                option.text = data[id];
                                floorSectorSelect.add(option);
                            }
                            floorSectorSelect.disabled = false;
                        } else {
                            console.error(data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    </script>

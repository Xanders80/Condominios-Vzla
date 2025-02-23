{{ html()->form('POST', route($page->url . '.store'))->id('form-create-' . $page->code)->acceptsFiles()->class('form form form-horizontal')->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            @if ($userLevel === 'user')
                <div class="col-md-6">
                    <div class="form-group">
                        <x-show-span dataUser="{{ $data->first_name }}" label="{{ trans('First name') }}" />
                        {!! html()->hidden('first_name', $data->first_name)->id('first_name') !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <x-show-span dataUser="{{ $data->last_name }}" label="{{ trans('Last Name') }}" />
                        {!! html()->hidden('last_name', $data->last_name)->id('last_name') !!}
                    </div>
                </div>
            @else
                <div class="col-6 form-group">
                    <x-input-text name="first_name" label="{{ trans('First name') }}" dataUser="{{ $data->first_name }}"
                        plHolder="{{ trans('Type here...') }}" icon="mdi mdi-account-outline " id="first_name"
                        isRequired=true />
                </div>
                <div class="col-6 form-group">
                    <x-input-text name="last_name" label="{{ trans('Last Name') }}" dataUser="{{ $data->last_name }}"
                        plHolder="{{ trans('Type here...') }}" icon="mdi mdi-account " id="last_name" isRequired=true />
                </div>
            @endif
        </div>
        <div class="row">
            <div class="col-md-2 col-sm-6 form-group">
                <x-input-select id="document_type_id" label="{{ trans('Document Type') }}" isRequired=true>
                    {!! html()->select('document_type_id', $idType)->placeholder(trans('Choose Menu'))->class('form-control select2')->id('document_type_id') !!}
                </x-input-select>
            </div>

            <div class="col-md-4 col-sm-6 form-group">
                <x-input-text icon="mdi mdi-account-card-details " name="document_id" label="{{ trans('Document') }}"
                    id="document_id" broadCol="col-12" isRequired=true />
            </div>
            <div class='form-group col-3'>
                <x-input-text name="phone_number" label="{{ trans('Phone') }}" icon="mdi mdi-phone-classic "
                    plHolder="{{ trans('Type here...') }}" id="phone_number" isRequired=true autofocus />
            </div>
            <div class='form-group col-3'>
                <x-input-text name="cell_phone_number" label="{{ trans('Movil') }}" icon="mdi mdi-cellphone-android "
                    plHolder="{{ trans('Type here...') }}" id="cell_phone_number" isRequired=true autofocus />
            </div>
        </div>


        <div class="row"> {{-- - Correo & Teléfono - --}}
            <div class="form-group col-md-6">
                @if ($userLevel === 'user')
                    <x-show-span dataUser="{{ auth()->user()->email }}" label="{{ trans('Email') }}" />
                    {!! html()->hidden('email', auth()->user()->email)->id('email') !!}
                @else
                    <x-input-select icon="mdi mdi-email-variant" id="email" label="{{ trans('Email') }}"
                        isRequired=true>
                        {!! html()->select('email', $listEmail)->placeholder(trans('Choose Menu'))->class('form-control select2')->id('email') !!}
                    </x-input-select>
                @endif
            </div>
            <div class="col-md-6 form-group">
                <x-input-select icon="mdi mdi-account-settings" id="dweller_type_id"
                    label="{{ trans('Dweller Type') }}" isRequired=true>
                    {!! html()->select('dweller_type_id', $dwellerType)->placeholder(trans('Choose Menu'))->class('form-control select2')->id('dweller_type_id') !!}
                </x-input-select>
            </div>
        </div>
        <x-input-area name="observations" {{-- - Observaciones - --}} label="{{ trans('Observations') }}"
            plHolder="{{ trans('Type here...') }}" id="observations" isRequired=true />
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
        max-width: 1000px !important;
    }
</style>
<script>
    $(document).ready(function() {
        // Define el titulo y los botones del modal
        $('.modal-title').html(
            '<i class="mdi mdi-account-plus mdi-24px text-success"></i> - {{ trans('Add Data') }} {{ $page->title }}'
        );
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Add') }} ');
        // Initialize select2
        $('.select2').select2().parent().css('z-index', 9999);

        function formatPhoneNumber(input) {
            let value = input.value.replace(/\D/g, ''); // Eliminar caracteres no numéricos
            if (value.length > 4 && value.length <= 10) {
                value = `(${value.slice(0, 4)}) ${value.slice(4, 7)}-${value.slice(7, 11)}`;
            } else if (value.length > 10) {
                value = `(${value.slice(0, 4)}) ${value.slice(4, 7)}-${value.slice(7, 11)}`;
            } else if (value.length <= 4) {
                value = value;
            }
            input.value = value;
        }

        document.getElementById('phone_number').addEventListener('input', function(e) {
            formatPhoneNumber(e.target);
        });

        document.getElementById('cell_phone_number').addEventListener('input', function(e) {
            formatPhoneNumber(e.target);
        });
    });
</script>

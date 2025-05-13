{!! html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-update-' . $page->code)->acceptsFiles()->class('form form form-horizontal')->open() !!}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class="col-3 form-group">
                <x-input-text name="nro_confirmation" label="{{ trans('Nro Confirmation') }}"
                    dataUser="{{ $data->nro_confirmation }}" plHolder="{{ trans('Type here...') }}"
                    icon="mdi mdi-ticket-confirmation" id="nro_confirmation" isRequired=true />
            </div>

            <div class="col-3 form-group">
                <x-input-text name="amount" label="{{ trans('Amount') }}" dataUser="{{ $data->amount }}"
                    plHolder="{{ trans('Type here...') }}" icon="mdi mdi-cash " id="amount" isRequired=true />
            </div>

            <div class='form-group col-3'>
                <x-date-time-picker icon="mdi mdi-calendar " id="date_pay" name="date_pay"
                    dataUser="{{ $data->date_pay }}" label="{{ trans('Pay Date') }}" isRequired=true min="-30 days"
                    max="+1 days" />
            </div>

            <div class='form-group col-3'>
                <x-date-time-picker icon="mdi mdi-calendar-check " id="date_confirm" name="date_confirm"
                    dataUser="{{ $data->date_confirm }}" label="{{ trans('Confirm Date') }}" isRequired=true
                    min="-1 days" max="+1 days" />
            </div>
        </div>

        <div class="row">
            <div class="col-6 form-group">
                <x-input-select icon="mdi mdi-bank" id="banks_id" label="{{ trans('Banks') }}" isRequired=true>
                    {!! html()->select('banks_id', $banks, $data->banks_id)->placeholder(trans('Choose Menu'))->class('form-control select2')->id('banks_id') !!}
                </x-input-select>
            </div>

            <div class="col-6 form-group">
                <x-input-select icon="mdi mdi-city" id="condominiums_id" label="{{ trans('Condominiums') }}"
                    isRequired=true>
                    {!! html()->select('condominiums_id', $condominiums, $data->condominiums_id)->placeholder(trans('Choose Menu'))->class('form-control select2')->id('condominiums_id') !!}
                </x-input-select>
            </div>

            <div class="col-6 form-group">
                <x-input-select icon="mdi mdi-credit-card" id="ways_to_pays_id" label="{{ trans('Ways To Pays') }}"
                    isRequired=true>
                    {!! html()->select('ways_to_pays_id', $waystopays, $data->ways_to_pays_id)->placeholder(trans('Choose Menu'))->class('form-control select2')->id('ways_to_pays_id') !!}
                </x-input-select>
            </div>

            <div class="col-6">
                {{-- - Archivo - --}}
                <x-input-file icon="mdi mdi-file-check" id="file" class="form-control"
                    accept="application/pdf,image/jpeg,image/png" labelFile="{{ trans('Allowed: PDF, jpg, png') }}"
                    label="{{ trans('Upload File') }}" type="file" />
            </div>
            <div class='form-group'> {{-- - Observaciones - --}}
                <x-input-area name="observations" label="{{ trans('Observations') }}" dataUser="{{ $data->observations }}"
                    plHolder="{{ trans('Type here...') }}" id="observations" isRequired=true />
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
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
            '<i class="mdi mdi-tooltip-edit mdi-24px text-warning"></i> - {{ trans('Edit Data') }} {{ $page->title }} {{ trans('Dweller') }}: {{ $data->dweller->name }}'
            );
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');
        // Initialize select2
        $('.select2').select2();

        $("#file").on("change", function() {
            var file = this.files[0];
            var fileType = file["type"];
            var ValidImageTypes = ["image/jpeg", "image/png"];
            var ValidPdfTypes = ["application/pdf"];
            if ($.inArray(fileType, ValidImageTypes) < 0 && $.inArray(fileType, ValidPdfTypes) < 0) {
                $("#file").val('');
                Swal.fire({
                    title: "{{ 'Oops!' }}",
                    text: "{{ trans('File not allowed, please choose a PDF or Image file.') }}",
                    icon: "error",
                    showClass: {
                        popup: `
                    animate__animated
                    animate__fadeInUp
                    animate__faster
                    `
                    },
                    hideClass: {
                        popup: `
                    animate__animated
                    animate__fadeOutDown
                    animate__faster
                    `
                    }
                });
            }
        });
    });
</script>

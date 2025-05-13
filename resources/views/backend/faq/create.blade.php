{{ html()->form('POST', route($page->url . '.store'))->id('form-create-' . $page->code)->acceptsFiles()->class('form form-horizontal')->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="form-group row">
            <div class="col-md-6">
                {{-- - Título - --}}
                <x-input-text name="title" label="{{ trans('Title / Group Name / FAQ Name') }}"
                    plHolder="{{ trans('Type here...') }}" icon="mdi mdi-comment-question-outline " id="title"
                    isRequired=true autofocus />
            </div>

            <div class="col-md-6">
                {{-- - Menú - --}}
                <x-input-select icon="mdi mdi-menu" id="menu_id" label="{{ trans('Menu') }}" isRequired=true>
                    {!! html()->select('menu_id', $menu)->class('form-control select2')->id('menu_id')->placeholder(trans('Choose Menu'))->required() !!}
                </x-input-select>
            </div>
        </div>

        {{-- - Descripción - --}}
        <x-input-area name="description" label="{{ trans('Description') }}" plHolder="{{ trans('Type here...') }}"
            id="description" isRequired=true />
        {{-- - Archivo - --}}
        <x-input-file icon="mdi mdi-file-outline " id="file" class="form-control"
            accept="application/pdf,video/mp4,image/jpeg,image/png"
            labelFile="{{ trans('Allowed: PDF video (mp4) image (jpg png)') }}" label="{{ trans('Upload File') }}"
            type="file" />
        <div class="row"> {{-- - Porcentajes Cobro y Actividad - --}}
            <div class="row col-9">
                {{-- - Contadores - --}}
                @foreach ([['label' => 'Visitors', 'name' => 'visitors', 'icons' => 'mdi mdi-eye-outline '], ['label' => 'Likes', 'name' => 'like', 'icons' => 'mdi mdi-thumb-up-outline '], ['label' => 'Dislikes', 'name' => 'dislike', 'icons' => 'mdi mdi-thumb-down-outline ']] as $input)
                    <x-input-number icon="{{ $input['icons'] }}" name="{{ $input['name'] }}"
                        label="{{ trans($input['label']) }}" id="{{ $input['name'] }}" broadCol="col-4"
                        isRequired=true />
                @endforeach
            </div>
            {{-- - Checkbox - --}}
            <div class='form-group col-sm-6 col-md-3 mt-4'>
                </di<x-input-checkbox id="md_checkbox" name="publish" label="{{ trans('Published') }}" class="checkbox"
                    valor="1" />
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
        max-width: 1000px !important;
    }
</style>
<script>
    $(document).ready(function() {
        // Define el titulo y los botones del modal
        $('.modal-title').html(
            '<i class="mdi mdi-book-plus mdi-24px text-success"></i> - {{ trans('Add Data') }} {{ $page->title }}'
            );
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Add') }} ');
        // Initialize select2
        $('.select2').select2().parent().css('z-index', 9999);

        // File input change event
        $("#file").on("change", function() {
            const file = this.files[0];
            const fileType = file["type"];
            const validFileTypes = [
                "video/mp4",
                "video/avi",
                "video/mov",
                "image/jpeg",
                "image/png",
                "application/pdf"
            ];

            // Check if the file type is valid
            if (!validFileTypes.includes(fileType)) {
                $("#file").val('');
                Swal.fire({
                    title: "{{ 'Oops!' }}",
                    text: "{{ trans('File not allowed, please choose a PDF, Video or Image file.') }}",
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

        // Detectar el idioma del navegador
        var userLang = navigator.language || navigator.userLanguage; // Obtener el idioma del navegador
        userLang = userLang.split('-')[0]; // Extraer solo la parte del idioma (ej. "es", "en")

        // Configuración del idioma para Summernote
        var summernoteLang = {
            'en': 'en-US', // Inglés
            'es': 'es-ES', // Español
            // Puedes agregar más idiomas según sea necesario
        };

        // Establecer el idioma por defecto si no se encuentra
        var langActual = summernoteLang[userLang] || 'en-US';

        // Inicializar Summernote
        $('#description').summernote({
            tabsize: 2,
            height: 200,
            width: 1000,
            spellCheck: false,
            dialogsInBody: true,
            lang: langActual, // Establecer el idioma
            callbacks: {
                onImageUpload: function(files) {
                    sendFile(files[0]);
                }
            }
        });
    });
</script>

{{ html()->form('POST', route($page->url . '.store'))->id('form-create-' . $page->code)->acceptsFiles()->class('form form-horizontal')->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="form-group row">
            <div class="col-md-6">
                {{-- - Título Anuncio - --}}
                <x-input-text name="title" label="{{ trans('Announcement Title') }}"
                    plHolder="{{ trans('Type here...') }}" icon="mdi mdi-newspaper " id="title" isRequired=true
                    autofocus />
            </div>

            <div class="col-md-6">
                {{-- - Menú Objetivo - --}}
                <x-input-select icon="mdi mdi-format-list-bulleted" id="menu_id" label="{{ trans('Target Menu') }}"
                    isRequired=true>
                    {!! html()->select('menu_id', $menu)->class('form-select select2')->id('menu_id')->placeholder(trans('Choose Menu'))->required() !!}
                </x-input-select>
            </div>
        </div>

        <div class="row">
            <div class='form-group col-6'>
                <x-date-time-picker icon="mdi mdi-calendar-today " id="start" name="start"
                    label="{{ trans('Start Date') }}" isRequired=true min="-1 days" max="+1 days" />
            </div>

            <div class='form-group col-6'>
                <x-date-time-picker icon="mdi mdi-calendar " id="end" name="end"
                    label="{{ trans('End Date') }}" isRequired=true min="-1 days" max="+30 days" />
            </div>
        </div>

        <div class='form-group'>
            {{-- - Contenido del Anuncio - --}}
            <x-input-area name="content" label="{{ trans('Announcement Content') }}"
                plHolder="{{ trans('Type here...') }}" id="content" isRequired=true />
        </div>

        <div class='form-group mt-1'>
            <x-input-file icon="ti-file" id="file" class="file-drag-drop"
                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/jpeg,image/png"
                labelFile="{{ trans('Allowed: JPG, JPEG, PNG, PDF, DOC, DOCX, XLS, XLSX') }}"
                label="{{ trans('Supporting Files') }}" type="file[]" isMultiple=true />
        </div>

        <div class='form-group row'>
            <div class="col-md-6">
                {{-- - Menú Urgente- --}}
                <x-input-select icon="mdi mdi-format-list-bulleted-type" id="urgency"
                    label="{{ trans('Urgency Level') }}" isRequired=true>
                    {!! html()->select('urgency', config('master.content.announcement.status'))->class('form-select select2')->id('urgency')->placeholder(trans('Choose Menu'))->required() !!}
                </x-input-select>
            </div>

            <div class="col-md-6">
                {{-- - Menú Relación - --}}
                <x-input-select icon="mdi mdi-format-list-checks" id="parent_id"
                    label="{{ trans('Related to another announcement?') }}">
                    {!! html()->select('parent_id', $parent)->class('form-control select2')->id('parent_id')->placeholder(trans('Choose Menu')) !!}
                </x-input-select>
            </div>
        </div>

        <!-- Publish Checkbox -->
        <x-input-checkbox id="md_checkbox" name="publish" label="{{ trans('Show Announcement') }}" class="checkbox"
            valor="1" />
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
<link href="{{ asset($template . '/fileupload/css/fileinput.css') }}" rel="stylesheet">
<link href="{{ asset($template . '/fileupload/css/font_bootstrap-icons.min.css') }}" rel="stylesheet">

<style>
    .kv-file-upload,
    .fileinput-upload,
    .file-upload-indicator {
        display: none;
    }

    .select2-container {
        z-index: 9999 !important;
        width: 100% !important;
    }

    .modal-lg {
        max-width: 1000px !important;
    }
</style>

<script src="{{ asset($template . '/fileupload/js/fileinput.js') }}"></script>

<script>
    $('#menu_id, #parent_id, #urgency').select2().parent().css('z-index', 9999)
    // Define el titulo y los botones del modal
    $('.modal-title').html(
        '<i class="mdi mdi-book-plus mdi-24px text-success"></i> - {{ trans('Add Data') }} {{ $page->title }}');
    $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Add') }} ');

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

    $('#content').summernote({
        tabsize: 2,
        height: 200,
        width: 1000,
        lang: langActual,
        toolbar: [
            "fontsize", "fontname", "forecolor", "paragraph", "table",
            "insert", "codeview", "link", "color"
        ],
        fontSizes: ['8', '9', '10', '11', '12', '14', '18', '24', '36'],
    });

    var noteModal = document.querySelector('.note-modal');
    noteModal.style.zIndex = 9999;
    noteModal.querySelector('.checkbox').style.display = 'none';
    noteModal.querySelector('.note-modal-content').style.padding = '3px';

    $(".file-drag-drop").fileinput({
        theme: 'fa',
        uploadUrl: "/#",
        allowedFileExtensions: ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx'],
        overwriteInitial: false,
        maxFileSize: 2048,
        maxFilesNum: 10,
        slugCallback: function(filename) {
            return filename.replace('(', '_').replace(']', '_');
        },
        initialPreviewAsData: true,
    });

    // File input change event
    $("#file").on("change", function() {
        const file = this.files[0];
        if (file) {
            const fileType = file["name"].split('.').pop().toLowerCase(); // Obtener la extensión del archivo
            const validFileTypes = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];

            // Check if the file type is valid
            if (!validFileTypes.includes(fileType)) {
                $("#file").val(''); // Clear the input
                // Show error message
                alert("Oops! File not allowed, please choose a PDF, Image or Document file.");
            }
        }
    });
</script>

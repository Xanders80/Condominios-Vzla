{!! html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-create-' . $page->code)->acceptsFiles()->class('form form-horizontal')->open() !!}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="form-group row">
            <div class="col-md-6">
                {{-- - Título Anuncio - --}}
                <x-input-text name="title" dataUser="{{ $data->title }}" label="{{ trans('Announcement Title') }}"
                    plHolder="{{ trans('Type here...') }}" icon="mdi mdi-newspaper " id="title" isRequired=true
                    autofocus />
            </div>

            <div class="col-md-6">
                {{-- - Menú Objetivo - --}}
                <x-input-select icon="mdi mdi-format-list-bulleted" id="menu_id" label="{{ trans('Target Menu') }}"
                    isRequired=true>
                    {!! html()->select('menu_id', $menu, $data->menu_id)->class('form-select select2')->id('menu_id')->placeholder(trans('Choose Menu'))->required() !!}
                </x-input-select>
            </div>
        </div>

        <!-- Start and End Dates -->
        <div class="row">
            <div class='form-group col-6'>
                <x-date-time-picker icon="mdi mdi-calendar-today " id="start" name="start"
                    label="{{ trans('Start Date') }}" dataUser="{{ $data->start }}" isRequired=true min="-1 days"
                    max="+1 days" />
            </div>

            <div class='form-group col-6'>
                <x-date-time-picker icon="mdi mdi-calendar " id="end" name="end"
                    label="{{ trans('End Date') }}" dataUser="{{ $data->end }}" isRequired=true min="-1 days"
                    max="+30 days" />
            </div>
        </div>

        {{-- - Contenido del Anuncio - --}}
        <x-input-area name="content" dataUser="{{ $data->content }}" label="{{ trans('Announcement Content') }}"
            plHolder="{{ trans('Type here...') }}" id="content" isRequired=true />

        <!-- Supporting Files -->
        <div class='form-group'>
            <x-input-file icon="ti-file" id="file" class="file-drag-drop"
                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/jpeg,image/png"
                labelFile="{{ trans('Allowed: JPG, JPEG, PNG, PDF, DOC, DOCX, XLS, XLSX') }}"
                label="{{ trans('Supporting Files') }}" type="file[]" isMultiple=true />
        </div>

        @if (!$data->file->isEmpty())
            <div class="form-group">
                <label class="control-label">{{ trans('Current Supporting Files: ') }}</label>
                <table class="table">
                    @foreach ($data->file as $file)
                        @if ($file->exists())
                            <tr id="{{ $file->id }}">
                                <td>
                                    {{ trans('File') }}:
                                    <a href="{{ $file->link_stream }}" target="_blank">{{ $file->file_name }}</a>
                                </td>
                                <td>
                                    {{ trans('Size') }}: {!! $file->size !!}
                                </td>
                                <td>
                                    <a href="#delete" class="btn btn-danger btn-xs delete-file"
                                        data-title={{ trans('Delete') }} data-id="{{ $file->id }}"
                                        data-url="{{ $file->link_delete }}"
                                        data-message="{{ trans('Do you want to delete this file?') }}">
                                        <span class="mdi mdi-delete "></span>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            </div>
        @endif

        <div class='form-group row'>
            <div class="col-md-6">
                {{-- - Menú Urgente- --}}
                <x-input-select icon="mdi mdi-format-list-bulleted-type" id="urgency"
                    label="{{ trans('Urgency Level') }}" isRequired=true>
                    {!! html()->select('urgency', config('master.content.announcement.status'), $data->urgency)->class('form-select select2')->id('urgency')->placeholder(trans('Choose Menu'))->required() !!}
                </x-input-select>
            </div>

            <div class="col-md-6">
                {{-- - Menú Relación - --}}
                <x-input-select icon="mdi mdi-format-list-checks" id="parent_id"
                    label="{{ trans('Related to another announcement?') }}" isRequired=true>
                    {!! html()->select('parent_id', $parent, $data->parent_id)->class('form-control select2')->id('parent_id')->placeholder(trans('Choose Menu')) !!}
                </x-input-select>
            </div>
        </div>

        <!-- Publish Checkbox -->
        <x-input-checkbox id="md_checkbox" dataUser="{{ $data->publish }}" name="publish"
            label="{{ trans('Show Announcement') }}" class="checkbox" />

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

<!-- Stylesheets -->
<link href="{{ asset($template . '/fileupload/css/fileinput.css') }}" rel="stylesheet">
<link href="{{ asset($template . '/fileupload/css/font_bootstrap-icons.min.css') }}" rel="stylesheet">

<!-- Inline Styles -->
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

<!-- Script Dependencies -->
<script src="{{ asset($template . '/fileupload/js/fileinput.js') }}"></script>

<!-- JavaScript Initialization -->
<script>
    $('.modal-title').html(
        '<i class="mdi mdi-tooltip-edit mdi-24px text-warning"></i> - {{ trans('Edit Data') }} {{ $page->title }}'
        );
    $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');

    $('.select2').select2().parent().css('z-index', 9999);

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

    // Configure note modal
    var noteModal = document.querySelector('.note-modal');
    noteModal.style.zIndex = 9999;
    noteModal.querySelector('.checkbox').style.display = 'none';
    noteModal.querySelector('.note-modal-content').style.padding = '3px';

    // Initialize File Input
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
</script>

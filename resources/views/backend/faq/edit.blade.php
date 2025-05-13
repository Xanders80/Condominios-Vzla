{!! html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-update-' . $page->code)->acceptsFiles()->class('form form-horizontal')->open() !!}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="form-group row">
            <div class="col-md-6">
                {{-- - Título - --}}
                <x-input-text name="title" dataUser="{{ $data->title }}"
                    label="{{ trans('Title / Group Name / FAQ Name') }}" plHolder="{{ trans('Type here...') }}"
                    icon="mdi mdi-comment-question-outline " id="title" isRequired=true autofocus />
            </div>

            <div class="col-md-6">
                {{-- - Menú - --}}
                <x-input-select icon="mdi mdi-menu" id="menu_id" label="{{ trans('Menu') }}" isRequired=true>
                    {!! html()->select('menu_id', $menu, $data->menu_id)->class('form-control select2')->id('menu_id')->placeholder(trans('Choose Menu'))->required() !!}
                </x-input-select>
            </div>
        </div>

        {{-- - Descripción - --}}
        <x-input-area name="description" dataUser="{{ $data->description }}" label="{{ trans('Description') }}"
            plHolder="{{ trans('Type here...') }}" id="description" isRequired=true />

        <div class='form-group'>
            {{-- - Archivo - --}}
            <x-input-file icon="mdi mdi-file-outline " id="file" class="form-control"
                accept="application/pdf,video/mp4,image/jpeg,image/png"
                labelFile="{{ trans('Allowed: PDF video (mp4) image (jpg png)') }}" label="{{ trans('Upload File') }}"
                type="file" />
        </div>
        @if (!is_null($data->file))
            <div class='form-group'>
                <table class="table table-{!! $data->id !!}">
                    @foreach ($data->files()->whereNull('alias')->cursor() as $file)
                        @if ($file->exists())
                            <tr>
                                <td>
                                    {{ trans('File') }}:
                                    <a href="{{ url($file->link_stream) }}" target="_blank"> {{ $file->name }} </a>
                                </td>
                                <td>
                                    {{ trans('Size') }}: {!! $file->size !!}
                                </td>
                                <td>
                                    {!! html()->a(url($data->file->link_download), '<i class="mdi mdi-cloud-download "></i>')->class('btn btn-xs btn-primary')->target('_blank') !!}
                                    {!! html()->a('#delete', '<i class="mdi mdi-delete "></i>')->class('delete btn btn-danger btn-xs')->attribute('data-url', url($data->file->link_delete)) !!}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            </div>
        @endif

        <div class="row"> {{-- - Porcentajes Cobro y Actividad - --}}
            <div class="row col-9">
                {{-- - Contadores - --}}
                @foreach ([['label' => 'Visitors', 'name' => 'visitors', 'icons' => 'mdi mdi-eye-outline', 'varData' => $data->visitors], ['label' => 'Likes', 'name' => 'like', 'icons' => 'mdi mdi-thumb-up-outline', 'varData' => $data->like], ['label' => 'Dislikes', 'name' => 'dislike', 'icons' => 'mdi mdi-thumb-down-outline', 'varData' => $data->dislike]] as $input)
                    <x-input-number icon="{{ $input['icons'] }}" name="{{ $input['name'] }}"
                        dataUser="{{ $input['varData'] }}" label="{{ trans($input['label']) }}"
                        id="{{ $input['name'] }}" broadCol="col-4" isRequired=true />
                @endforeach
            </div>
            <div class='form-group col-sm-6 col-md-3 mt-4'>
                </di<x-input-checkbox id="md_checkbox" name="publish" dataUser="{{ $data->publish }}"
                    label="{{ trans('Published') }}" class="checkbox" isRequired=true />
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
    // Define el titulo y los botones del modal
    $('.modal-title').html(
        '<i class="mdi mdi-tooltip-edit mdi-24px text-warning"></i> - {{ trans('Edit Data') }} {{ $page->title }}'
        );
    $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');

    $('.select2').select2().parent().css('z-index', 9999);

    $("#file").on("change", function() {
        var file = this.files[0];
        var fileType = file["type"];
        var ValidVideoTypes = ["video/mp4", "video/avi", "video/mov"];
        var ValidImageTypes = ["image/jpeg", "image/png"];
        var ValidPdfTypes = ["application/pdf"];
        if ($.inArray(fileType, ValidImageTypes) < 0 && $.inArray(fileType, ValidVideoTypes) < 0 && $.inArray(
                fileType, ValidPdfTypes) < 0) {
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

    $('#description').summernote({
        tabsize: 2,
        height: 200,
        width: 1000,
        spellCheck: false,
        dialogsInBody: true,
        lang: langActual, // Establecer el idioma
        toolbar: [
            ['font', ['bold', 'underline', 'italic', 'clear']],
            ['fontname', ['fontname', 'fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
        ],
        callbacks: {
            onImageUpload: function(files) {
                sendFile(files[0], $(this));
            },
            onMediaDelete: function(target) {
                let id = $(target).attr('id');
                let alt = $(target).attr('alt');
                let url = "{{ url(config('master.app.url.backend') . '/file/delete') }}" + '/' + id + '/' +
                    alt;
                fileDelete(url, function(status) {
                    if (status) {
                        Swal.fire({
                            title: "{{ trans('Success!') }}",
                            text: "{{ trans('File deleted successfully.') }}",
                            icon: "success",
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
            }
        }
    });

    $('.delete').on("click", function() {
        let url = $(this).data('url');
        let row = $(this).closest('tr');
        row.hide();
        fileDelete(url, function(status) {
            if (status) {
                Swal.fire({
                    title: "{{ trans('Success!') }}",
                    text: "{{ trans('File deleted successfully.') }}",
                    icon: "success",
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
            } else {
                row.show();
                Swal.fire({
                    title: "{{ 'Oops!' }}",
                    text: "{{ trans('Something went wrong, please try again later.') }}",
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

    function fileDelete(url, callback) {
        $.ajax({
            type: 'GET',
            url: url,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                callback(response.status);
            },
            error: function() {
                callback(false);
            }
        });
    }
</script>

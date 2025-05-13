{!! html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-edit-' . $page->code)->acceptsFiles()->class('form form-horizontal')->open() !!}
<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <x-input-text name="title" dataUser="{{ $data->title }}" label="{{ trans('Menu Name') }}"
                        plHolder="{{ trans('Type name here') }}" icon="fa fa-bars" id="title" isRequired=true
                        autofocus />
                </div>

                <div class="form-group">
                    <x-input-text name="url" dataUser="{{ $data->url }}" label="{{ trans('Link') }}"
                        plHolder="{{ trans('Type url here') }}" icon="mdi mdi-link" id="url" isRequired=true
                        autofocus />
                </div>

                <div class="form-group">
                    <x-input-select icon="mdi mdi-relative-scale" id="model" label="{{ trans('Model') }}">
                        {!! html()->select('model', $model, collect(explode('\\', $data->model))->last())->class('form-control select2')->id('model')->placeholder(trans('Select Model (optional)')) !!}
                    </x-input-select>
                </div>

                <div class="form-group">
                    <x-input-text name="icon" label="{{ trans('Icon') }}" dataUser="{{ $data->icon }}"
                        plHolder="{{ trans('Icon') }}" icon="{{ $data->icon }}" class="form-control iconpicker"
                        id="icon" isRequired=true autocomplete="off" />
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <x-input-text name="subtitle" label="{{ trans('Menu Subtitle') }}"
                        dataUser="{{ $data->subtitle }}" plHolder="{{ trans('Type subtitle here') }}"
                        icon="mdi mdi-format-title" id="subtitle" isRequired=true autofocus />
                </div>
                <div class="form-group">
                    <x-input-text name="code" dataUser="{{ $data->code }}" label="{{ trans('Menu Code') }}"
                        plHolder="{{ trans('Type code here') }}" icon="fa fa-code" id="code" isRequired=true
                        autofocus />
                </div>
                <div class="form-group">
                    <x-input-select icon="mdi mdi-library-books" id="model" label="{{ trans('Menu Type') }}">
                        {!! html()->select('type', ['', 'backend' => 'Backend', 'frontend' => 'Frontend'], $data->type)->class('form-control select2')->id('type')->placeholder(trans('Select Menu Type')) !!}
                    </x-input-select>
                </div>

                <div class="row">
                    @foreach (['active' => 'Status', 'show' => 'Display', 'coming_soon' => 'Coming Soon', 'maintenance' => 'Maintenance'] as $key => $label)
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input-select id="{{ $key }}" label="{{ trans($label) }}">
                                    {!! html()->select(
                                            $key,
                                            [
                                                1 => $key === 'active' ? trans('Active') : trans('Yes'),
                                                0 => $key === 'active' ? trans('Inactive') : trans('No'),
                                            ],
                                            $data->$key,
                                        )->class('form-control select2')->id($key) !!}
                                </x-input-select>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <x-input-label id="access_group_id" label="{{ trans('Select Access') }}" isRequired="true" />
                <x-input-label id="access-only" label="({{ trans('Select at least one') }})" onlyLabel="true" />

                <div class="row p-5" id="access_group_id">
                    @php($access_menu = $data->access_menu()->pluck('access_group_id'))
                    @foreach ($access_group as $key => $value)
                        <div class="col-12">
                            <x-input-checkbox id="{{ 'access_group_' . $key }}" name="{{ 'access_group_id[]' }}"
                                dataUser="{{ collect($access_menu)->contains($key) }}" label="{{ $value }}"
                                valor="{{ $key }}" class="checkbox" attribute="true" />

                            <div class="form-control access-menu-crud-{{ $key }} m-2">
                                <x-input-label id="access-rights" label="{{ trans('Specify Access Rights') }}" />

                                <a href="javascript:void(0)" type="button"
                                    onclick="checkAll('access-crud-{{ $key }}', {{ $key }})"
                                    class="check-all-{{ $key }} btn btn-xs btn-success">
                                    <i class="fa fa-check"></i> {{ trans('Check All') }}
                                </a>

                                <div class="row mt-2">
                                    @foreach (config('master.app.level') as $i => $v)
                                        <div class="col-md-2 access-crud-{{ $key }}">
                                            <x-input-checkbox id="crud_{{ $i }}_{{ $key }}"
                                                name="access_crud_{{ $key }}[]"
                                                dataUser="{{ collect($data->access_menu()->where('access_group_id', $key)->first()->access ?? [])->contains($v) }}"
                                                label="{{ trans(ucwords($v)) }}" valor="{{ $v }}"
                                                class="checkbox" />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="message"></div>
    <div class="progress" style="display: none;">
        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            <div id="statustxt">0%</div>
        </div>
    </div>
</div>

{!! html()->hidden('function')->value('loadMenu,sidebarMenu')->id('function') !!}
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

<script src="{{ asset($template . '/assets/vendor_components/bootstrap-iconpicker/dist/iconpicker.js') }}"></script>
<script src="{{ asset($template . '/assets/vendor_components/nestable/jquery.nestable.js') }}"></script>
<script>
    // Set modal title
    $('.modal-title').html(
        '<i class="mdi mdi-tooltip-edit mdi-24px text-warning"></i> - {{ trans('Edit Data') }} {{ $page->title }}'
        );
    $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');

    // Initialize Select2
    $('.select2').select2();

    // Initialize Iconpicker
    (async () => {
        const response = await fetch(
            "{{ asset($template . '/assets/vendor_components/bootstrap-iconpicker/dist/iconsets/fontawesome4.json') }}"
            );
        const icons = await response.json();

        const iconpicker = new Iconpicker(document.querySelector(".iconpicker"), {
            icons: icons,
            showSelectedIn: document.querySelector(".selected-icon"),
            defaultValue: "{!! $data->icon !!}",
            valueFormat: val => `fa ${val.replace('fas-', 'fa-')}`,
        });

        iconpicker.set();
        iconpicker.set("{!! str_replace('fa ', '', $data->icon) !!}");
    })();

    // Show/Hide access menu based on checkbox selection
    $('.access_group_id').on('click', function() {
        const count = $('.access_group_id:checked').length;
        const groupId = $(this).val();

        if (count > 0) {
            $('.access-menu-crud-' + groupId).show();
        } else {
            $('.access-crud' + groupId).find('input[type="checkbox"]').prop('checked', false);
            $('.access-menu-level' + groupId).hide();
        }
    });
</script>

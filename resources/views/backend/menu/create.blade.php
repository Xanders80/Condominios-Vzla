    {{ html()->form('POST', route($page->url . '.store'))->id('form-create-' . $page->code)->acceptsFiles()->class('form form-horizontal')->open() }}
    <div class="panel shadow-sm" style="border-radius: 10px;">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    @foreach ([['icons' => 'fa fa-bars', 'label' => 'Menu Name', 'name' => 'title', 'placeholder' => 'Type name here', 'required' => false], ['icons' => 'mdi mdi-link', 'label' => 'Link', 'name' => 'url', 'placeholder' => 'Type url here', 'required' => false], ['icons' => 'mdi mdi-relative-scale', 'label' => 'Model', 'name' => 'model', 'type' => 'select', 'options' => $model, 'placeholder' => 'Select Model']] as $input)
                        <div class="form-group">
                            <x-input-label id="{{ $input['name'] }}" label="{{ trans($input['label']) }}"
                                isRequired="{{ isset($input['required']) }}" />
                            @if (isset($input['type']) && $input['type'] === 'select')
                                <x-input-select icon="{{ $input['icons'] }}" id="{{ $input['name'] }}">
                                    {!! html()->select(trans($input['name']), $input['options'])->placeholder(trans($input['placeholder']))->class('form-control select2')->id($input['name']) !!}
                                </x-input-select>
                            @else
                                <x-input-text name="{{ $input['name'] }}" plHolder="{{ trans($input['placeholder']) }}"
                                    icon="{{ $input['icons'] }}" id="{{ $input['name'] }}" autofocus />
                            @endif
                        </div>
                    @endforeach

                    <div class="form-group">
                        <div class="input-group mb-3">
                            <x-input-text name="icon" label="{{ trans('Icon') }}" plHolder="{{ trans('Icon') }}"
                                icon="{{ $input['icons'] }}" class="form-control iconpicker" id="icon"
                                isRequired="{{ isset($input['required']) }}" autocomplete="off" />
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    @foreach ([['icons' => 'mdi mdi-format-title', 'label' => 'Menu Subtitle', 'name' => 'subtitle', 'placeholder' => 'Type subtitle here', 'required' => false, 'note' => 'e.g : Welcome to Menu page'], ['icons' => 'fa fa-code', 'label' => 'Menu Code', 'name' => 'code', 'placeholder' => 'Type code here', 'required' => false], ['icons' => 'mdi mdi-library-books', 'label' => 'Menu Type', 'name' => 'type', 'type' => 'select', 'options' => ['' => 'Select Menu Type', 'backend' => 'Backend', 'frontend' => 'Frontend'], 'default' => 'backend', 'required' => false]] as $input)
                        <div class="form-group">
                            <x-input-label id="{{ $input['name'] }}" label="{{ trans($input['label']) }}"
                                isRequired="{{ isset($input['required']) }}" />
                            @if (isset($input['note']))
                                {!! html()->span(trans($input['note']))->class('text-danger') !!}
                            @endif
                            @if (isset($input['type']) && $input['type'] === 'select')
                                <x-input-select icon="{{ $input['icons'] }}" id="{{ $input['name'] }}">
                                    {!! html()->select(trans($input['name']), $input['options'], $input['default'] ?? null)->placeholder($input['default'])->class('form-control select2')->id($input['name']) !!}
                                </x-input-select>
                            @else
                                <x-input-text name="{{ $input['name'] }}"
                                    plHolder="{{ trans($input['placeholder']) }}" icon="{{ $input['icons'] }}"
                                    id="{{ $input['name'] }}" autofocus />
                            @endif
                        </div>
                    @endforeach

                    <div class="row">
                        @foreach ([['label' => 'Status', 'name' => 'active', 'options' => [1 => trans('Active'), 0 => trans('Inactive')], 'required' => false], ['label' => 'Show', 'name' => 'show', 'options' => [1 => trans('Yes'), 0 => trans('No')], 'required' => false], ['label' => 'Coming Soon', 'name' => 'coming_soon', 'options' => [1 => trans('Yes'), 0 => trans('No')]], ['label' => 'Maintenance', 'name' => 'maintenance', 'options' => [1 => trans('Yes'), 0 => trans('No')]]] as $input)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input-select id="{{ $input['name'] }}" label="{{ trans($input['label']) }}">
                                        {!! html()->select($input['name'], $input['options'], $input['default'] ?? null)->class('form-control select2')->id($input['name']) !!}
                                    </x-input-select>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <x-input-label id="access_group_id" label="{{ trans('Who can access this menu?') }}"
                        isRequired="true" />
                    <x-input-label id="access-only" label="({{ trans('Select at least one') }})" onlyLabel="true" />

                    <div class="row p-5" id="access_group_id">
                        @foreach ($access_group as $key => $value)
                            <div class="col-12">
                                <x-input-checkbox id="md_checkbox_{{ $key }}" name="access_group_id[]"
                                    label="{{ $value }}" valor="{{ $key }}"
                                    class="checkbox access_group_id" />

                                <div class="form-control access-menu-crud-{{ $key }} m-2"
                                    style="display: none;">
                                    <x-input-label id="access-rights" label="{{ trans('Specify Access Rights') }}" />

                                    <a href="javascript:void(0)" type="button"
                                        onclick="checkAll('access-crud-{{ $key }}', {{ $key }})"
                                        class="check-all-{{ $key }} btn btn-xs btn-success">
                                        <i class="fa fa-check"></i> {{ trans('Check All') }}
                                    </a>

                                    <div class="row mt-2">
                                        @foreach (config('master.app.level') as $i => $v)
                                            <div class="col-md-2 access-crud-{{ $key }}">
                                                <x-input-checkbox
                                                    onclick="checkAllLevel('crud_{{ $i }}', this)"
                                                    id="crud_{{ $i }}_{{ $key }}"
                                                    name="access_crud_{{ $key }}[]"
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
            <div class="row">
                <div class="col-md-12">
                    <span class="message"></span>
                    <div class="progress" style="display: none;">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                            aria-valuemax="100">
                            <div id="statustxt">0%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! html()->hidden('function')->value('loadMenu, sidebarMenu')->id('function') !!}
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
            '<i class="mdi mdi-book-plus mdi-24px text-success"></i> - {{ trans('Add Data') }} {{ $page->title }}');
        $('.submit-data').html('<i class="mdi mdi-content-save"></i> {{ trans('Save') }} ');

        // Initialize Select2
        $('.select2').select2();

        // Initialize Iconpicker
        (async () => {
            const response = await fetch(
                "{{ asset($template . '/assets/vendor_components/bootstrap-iconpicker/dist/iconsets/fontawesome4.json') }}"
            )
            const result = await response.json()

            const iconpicker = new Iconpicker(document.querySelector(".iconpicker"), {
                icons: result,
                showSelectedIn: document.querySelector(".selected-icon"),
                defaultValue: 'fa-arrow-right',
                valueFormat: val => `fa ${val.replace('fas-', 'fa-')}`,
            });
            iconpicker.set()
            iconpicker.set('fa-arrow-right')
        })()

        // Show/Hide access menu based on checkbox selection
        $('.access_group_id').on('click', function() {
            let count = $('.access_group_id:checked').length;
            if (count > 0) {
                $('.access-menu-crud-' + $(this).val()).show();
            } else {
                $('.access-crud' + $(this).val()).find('input[type="checkbox"]').prop('checked', false);
                $('.access-menu-level' + $(this).val()).hide();
            }
        })
    </script>

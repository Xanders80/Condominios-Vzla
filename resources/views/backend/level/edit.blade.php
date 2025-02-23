{!! html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-update-' . $page->code)->acceptsFiles()->class('form form-horizontal')->open() !!}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class='form-group row'>
            <div class="col-md-6">
                {{-- - Nombre del Nivel - --}}
                <x-input-text name="name" label="{{ trans('Group Name') }}" plHolder="{{ trans('Type here...') }}"
                    icon="mdi mdi-elevator " id="name" dataUser="{{ $data->name }}" isRequired=true autofocus />
            </div>

            <div class="col-md-6">
                {{-- - Código del Nivel - --}}
                <x-input-text name="code" label="{{ trans('Group Code') }}" plHolder="{{ trans('Type here...') }}"
                    icon="mdi mdi-code-string " id="code" dataUser="{{ $data->code }}" isRequired=true
                    autofocus />
            </div>
        </div>

        <!-- Access Rights Field -->
        <div class="form-group">
            {!! html()->label('Change access rights for this level?', 'access')->class('control-label') !!}
            <div class="row mt-2">
                @foreach (collect(config('master.app.level')) as $key => $level)
                    <div class="col-auto">
                        {{-- - Checkbox - --}}
                        <x-input-checkbox id="md_checkbox_{{ $key }}" name="access[]"
                            dataUser="{{ in_array($level, $data->access ?? []) ? ($data->access[$level] ?? false ? true : false) : false }}"
                            label="{{ trans($level) }}" valor="{{ $level }}" class="checkbox" />
                    </div>
                @endforeach
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
    .modal-lg {
        max-width: 1000px !important;
    }
</style>

<script>
    $('.modal-title').html(
        '<i class="mdi mdi-tooltip-edit mdi-24px text-warning"></i> - {{ trans('Edit Data') }} {{ $page->title }}'
        );
    $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');
</script>

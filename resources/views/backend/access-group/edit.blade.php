{!! html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-update-' . $page->code)->acceptsFiles()->class('form form-horizontal')->open() !!}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class='form-group row'>
            <div class="col-md-6">
                {{-- - Nombre del Grupo - --}}
                <x-input-text name="name" label="{{ trans('Group Name') }}" plHolder="{{ trans('Type here...') }}"
                    icon="mdi mdi-account-multiple " id="name" dataUser="{{ $data->name }}" isRequired=true
                    autofocus />
            </div>

            <div class="col-md-6">
                {{-- - Código - --}}
                <x-input-text name="code" label="{{ trans('Group Code') }}" plHolder="{{ trans('Type here...') }}"
                    icon="mdi mdi-code-string " id="code" dataUser="{{ $data->code }}" isRequired=true
                    autofocus />
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
        '<i class="mdi mdi-tooltip-edit mdi-24px text-warning"></i> - {{ trans('Edit Data') }} {{ $page->title }}');
    $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');
</script>

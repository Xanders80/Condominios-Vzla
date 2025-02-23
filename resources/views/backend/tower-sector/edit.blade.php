{!! html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-create-' . $page->code)->acceptsFiles()->class('form form form-horizontal')->open() !!}
<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row"> {{-- - Nombre & Rif - --}}
            <div class='col-6'>
                <x-input-text name="name" label="{{ trans('Name') }}" dataUser="{{ $data->name }}"
                    plHolder="{{ trans('Type here...') }}" icon="mdi mdi-home-map-marker " id="name" isRequired=true
                    autofocus />
            </div>
            <div class='col-6'>
                <x-input-select icon="mdi mdi-city" id="condominiums_id" label="{{ trans('Condominiums') }}"
                    isRequired=true>
                    {!! html()->select('condominiums_id', $condominiums, $data->condominiums_id)->placeholder('Choose here')->class('form-control select2')->id('condominiums_id') !!}
                </x-input-select>
            </div>
        </div>
        <x-input-text name="description" label="{{ trans('Description') }}" dataUser="{{ $data->description }}"
            plHolder="{{ trans('Type here...') }}" icon="mdi mdi-file-document-box " id="description" isRequired=true
            autofocus />
    </div>
</div>
{!! html()->hidden('table-id', 'datatable')->id('table-id') !!}
{{-- {!! html()->hidden('function','loadMenu,sidebarMenu')->id('function') !!} --}}
{{-- {!! html()->hidden('redirect',url('/dashboard'))->id('redirect') !!} --}}
{!! html()->closeModelForm() !!}
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
    $('.select2').select2();
    $('.modal-title').html(
        '<i class="mdi mdi-tooltip-edit mdi-24px text-warning"></i> - {{ trans('Edit Data') }} {{ $page->title }}');
    $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');
</script>

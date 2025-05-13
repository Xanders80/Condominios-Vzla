{!! html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-update-' . $page->code)->acceptsFiles()->class('form form form-horizontal')->open() !!}
<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <x-input-text name="name" label="{{ trans('Name') }}" dataUser="{{ $data->name }}"
            plHolder="{{ trans('Type here...') }}" icon="mdi mdi-home-outline " id="name" isRequired=true autofocus />
    </div>
</div>
{!! html()->hidden('table-id', 'datatable')->id('table-id') !!}
{{-- {!! html()->hidden('function','loadMenu,sidebarMenu')->id('function') !!} --}}
{{-- {!! html()->hidden('redirect',url('/dashboard'))->id('redirect') !!} --}}
{!! html()->closeModelForm() !!}
<style>
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

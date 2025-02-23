<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6 form-group">
                <x-show-span dataUser="{{ $data->first_name }}" label="{{ trans('First Name') }}" />
            </div>
            <div class="col-md-6 form-group">
                <x-show-span dataUser="{{ $data->last_name }}" label="{{ trans('Last Name') }}" />
            </div>
            <div class="row">
                <div class="col-md-2 col-sm-6 form-group">
                    <x-show-span dataUser="{{ $data->dwellerType->name }}" label="{{ trans('Document Type') }}" />
                </div>
                <div class="col-md-4 col-sm-6 form-group">
                    <x-show-span dataUser="{{ $data->document_id }}" label="{{ trans('Document Type') }}" />
                </div>
                <div class='form-group col-3'>
                    <x-show-span dataUser="{{ $data->phone_number }}" label="{{ trans('Phone') }}" />
                </div>
                <div class='form-group col-3'>
                    <x-show-span dataUser="{{ $data->cell_phone_number }}" label="{{ trans('Movil') }}" />
                </div>
            </div>
            <div class="row"> {{-- - Correo & Teléfono - --}}
                <div class="form-group col-6">
                    <x-show-span dataUser="{{ $data->email }}" label="{{ trans('Email') }}" />
                </div>
                <div class="col-6 form-group">
                    <x-show-span dataUser="{{ $data->dwellerType->name }}" label="{{ trans('Dweller Type') }}" />
                </div>
            </div>
            <x-show-span dataUser="{{ $data->observations }}" label="{{ trans('Observations') }}" />
        </div>
    </div>
</div>
<style>
    .modal-lg {
        max-width: 1000px !important;
    }
</style>
<script>
    $('.submit-data').hide();
    $('.modal-title').html(
        '<i class="mdi mdi mdi-eye mdi-24px text-info"></i> - {{ trans('Show Data') }} {{ $page->title }}');
</script>

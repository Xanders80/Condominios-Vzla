<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <x-show-span dataUser="{{ $data->name }}" label="{{ trans('Name') }}" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <x-show-span dataUser="{{ $data->rif }}" label="{{ trans('Rif') }}" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <x-show-span dataUser="{{ $data->name_incharge }}" label="{{ trans('Name In Charge') }}" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <x-show-span dataUser="{{ $data->jobs_incharge }}" label="{{ trans('Jobs In Charge') }}" />
                </div>
            </div>
            <div class="col-md-6">
                <x-show-span dataUser="{{ $data->email }}" label="{{ trans('Email') }}" />
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <x-show-span dataUser="{{ $data->phone }}" label="{{ trans('Phone') }}" />
                </div>
            </div>
            <div class="address-group mt-2"
                style="border: 1px solid #ccc; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <h5>{{ trans('Address Information') }}</h5> {{-- - Dirección - --}}
                <div class="form-group">
                    <x-show-span dataUser="{{ $data->address_line }}" label="{{ trans('Address: Zone / Sector / Urbanism / Neighborhood') }}" />
                </div>
                <div class="row">
                        <div class="form-group">
                            <x-show-span dataUser="{{ $fullAddress }}" label="{{ trans('Full Address') }}" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-auto">
                    <div class="form-group">
                        <x-show-span dataUser="{{ $data->reserve_found }}" label="{{ trans('Reserve Found') }}" />
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <x-show-span dataUser="{{ $data->rate_percentage }}" label="{{ trans('Rate Percentage') }}" />
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <x-show-span dataUser="{{ $data->billing_date }}" label="{{ trans('Billing Date') }}" />
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <x-show-span dataUser="{{ $nameActivo }}" label="{{ trans('Active') }}" />
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <x-show-span dataUser="{{ $data->logo }}" label="{{ trans('Logo') }}" />
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <x-show-span dataUser="{{ $data->observations }}" label="{{ trans('Observations') }}" />
                </div>
            </div>
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

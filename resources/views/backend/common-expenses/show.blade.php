<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12 form-group">
                <x-show-span dataUser="{{ $data->condominium->name }}" label="{{ trans('Condominium') }}" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <x-show-span dataUser="{{ $data->period->format('m/Y') }}" label="{{ trans('Period') }}" />
            </div>
            <div class="col-md-6 form-group">
                <x-show-span dataUser="{{ number_format($data->total_amount, 2, ',', '.') }} Bs"
                    label="{{ trans('Total Amount') }}" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <x-show-span dataUser="{{ ucfirst($data->status) }}" label="{{ trans('Status') }}" />
            </div>
            <div class="col-md-6 form-group">
                <x-show-span dataUser="{{ $data->created_at->format('d/m/Y H:i') }}"
                    label="{{ trans('Created At') }}" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 form-group">
                <x-show-span dataUser="{{ $data->notes ?? 'N/A' }}" label="{{ trans('Notes') }}" />
            </div>
        </div>
    </div>
</div>

<style>
    .modal-lg {
        max-width: 800px !important;
    }
</style>

<script>
    $('.submit-data').hide();
    $('.modal-title').html(
        '<i class="mdi mdi-eye mdi-24px text-info"></i> - {{ trans('Show Common Expense') }}'
    );
</script>
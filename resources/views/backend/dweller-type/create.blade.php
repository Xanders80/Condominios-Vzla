{{ html()->form('POST', route($page->url . '.store'))->id('form-create-' . $page->code)->acceptsFiles()->class('form form form-horizontal')->open() }}
<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <x-input-text name="name" label="{{ trans('Name') }}" plHolder="{{ trans('Type here...') }}"
            icon="mdi mdi-account-box-outline " id="name" isRequired=true autofocus />
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
    $(document).ready(function() {
        // Define el titulo y los botones del modal
        $('.modal-title').html(
            '<i class="mdi mdi-book-plus mdi-24px text-success"></i> - {{ trans('Add Data') }} {{ $page->title }}'
            );
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Add') }} ');
        // Initialize select2
        $('.select2').select2().parent().css('z-index', 9999);
    });
</script>

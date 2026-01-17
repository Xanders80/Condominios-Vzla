{{ html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-edit-' . $page->code)->class('form form-horizontal')->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class='form-group col-6'>
                <x-input-text name="name" label="{{ trans('Company Name') }}" id="name" isRequired=true />
            </div>
            <div class='form-group col-6'>
                <x-input-text name="tax_id" label="{{ trans('Tax ID (RIF)') }}" id="tax_id" isRequired=true />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-6'>
                <x-input-text name="email" label="{{ trans('Email') }}" id="email" type="email" />
            </div>
            <div class='form-group col-6'>
                <x-input-text name="phone" label="{{ trans('Phone') }}" id="phone" />
            </div>
        </div>
        <div class="row">
            <div class='form-group col-4'>
                <x-input-select id="status" label="{{ trans('Status') }}" isRequired=true>
                    {!! html()->select('status', ['active' => trans('Active'), 'inactive' => trans('Inactive')])->class('form-control select2')->id('status') !!}
                </x-input-select>
            </div>
        </div>
    </div>
</div>

{!! html()->hidden('table-id', 'datatable')->id('table-id') !!}
{!! html()->form()->close() !!}

<script>
    $(document).ready(function() {
        $('.modal-title').html('<i class="mdi mdi-pencil mdi-24px text-warning"></i> - {{ trans('Edit Supplier') }}');
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Update') }} ');
        $('.select2').select2().parent().css('z-index', 9999);
    });
</script>

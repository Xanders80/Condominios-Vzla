{{ html()->form('POST', route($page->url . '.store'))->id('form-create-' . $page->code)->acceptsFiles()->class('form form-horizontal')->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6 form-group">
                <x-input-text name="first_name" label="{{ trans('First name') }}" plHolder="{{ trans('Type here...') }}"
                    icon="mdi mdi-account-outline " id="first_name" isRequired=true autofocus />
            </div>
            <div class="col-md-6 form-group">
                <x-input-text name="last_name" label="{{ trans('Last Name') }}" plHolder="{{ trans('Type here...') }}"
                    icon="mdi mdi-account " id="last_name" isRequired=true autofocus />
            </div>
        </div>

        <div class="form-group">
            <x-input-text name="email" label="{{ trans('Email') }}" plHolder="{{ trans('Type here...') }}"
                icon="mdi mdi-email-variant " id="email" isRequired=true autofocus />
        </div>

        <div class="form-group row">
            <div class="col-md-6">
                <x-input-password name="password" label="{{ trans('Password') }}" plHolder="{{ trans('Type here...') }}"
                    icon="mdi mdi-key " id="password" isRequired=true autofocus />
            </div>
            <div class="col-md-6">
                <x-input-password name="password_confirmation" label="{{ trans('Confirm Password') }}"
                    plHolder="{{ trans('Type here...') }}" icon="mdi mdi-key-variant " id="password_confirmation"
                    isRequired=true autofocus />
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-6">
                <x-input-select icon="mdi mdi-format-list-bulleted" id="level_id"
                    label="{{ trans('Use Access Level') }}">
                    {!! html()->select('level_id', $level)->class('form-control select2')->id('level_id')->placeholder(trans('Choose Menu'))->required() !!}
                </x-input-select>
            </div>
            <div class="col-md-6">
                <x-input-select icon="mdi mdi-format-list-bulleted-type" id="access_group_id"
                    label="{{ trans('Use Access Group') }}">
                    {!! html()->select('access_group_id', $access_group)->class('form-control select2')->id('access_group_id')->placeholder(trans('Choose Menu'))->required() !!}
                </x-input-select>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <span class="message"></span>
        <div class="progress" style="display: none;">
            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                <div id="statustxt">0%</div>
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
            '<i class="mdi mdi-account-plus mdi-24px text-success"></i> - {{ trans('Add Data') }} {{ $page->title }}'
            );
        $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Add') }} ');
        // Initialize select2
        $('.select2').select2();
    });
</script>

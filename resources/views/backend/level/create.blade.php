{{ html()->form('POST', route($page->url . '.store'))->id('form-create-' . $page->code)->acceptsFiles()->class('form form-horizontal')->open() }}

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="form-group row">
            <div class="col-md-6">
                {{-- - Nombre - --}}
                <x-input-text name="name" label="{{ trans('Level Name') }}" plHolder="{{ trans('Type here...') }}"
                    icon="mdi mdi-elevator " id="name" isRequired=true autofocus />
            </div>

            <div class="col-md-6">
                {{-- - Código l - --}}
                <x-input-text name="code" label="{{ trans('Level Code') }}" plHolder="{{ trans('Type here...') }}"
                    icon="mdi mdi-code-string " id="code" isRequired=true autofocus />
            </div>
        </div>

        <div class='form-group'>
            {!! html()->label('What access rights does this level have?', 'access')->class('control-label') !!}
            <div class="row mt-2">
                @foreach (collect(config('master.app.level')) as $key => $level)
                    <div class="col-auto">
                        {{-- - Checkbox - --}}
                        <x-input-checkbox id="md_checkbox_{{ $key }}" name="access[]"
                            label="{{ trans($level) }}" valor="{{ $level }}" class="checkbox" />
                    </div>
                @endforeach
                <span class="text-danger">
                    <i
                        class="mdi mdi-alert-octagram"></i>{{ trans('This will restrict all actions that can be performed by users with this level') }}
                </span>
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
        '<i class="mdi mdi-book-plus mdi-24px text-success"></i> - {{ trans('Add Data') }} {{ $page->title }}');
    $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Add') }} ');
</script>

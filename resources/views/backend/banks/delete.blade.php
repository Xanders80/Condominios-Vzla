{!! html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() !!}
<x-body-delete>
    <div class="col-auto">
        <div class="form-group">
            <x-show-span condition=true dataUser="{{ $data->code_sudebank }}" label="{{ trans('Code Sudeban') }}" />
        </div>
    </div>
    <div class="col-auto">
        <div class="form-group">
            <x-show-span condition=true dataUser="{{ $data->name_ibp }}" label="{{ trans('Name') }}" />
        </div>
    </div>
</x-body-delete>

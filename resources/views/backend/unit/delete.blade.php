{!! html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-create-' . $page->code)->class('form form-horizontal')->open() !!}
<x-body-delete>
    <div class="col-auto">
        <div class="form-group">
            <x-show-span condition=true dataUser="{{ $data->name }}" label="{{ trans('Name') }}" />
        </div>
    </div>
    <div class="col-auto">
        <div class="form-group">
            <x-show-span condition=true dataUser="{{ $data->dweller->name }}" label="{{ trans('Dweller') }}" />
        </div>
    </div>
</x-body-delete>

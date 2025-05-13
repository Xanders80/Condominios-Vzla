{!! html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() !!}
<x-body-delete>
    <div class="col-auto">
        <div class="form-group">
            <x-show-span condition=true dataUser="{{ $data->title }}" label="{{ trans('Title') }}" />
        </div>
    </div>
    <div class="col-auto">
        <div class="form-group">
            <x-show-span condition=true dataUser="{{ $data->content }}" label="{{ trans('Content') }}" />
        </div>
    </div>
</x-body-delete>

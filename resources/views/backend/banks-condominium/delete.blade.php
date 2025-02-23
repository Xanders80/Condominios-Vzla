{!! html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() !!}
<x-body-delete>
    <div class="col-auto">
        <div class="form-group">
            <x-show-span condition=true dataUser="{{ $data->condominiums->name }}" label="{{ trans('Condominiums') }}" />
        </div>
    </div>

    <div class="col-auto">
        <div class="form-group">
            <x-show-span condition=true dataUser="{{ $data->banks->name }}" label="{{ trans('Banks') }}" />
        </div>
    </div>

    <div class="col-auto">
        <div class="form-group">
            <x-show-span condition=true dataUser="{{ $data->account_number }}" label="{{ trans('Account Number') }}" />
        </div>
    </div>
</x-body-delete>

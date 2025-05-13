{!! html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-create-' . $page->code)->class('form form-horizontal')->open() !!}
<x-body-delete>
    <div class="col-auto">
        <div class="form-group">
            <x-show-span condition=true dataUser="{{ $data->nro_confirmation }}" label="{{ trans('Nro Confirmation') }}" />
        </div>
    </div>
    <div class="col-auto">
        <div class="form-group">
            <x-show-span condition=true dataUser="{{ $data->amount }}" label="{{ trans('Amount') }}" />
        </div>
    </div>
</x-body-delete>

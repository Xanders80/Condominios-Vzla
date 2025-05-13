@props([
    'id',
    'name',
    'class',
    'label' => '',
    'dataUser' => '',
    'valor' => '',
    'isRequired' => false,
    'onlyLabel' => false,
    'attribute' => '',
])

<div class='form-group'>
    {!! html()->checkbox($name, $dataUser, $valor)->id($id)->class($class)->attributes(!empty($attribute) ? ['onclick' => "checkAllLevel('access-menu-crud-$valor', this)"] : []) !!}
    @if (!empty($label))
        <x-input-label id="{{ $id }}" label="{{ $label }}" isRequired="{{ $isRequired }}"
            onlyLabel="true" />
    @endif
</div>

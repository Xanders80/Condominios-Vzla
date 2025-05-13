@props(['id', 'name', 'label' => '', 'plHolder' => '', 'dataUser' => '', 'isRequired' => false]) <!-- Valor predeterminado agregado -->

<div class='form-group'>
    @if (!empty($label))
        <x-input-label id="{{ $id }}" label="{{ $label }}" isRequired="{{ $isRequired }}" />
    @endif
    <div class='input-group'>
        {!! html()->textarea($name, $dataUser)->placeholder($plHolder)->class('form-control hie bg-transparent')->id($id)->name($name)->required() !!}
    </div>
</div>

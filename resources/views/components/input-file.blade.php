@props([
    'icon',
    'id',
    'class',
    'accept',
    'labelFile',
    'label' => '',
    'type' => '',
    'dataUser' => '',
    'isRequired' => false,
    'isMultiple' => false,
]) <!-- Valor predeterminado agregado -->

<div class='form-group'>
    @if (!empty($label))
        <x-input-label id="{{ $id }}" label="{{ $label }}" isRequired="{{ $isRequired }}" />
    @endif

    <span class="text-danger">{{ $labelFile }}</span><br>
    <div class="input-group file-loading">
        <span class="input-group-text bg-transparent"><i class="{{ $icon }}"></i></span>
        {!! html()->file($type)->id($id)->class($class)->multiple($isMultiple)->data('overwrite-initial', false)->data('min-file-count', 1)->accept($accept) !!}
    </div>
</div>

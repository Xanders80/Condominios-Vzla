@props([
    'icon' => '',
    'id',
    'name',
    'label' => '',
    'class' => 'form-control ps-15 bg-transparent',
    'plHolder' => '',
    'dataUser' => '',
    'isRequired' => false,
]) <!-- Valor predeterminado agregado -->

@if (!empty($label))
    <x-input-label id="{{ $id }}" label="{{ $label }}" isRequired="{{ $isRequired }}" />
@endif

<div class="input-group">
    @if (!empty($icon))
        @if ($name != 'icon')
            <span class="input-group-text bg-transparent">
                <i class="{{ $icon }}" style="font-size: 14px;" aria-hidden="true"></i>
            </span>
        @else
            <span class="input-group-prepend">
                <i class="input-group-text selected-icon" aria-hidden="true"></i>
            </span>
        @endif
    @endif
    {!! html()->text($name, $dataUser)->placeholder($plHolder)->class($class)->id($id)->name($name)->required($isRequired) !!}
</div>

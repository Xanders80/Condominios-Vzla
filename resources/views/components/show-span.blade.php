@props(['label', 'dataUser' => '', 'condition' => false])

@php
    // Determina el color basado en la condición
    $textColor = $condition ? 'text-light' : 'text-black';
@endphp

<div class="form-group">
    {!! html()->span()->text($label)->class("control-label block text-sm font-bold text-nowrap {$textColor}") !!}
    {!! html()->p($dataUser)->class('form-control') !!}
</div>

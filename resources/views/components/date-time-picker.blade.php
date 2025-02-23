@props(['icon', 'id', 'name', 'label' => '', 'dataUser' => date('Y-m-d'), 'isRequired' => false, 'min', 'max']) <!-- Establecer fecha actual como valor por defecto -->

@if (!empty($label))
    <x-input-label id="{{ $id }}" label="{{ $label }}" :isRequired="$isRequired" />
@endif

<div class="input-group">
    <span class="input-group-text bg-transparent"><i class="{{ $icon }}"></i></span>
    <input type="date" class="form-control ps-15 bg-transparent" id="{{ $id }}" name="{{ $name }}"
        value="{{ $dataUser }}" required="{{ $isRequired }}" min="{{ date('Y-m-d', strtotime($min)) }}"
        max="{{ date('Y-m-d', strtotime($max)) }}" />
</div>

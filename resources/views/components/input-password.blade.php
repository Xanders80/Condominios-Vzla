@props(['icon', 'id', 'name', 'label' => '', 'plHolder' => '', 'dataUser' => '', 'isRequired' => false])

@if (!empty($label))
    <x-input-label id="{{ $id }}" label="{{ $label }}" isRequired="{{ $isRequired }}" />
@endif
<div class="input-group">
    <span class="input-group-text bg-transparent"><i class="{{ $icon }}"></i></span>
    {!! html()->password($name, $dataUser)->placeholder($plHolder)->class('form-control ps-15 bg-transparent')->id($id)->name($name)->autocomplete('off')->required($isRequired) !!}
    <span class="input-group-text show-hide-password bg-transparent"><i class="mdi mdi-eye-off-outline "></i></span>
</div>

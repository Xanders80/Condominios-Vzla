@props(['icon' => '', 'id', 'label' => '', 'isRequired' => false])

@if (!empty($label))
    <x-input-label id="{{ $id }}" label="{{ $label }}" isRequired="{{ $isRequired }}" />
@endif
<div class="input-group d-flex align-items-center">
    @if (!empty($icon))
        <span class="input-group-text bg-transparent flex-shrink-0 align-self-start col-1">
            <i class="{{ $icon }}" style="font-size: 14px;"></i>
        </span>
        <div class="col-11">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</div>

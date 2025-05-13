@props(['icon' => '', 'id', 'name', 'label' => '', 'dataUser' => 0, 'isRequired' => false, 'broadCol'])

<div class="{{ $broadCol }}">
    <div class='form-group'>
        @if (!empty($label))
            <x-input-label id="{{ $id }}" label="{{ $label }}" isRequired="{{ $isRequired }}" />
        @endif
        <div class="input-group">
            @if (!empty($icon))
                <span class="input-group-text bg-transparent">
                    <i class="{{ $icon }}" aria-hidden="true"></i>
                </span>
            @endif
            {!! html()->number($id, $dataUser)->class('form-control')->id($id)->name($name)->required($isRequired) !!}
        </div>
    </div>
</div>

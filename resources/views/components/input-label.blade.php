@props(['id', 'label', 'isRequired' => false, 'onlyLabel' => false])

{!! html()->label($label, $id)->class('control-label block text-sm font-bold text-nowrap') !!}
<span class="{{ $isRequired ? 'text-danger' : '' }}">
    {{ $isRequired ? ' * ' : ($onlyLabel ? '' : trans('(Opt.)')) }}
</span>

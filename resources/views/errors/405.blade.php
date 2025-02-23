<x-guest-layout>
    <x-show-body-error codeError="405" message="Oops! {{ trans('Method Not Allowed.') }}"
        detail="{{ trans('The requested page dose Method Not Allowed') }}" />
</x-guest-layout>

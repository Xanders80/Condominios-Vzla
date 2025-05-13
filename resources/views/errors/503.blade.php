<x-guest-layout>
    <x-show-body-error codeError="503" message="{{ trans('Oops! Service unavailable.') }}"
        detail="{{ trans('The requested page dose Service unavailable') }}" />
</x-guest-layout>

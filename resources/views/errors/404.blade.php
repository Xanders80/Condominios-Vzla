<x-guest-layout>
    <x-show-body-error codeError="404" message="{{ trans('Oops! Page not found.') }}"
        detail="{{ trans('The requested page dose not found') }}" />
</x-guest-layout>

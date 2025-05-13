<x-guest-layout>
    <x-show-body-error codeError="401" message="{{ trans('Oops! You are not authorized to access this page.') }}"
        detail="{{ trans('The requested page dose not authorized') }}" />
</x-guest-layout>

<x-guest-layout>
    <x-show-body-error codeError="429" message="{{ trans('Oops! Too many requests.') }}"
        detail="{{ trans('The requested page dose Too many') }}" />
</x-guest-layout>

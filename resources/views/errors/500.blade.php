<x-guest-layout>
    <x-show-body-error codeError="500" message="{{ trans('Oops! Something went wrong on our servers.') }}"
        detail="{{ trans('The requested page dose not exist') }}" />
</x-guest-layout>

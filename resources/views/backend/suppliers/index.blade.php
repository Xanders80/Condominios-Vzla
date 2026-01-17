@extends('backend.main.index')
@push('title', $page->title ?? __('Suppliers'))
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ __('N°') }}</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Tax ID') }}</th>
        <th>{{ __('Category') }}</th>
        <th>{{ __('Phone') }}</th>
        <th>{{ __('Status') }}</th>
        <th class="text-center w-0">{{ __('Action') }}</th>
    </x-body-index>
@endsection

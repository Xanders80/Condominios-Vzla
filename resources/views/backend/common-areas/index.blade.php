@extends('backend.main.index')
@push('title', $page->title ?? __('Common Areas'))
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ __('N°') }}</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Capacity') }}</th>
        <th>{{ __('Fee') }}</th>
        <th>{{ __('Bookable') }}</th>
        <th>{{ __('Status') }}</th>
        <th class="text-center w-0">{{ __('Action') }}</th>
    </x-body-index>
@endsection

@extends('backend.main.index')
@push('title', $page->title ?? __('Common Area Bookings'))
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ __('N°') }}</th>
        <th>{{ __('Unit') }}</th>
        <th>{{ __('Area') }}</th>
        <th>{{ __('Date') }}</th>
        <th>{{ __('Time') }}</th>
        <th>{{ __('Fee Paid') }}</th>
        <th>{{ __('Status') }}</th>
        <th class="text-center w-0">{{ __('Action') }}</th>
    </x-body-index>
@endsection

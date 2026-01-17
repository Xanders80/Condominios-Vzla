@extends('backend.main.index')
@push('title', $page->title ?? __('Interest Rates'))
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ __('N°') }}</th>
        <th>{{ __('Percentage') }}</th>
        <th>{{ __('Start Date') }}</th>
        <th>{{ __('End Date') }}</th>
        <th>{{ __('Status') }}</th>
        <th class="text-center w-0">{{ __('Action') }}</th>
    </x-body-index>
@endsection

@extends('backend.main.index')
@push('title', $page->title ?? __('Motions & Voting'))
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ __('N°') }}</th>
        <th>{{ __('Session Date') }}</th>
        <th>{{ __('Title') }}</th>
        <th>{{ __('Voting Type') }}</th>
        <th>{{ __('Status') }}</th>
        <th class="text-center w-0">{{ __('Action') }}</th>
    </x-body-index>
@endsection

@extends('backend.main.index')
@push('title', $page->title ?? __('Incident Reports'))
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ __('N°') }}</th>
        <th>{{ __('Unit') }}</th>
        <th>{{ __('Title') }}</th>
        <th>{{ __('Priority') }}</th>
        <th>{{ __('Location') }}</th>
        <th>{{ __('Status') }}</th>
        <th class="text-center w-0">{{ __('Action') }}</th>
    </x-body-index>
@endsection

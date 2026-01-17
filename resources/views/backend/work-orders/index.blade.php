@extends('backend.main.index')
@push('title', $page->title ?? __('Work Orders'))
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ __('N°') }}</th>
        <th>{{ __('Title') }}</th>
        <th>{{ __('Supplier') }}</th>
        <th>{{ __('Estimated Cost') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Scheduled Date') }}</th>
        <th class="text-center w-0">{{ __('Action') }}</th>
    </x-body-index>
@endsection

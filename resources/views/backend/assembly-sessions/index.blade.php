@extends('backend.main.index')
@push('title', $page->title ?? __('Assemblies'))
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ __('N°') }}</th>
        <th>{{ __('Type') }}</th>
        <th>{{ __('Date') }}</th>
        <th>{{ __('Location') }}</th>
        <th>{{ __('Quorum') }}</th>
        <th>{{ __('Status') }}</th>
        <th class="text-center w-0">{{ __('Action') }}</th>
    </x-body-index>
@endsection

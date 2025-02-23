@extends('backend.main.index')
@push('title', $page->title ?? 'Unittype')
@section('content')
    <x-body-index showAdd="{{ $user->create && ($recordCount === 0 && $dweller) }}">
        <th class="w-0">{{ __('N°') }}</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Unit Type') }}</th>
        <th>{{ __('Dweller') }}</th>
        <th>{{ __('Tower Sector') }}</th>
        <th>{{ __('Floor Street') }}</th>
        <th>{{ __('Status') }}</th>
        <th class="text-center w-0">{{ __('Action') }}</th>
    </x-body-index>
@endsection

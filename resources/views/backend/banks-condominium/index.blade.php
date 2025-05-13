@extends('backend.main.index')
@push('title', $page->title ?? 'Unittype')
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ __('No') }}</th>
        <th>{{ __('Condominiums') }}</th>
        <th>{{ __('Account Number') }}</th>
        <th>{{ __('Banks') }}</th>
        <th class="text-center w-0">{{ __('Action') }}</th>
    </x-body-index>
@endsection

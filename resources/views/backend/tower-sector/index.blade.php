@extends('backend.main.index')
@push('title', $page->title ?? 'Towersector')
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ __('N°') }}</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Description') }}</th>
        <th>{{ __('Condominiums') }}</th>
        <th class="text-center w-0">{{ __('Action') }}</th>
    </x-body-index>
@endsection

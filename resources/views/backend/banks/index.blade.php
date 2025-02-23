@extends('backend.main.index')
@push('title', $page->title ?? 'Unittype')
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ __('N°') }}</th>
        <th>{{ __('Sudeban') }}</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Rif') }}</th>
        <th>{{ __('Website') }}</th>
        <th>{{ __('Active') }}</th>
        <th class="text-center w-0">{{ __('Action') }}</th>
    </x-body-index>
@endsection

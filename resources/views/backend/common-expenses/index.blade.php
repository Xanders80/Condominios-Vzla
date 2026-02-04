@extends('backend.main.index')
@push('title', $page->title ?? __('Common Expenses'))
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ __('N°') }}</th>
        <th>{{ __('Condominium') }}</th>
        <th>{{ __('Period') }}</th>
        <th>{{ __('Total Amount') }}</th>
        <th>{{ __('Status') }}</th>
        <th class="text-center w-0">{{ __('Action') }}</th>
    </x-body-index>
@endsection
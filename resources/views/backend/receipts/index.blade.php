@extends('backend.main.index')
@push('title', $page->title ?? __('Receipts'))
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ __('N°') }}</th>
        <th>{{ __('Unit') }}</th>
        <th>{{ __('Month/Year') }}</th>
        <th>{{ __('Amount (Bs)') }}</th>
        <th>{{ __('Amount (USD)') }}</th>
        <th>{{ __('Status') }}</th>
        <th class="text-center w-0">{{ __('Action') }}</th>
    </x-body-index>
@endsection

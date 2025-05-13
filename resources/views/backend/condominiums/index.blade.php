@extends('backend.main.index')
@push('title', $page->title ?? 'Condominiums')
@section('content')
    <x-body-index showAdd="{{ $user->create && $dweller }}">
        <th class="w-0">{{ trans('N°') }}</th>
        <th>{{ trans('Name') }}</th>
        <th>{{ trans('In Charge') }}</th>
        <th>{{ trans('Reserve Found') }}</th>
        <th>{{ trans('Rate Percentage') }}</th>
        <th>{{ trans('Billing Date') }}</th>
        <th>{{ trans('Active') }}</th>
        <th class="text-center w-0">{{ trans('Action') }}</th>
    </x-body-index>
@endsection

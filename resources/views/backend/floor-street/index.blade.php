@extends('backend.main.index')
@push('title', $page->title ?? 'Floorstreet')
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ trans('No') }}</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Tower Sector') }}</th>
        <th class="text-center w-0">{{ trans('Actions') }}</th>
    </x-body-index>
@endsection

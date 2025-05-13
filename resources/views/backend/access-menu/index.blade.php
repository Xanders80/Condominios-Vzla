@extends('backend.main.index')
@push('title', $page->title ?? 'Access Menu')
@section('content')
    <x-body-index>
        <th class="w-0">{{ trans('N°') }}</th>
        <th>{{ trans('Access Group') }}</th>
        <th class="text-center w-0">{{ trans('Actions') }}</th>
    </x-body-index>
@endsection

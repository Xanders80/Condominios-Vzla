@extends('backend.main.index')
@push('title', $page->title ?? 'Level')
@section('content')
    <x-body-index>
        <th class="w-0">{{ trans('N°') }}</th>
        <th>{{ trans('Question') }}</th>
        <th>{{ trans('Menu') }}</th>
        <th class="text-center">{{ trans('Actions') }}</th>
    </x-body-index>
@endsection

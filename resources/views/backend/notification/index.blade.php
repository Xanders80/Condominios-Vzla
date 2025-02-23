@extends('backend.main.index')
@push('title', trans($page->title) ?? 'Notification')
@section('content')
    <x-body-index>
        <th class="w-0">{{ trans('N°') }}</th>
        <th>{{ trans('Title') }}</th>
        <th>{{ trans('Message') }}</th>
        <th>{{ trans('Status') }}</th>
        <th class="text-center w-0">{{ trans('Action') }}</th>
    </x-body-index>
@endsection

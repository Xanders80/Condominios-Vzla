@extends('backend.main.index')
@push('title', $page->title ?? 'User Management')
@section('content')
    <x-body-index showAdd="{{ $user->create && auth()->user()->level->code !== 'user' }}" columns="col-sm-4 col-xl-4"
        :dataArray="$data">
        <th class="w-0">{{ trans('N°') }}</th>
        <th>{{ trans('Full Name') }}</th>
        <th>{{ trans('Email') }}</th>
        <th>{{ trans('Level') }}</th>
        <th>{{ trans('Access Group') }}</th>
        <th>{{ trans('Verified') }}</th>
        <th class="text-center w-0">{{ trans('Actions') }}</th>
    </x-body-index>
@endsection

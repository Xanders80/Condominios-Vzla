@extends('backend.main.index')
@push('title', $page->title ?? 'dweller')
@section('content')
    <x-body-index showAdd="{{ $user->create && $recordCount === 0 }}" columns="col-sm-4 col-xl-2" :dataArray="$dataDweller">
        <th class="w-0">{{ __('N°') }}</th>
        <th>{{ __('Full Name') }}</th>
        <th>{{ __('Email') }}</th>
        <th>{{ __('Movil') }}</th>
        <th>{{ __('Dweller Type') }}</th>
        <th class="text-center w-0">{{ __('Action') }}</th>
    </x-body-index>
@endsection

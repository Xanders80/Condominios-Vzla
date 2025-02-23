@extends('backend.main.index')
@push('title', $page->title ?? 'Announcement')
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ trans('N°') }}</th>
        <th>{{ trans('Target') }}</th>
        <th>{{ trans('Title') }}</th>
        <th>{{ trans('Start') }}</th>
        <th>{{ trans('End') }}</th>
        <th>{{ trans('Urgency') }}</th>
        <th>{{ trans('Published') }}</th>
        <th class="text-center w-0">{{ trans('Action') }}</th>
    </x-body-index>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset($template . '/assets/vendor_plugins/summernote/summernote-lite.css') }}">
@endpush

@push('js')
    <script src="{{ asset($template . '/assets/vendor_plugins/summernote/summernote-lite.min.js') }}"></script>
@endpush

@extends('backend.main.index')
@push('title', $page->title ?? 'FAQ')
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ trans('N°') }}</th>
        <th>{{ trans('Title') }}</th>
        <th class="text-center w-0"><i class="mdi mdi-eye-outline mdi-18px text-info"></i></th>
        <th class="text-center w-0"><i class="mdi mdi-thumb-up mdi-18px text-success"></i></th>
        <th class="text-center w-0"><i class="mdi mdi-thumb-down mdi-18px text-danger"></i></th>
        <th class="text-center w-0">{{ trans('Published') }}</th>
        <th class="text-center w-0">{{ trans('Action') }}</th>
    </x-body-index>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset($template . '/assets/vendor_plugins/summernote/summernote-lite.css') }}">
@endpush

@push('js')
    <script src="{{ asset($template . '/assets/vendor_plugins/summernote/summernote-lite.min.js') }}"></script>
@endpush

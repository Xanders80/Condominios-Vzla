@extends('backend.main.index')
@push('title', $page->title ?? 'Level')
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ trans('No') }}</th>
        <th>{{ trans('Level Name') }}</th>
        <th>{{ trans('Code | Alias') }}</th>
        <th>{{ trans('Access Rights') }}</th>
        <th class="text-center w-0">{{ trans('Actions') }}</th>
    </x-body-index>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $(document).on("keyup", "#name", function() {
                let name = $(this).val();
                let value = name.replace(/ /g, "-");
                $("#code").val(value.toLowerCase());
            });
        });
    </script>
@endpush

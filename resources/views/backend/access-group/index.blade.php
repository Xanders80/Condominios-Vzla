@extends('backend.main.index')
@push('title', $page->title ?? 'Access Group')
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ trans('N°') }}</th>
        <th>{{ trans('Group Name') }}</th>
        <th>{{ trans('Code | Alias') }}</th>
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

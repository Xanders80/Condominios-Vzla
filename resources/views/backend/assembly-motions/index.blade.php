@extends('backend.main.index')
@push('title', $page->title ?? __('Motions & Voting'))
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        <th class="w-0">{{ __('N°') }}</th>
        <th>{{ __('Session Date') }}</th>
        <th>{{ __('Title') }}</th>
        <th>{{ __('Voting Type') }}</th>
        <th>{{ __('Status') }}</th>
        <th class="text-center w-0">{{ __('Action') }}</th>
    </x-body-index>

    @push('js')
        <script>
            $(document).ready(function() {
                if (typeof Echo !== 'undefined') {
                    Echo.channel('motion-results')
                        .listen('.motion.updated', (e) => {
                            console.log('Motion updated:', e.motion);
                            // Refresh the datatable if it exists
                            if (window.LaravelDataTables && window.LaravelDataTables["datatable"]) {
                                window.LaravelDataTables["datatable"].ajax.reload(null, false);
                            } else {
                                // Fallback reload if datatable object is not directly accessible this way
                                $('.buttons-reload').click();
                            }
                        });
                }
            });
        </script>
    @endpush
@endsection

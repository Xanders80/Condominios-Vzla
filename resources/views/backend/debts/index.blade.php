@extends('backend.main.index')
@push('title', $page->title ?? __('Deudas'))
@section('content')
    <x-body-index showAdd="{{ $user->create }}">
        @slot('extra_buttons')
            <button class="btn btn-sm btn-info pull-up" onclick="processInterests()" title="{{ __('Process Daily Interests') }}">
                <i class="mdi mdi-calculator"></i> {{ __('Calculate Interests') }}
            </button>
        @endslot
        <th class="w-0">{{ __('N°') }}</th>
        <th>{{ __('Unit') }}</th>
        <th>{{ __('Receipt') }}</th>
        <th>{{ __('Amount') }}</th>
        <th>{{ __('Due Date') }}</th>
        <th>{{ __('Days GP') }}</th>
        <th>{{ __('Status') }}</th>
        <th class="text-center w-0">{{ __('Action') }}</th>
    </x-body-index>

    <script>
        function processInterests() {
            Swal.fire({
                title: '{{ trans('Are you sure?') }}',
                text: '{{ trans('This will calculate interests for all overdue debts.') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ trans('Yes, process!') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route($page->url . '.process-interests') }}',
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            Swal.fire('Processed!', response.message, 'success');
                            $('#datatable').DataTable().ajax.reload();
                        },
                        error: function() {
                            Swal.fire('Error!', 'Failed to process interests', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection

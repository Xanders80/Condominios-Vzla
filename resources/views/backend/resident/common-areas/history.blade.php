@extends('backend.main.index')
@push('title', __('Booking History'))
@section('content')
<div class="content-wrapper">
    <div class="container-full">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="me-auto">
                    <h3 class="page-title">{{ __('My Bookings') }}</h3>
                    <div class="d-inline-block align-items-center">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('coowner-dashboard') }}"><i class="mdi mdi-home-outline"></i></a></li>
                                <li class="breadcrumb-item"><a href="{{ route('resident.common-areas.index') }}">{{ __('Common Areas') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('History') }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-hover no-wrap">
                                    <thead>
                                        <tr class="bg-light">
                                            <th>{{ __('Area') }}</th>
                                            <th>{{ __('Date & Time') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($bookings as $booking)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary-light h-40 w-40 l-h-40 rounded text-center me-10">
                                                            <i class="mdi mdi-tree text-primary fs-20"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 font-weight-600">{{ $booking->commonArea->name }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-mute"><i class="mdi mdi-calendar me-1"></i> {{ $booking->start_time->format('d/m/Y') }}</span><br>
                                                    <small class="text-muted"><i class="mdi mdi-clock-outline me-1"></i> {{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}</small>
                                                </td>
                                                <td>
                                                    <strong>{{ $booking->currency }} {{ number_format($booking->total_amount, 2) }}</strong>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusClasses = [
                                                            'pending' => 'badge-warning',
                                                            'confirmed' => 'badge-success',
                                                            'cancelled' => 'badge-danger',
                                                            'completed' => 'badge-info',
                                                        ];
                                                    @endphp
                                                    <span class="badge {{ $statusClasses[$booking->status] ?? 'badge-secondary' }}">
                                                        {{ ucfirst($booking->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('resident.common-areas.show', $booking->id) }}" class="btn btn-primary-light btn-xs" title="{{ __('View Details') }}">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                        @if($booking->status === 'pending' || $booking->status === 'confirmed')
                                                            <button class="btn btn-danger-light btn-xs" onclick="cancelBooking('{{ $booking->id }}')" title="{{ __('Cancel') }}">
                                                                <i class="mdi mdi-close"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-30 text-muted">
                                                    {{ __('You have no reservation history.') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    function cancelBooking(id) {
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("A cancellation penalty might apply according to the area rules.") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("Yes, cancel it!") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                // Implement cancellation logic here (AJAX)
                $.post('{{ url(config("master.app.url.backend")."/bookings") }}/' + id + '/cancel', {
                    _token: '{{ csrf_token() }}'
                }).done(function(res) {
                    if(res.status) {
                        Swal.fire('{{ __("Cancelled!") }}', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('{{ __("Error!") }}', res.message, 'error');
                    }
                });
            }
        });
    }
</script>
@endsection

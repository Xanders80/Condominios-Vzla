@extends('backend.main.index')
@push('title', __('Booking Details') . ' - ' . $booking->commonArea->name)
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Content Header -->
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="me-auto">
                        <h3 class="page-title">{{ __('Reservation Details') }}</h3>
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('coowner-dashboard') }}"><i
                                            class="mdi mdi-home-outline"></i></a></li>
                                <li class="breadcrumb-item"><a
                                        href="{{ route('resident.common-areas.index') }}">{{ __('Common Areas') }}</a></li>
                                <li class="breadcrumb-item"><a
                                        href="{{ route('resident.common-areas.history') }}">{{ __('History') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('Details') }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="row">
                    <div class="col-lg-8 col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">{{ __('Booking Information') }}</h4>
                                <div class="box-controls pull-right">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'badge-warning',
                                            'confirmed' => 'badge-success',
                                            'cancelled' => 'badge-danger',
                                            'completed' => 'badge-info',
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusClasses[$booking->status] ?? 'badge-secondary' }} fs-14">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-20">
                                            <small class="text-muted d-block">{{ __('Common Area') }}</small>
                                            <h4 class="font-weight-600">{{ $booking->commonArea->name }}</h4>
                                        </div>
                                        <div class="mb-20">
                                            <small class="text-muted d-block">{{ __('Reserved for') }}</small>
                                            <h5 class="font-weight-600"><i class="mdi mdi-calendar me-1"></i>
                                                {{ $booking->start_time->format('d/m/Y') }}</h5>
                                            <p class="mb-0"><i class="mdi mdi-clock-outline me-1"></i>
                                                {{ $booking->start_time->format('H:i') }} -
                                                {{ $booking->end_time->format('H:i') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-20">
                                            <small class="text-muted d-block">{{ __('Total Amount') }}</small>
                                            <h3 class="text-primary font-weight-700">
                                                {{ $booking->currency }} {{ number_format($booking->total_amount, 2) }}
                                            </h3>
                                            @if($booking->currency !== 'USD')
                                                <small class="text-muted">
                                                    ~ USD
                                                    {{ number_format($booking->total_amount / $booking->exchange_rate, 2) }}
                                                    ({{ __('Rate') }}: {{ number_format($booking->exchange_rate, 4) }})
                                                </small>
                                            @endif
                                        </div>
                                        <div class="mb-20">
                                            <small class="text-muted d-block">{{ __('Booking ID') }}</small>
                                            <code>{{ $booking->id }}</code>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="mt-20">
                                    <h5 class="font-weight-600">{{ __('Description') }}</h5>
                                    <p>{{ $booking->commonArea->description ?? __('No description available.') }}</p>
                                </div>
                            </div>
                            <div class="box-footer">
                                <a href="{{ route('resident.common-areas.history') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-arrow-left me-1"></i> {{ __('Back to History') }}
                                </a>
                                @if($booking->status === 'pending' || $booking->status === 'confirmed')
                                    <button class="btn btn-danger-light pull-right"
                                        onclick="cancelBooking('{{ $booking->id }}')">
                                        <i class="mdi mdi-close-circle me-1"></i> {{ __('Cancel Reservation') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-12">
                        @if($booking->status === 'pending' && $booking->total_amount > 0)
                            <div class="box bg-primary-light">
                                <div class="box-header with-border no-border">
                                    <h4 class="box-title text-primary"><i
                                            class="mdi mdi-information me-2"></i>{{ __('Payment Required') }}</h4>
                                </div>
                                <div class="box-body">
                                    <p>{{ __('To confirm your reservation, please report your payment.') }}</p>
                                    <a href="{{ route('payments.create') }}" class="btn btn-primary btn-block">
                                        <i class="mdi mdi-cash-multiple me-1"></i> {{ __('Report Payment Now') }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">{{ __('Location & Rules') }}</h4>
                            </div>
                            <div class="box-body">
                                <p><strong>{{ __('Max Occupancy') }}:</strong> {{ $booking->commonArea->max_occupancy }}
                                    {{ __('Persons') }}</p>
                                <p><strong>{{ __('Rules') }}:</strong></p>
                                <ul class="ps-15">
                                    <li>{{ __('Respect the schedule.') }}</li>
                                    <li>{{ __('Maintain cleanliness.') }}</li>
                                    <li>{{ __('Cancellation penalty') }}:
                                        {{ number_format($booking->commonArea->cancellation_penalty_percentage, 2) }}%</li>
                                </ul>
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
                text: '{{ __("A cancellation penalty might apply.") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ __("Yes, cancel it!") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('{{ route("resident.common-areas.cancel", "") }}/' + id, {
                        _token: '{{ csrf_token() }}'
                    }).done(function (res) {
                        if (res.status) {
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
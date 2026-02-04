@extends('backend.main.index')
@push('title', __('Book Area') . ' - ' . $area->name)
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Content Header -->
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="me-auto">
                        <h3 class="page-title">{{ $area->name }}</h3>
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('coowner-dashboard') }}"><i
                                            class="mdi mdi-home-outline"></i></a></li>
                                <li class="breadcrumb-item"><a
                                        href="{{ route('resident.common-areas.index') }}">{{ __('Common Areas') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('Reservation') }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="row">
                    <!-- Area Details & Rules -->
                    <div class="col-lg-4 col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">{{ __('Reservation Rules') }}</h4>
                            </div>
                            <div class="box-body">
                                <div class="media-list media-list-divided">
                                    <div class="media px-0">
                                        <span class="avatar avatar-sm bg-primary-light rounded"><i
                                                class="mdi mdi-account-group text-primary"></i></span>
                                        <div class="media-body">
                                            <p><strong>{{ __('Max Occupancy') }}</strong></p>
                                            <p>{{ $area->max_occupancy }} {{ __('Persons') }}</p>
                                        </div>
                                    </div>
                                    <div class="media px-0">
                                        <span class="avatar avatar-sm bg-info-light rounded"><i
                                                class="mdi mdi-clock-alert text-info"></i></span>
                                        <div class="media-body">
                                            <p><strong>{{ __('Anticipation') }}</strong></p>
                                            <p>{{ __('At least') }} {{ $area->min_anticipation_hours }}
                                                {{ __('hours before') }}</p>
                                        </div>
                                    </div>
                                    @if($area->max_booking_hours)
                                        <div class="media px-0">
                                            <span class="avatar avatar-sm bg-warning-light rounded"><i
                                                    class="mdi mdi-timer-off text-warning"></i></span>
                                            <div class="media-body">
                                                <p><strong>{{ __('Max Duration') }}</strong></p>
                                                <p>{{ $area->max_booking_hours }} {{ __('hours') }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="media px-0">
                                        <span class="avatar avatar-sm bg-danger-light rounded"><i
                                                class="mdi mdi-cash-register text-danger"></i></span>
                                        <div class="media-body">
                                            <p><strong>{{ __('Cancellation Penalty') }}</strong></p>
                                            <p>{{ number_format($area->cancellation_penalty_percentage, 2) }}%
                                                {{ __('of the total fee') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Form -->
                    <div class="col-lg-8 col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">{{ __('Select Date & Time') }}</h4>
                            </div>
                            <div class="box-body">
                                <form id="form-booking" class="form">
                                    @csrf
                                    <input type="hidden" name="common_area_id" value="{{ $area->id }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('Date') }}</label>
                                                <input type="date" class="form-control" name="booking_date"
                                                    id="booking_date" required min="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('Start Time') }}</label>
                                                <input type="time" class="form-control" name="start_time" id="start_time"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('End Time') }}</label>
                                                <input type="time" class="form-control" name="end_time" id="end_time"
                                                    required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-20">
                                        <div class="col-12">
                                            <div id="availability-status" class="alert d-none"></div>
                                        </div>
                                    </div>

                                    <div class="row mt-10">
                                        <div
                                            class="col-12 d-flex justify-content-between align-items-center bg-light p-20 rounded">
                                            <div>
                                                <h4 class="mb-0">{{ __('Estimated Total') }}</h4>
                                                <h2 class="text-primary mb-0" id="display-fee">--</h2>
                                                <small class="text-muted" id="display-exchange-rate"></small>
                                            </div>
                                            <button type="submit" class="btn btn-primary" id="btn-submit" disabled>
                                                <i class="mdi mdi-check-circle me-1"></i> {{ __('Confirm Reservation') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @push('js')
        <script>
            $(document).ready(function () {
                $('#booking_date, #start_time, #end_time').on('change', function () {
                    checkAvailability();
                });

                $('#form-booking').on('submit', function (e) {
                    e.preventDefault();
                    confirmBooking();
                });

                function checkAvailability() {
                    const date = $('#booking_date').val();
                    const start = $('#start_time').val();
                    const end = $('#end_time').val();

                    if (!date || !start || !end) return;

                    const startTime = date + ' ' + start;
                    const endTime = date + ' ' + end;

                    $('#availability-status').addClass('d-none').removeClass('alert-success alert-danger');
                    $('#btn-submit').prop('disabled', true);

                    $.post('{{ route("resident.common-areas.book") }}', {
                        _token: '{{ csrf_token() }}',
                        common_area_id: '{{ $area->id }}',
                        start_time: startTime,
                        end_time: endTime,
                        dry_run: true // Placeholder for potential availability-only check
                    }).done(function (res) {
                        if (res.status) {
                            $('#availability-status').html('<i class="mdi mdi-check-circle me-1"></i> ' + res.message)
                                .removeClass('d-none alert-danger').addClass('alert-success');

                            if (res.data && res.data.fee) {
                                $('#display-fee').text(res.data.fee.currency + ' ' + Number(res.data.fee.total).toFixed(2));
                                if (res.data.fee.currency !== 'USD') {
                                    // Optionally show USD equivalent
                                }
                            }
                            $('#btn-submit').prop('disabled', false);
                        } else {
                            $('#availability-status').html('<i class="mdi mdi-alert-circle me-1"></i> ' + res.message)
                                .removeClass('d-none alert-success').addClass('alert-danger');
                            $('#display-fee').text('--');
                        }
                    });
                }

                function confirmBooking() {
                    const date = $('#booking_date').val();
                    const start = $('#start_time').val();
                    const end = $('#end_time').val();

                    Swal.fire({
                        title: '{{ __("Confirm Reservation") }}?',
                        text: '{{ __("You are about to book this area.") }}',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: '{{ __("Yes, Book it") }}',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return $.post('{{ route("resident.common-areas.book") }}', {
                                _token: '{{ csrf_token() }}',
                                common_area_id: '{{ $area->id }}',
                                start_time: date + ' ' + start,
                                end_time: date + ' ' + end
                            }).then(response => {
                                if (!response.status) {
                                    throw new Error(response.message)
                                }
                                return response;
                            }).catch(error => {
                                Swal.showValidationMessage(`Request failed: ${error}`)
                            })
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: '{{ __("Success!") }}',
                                text: result.value.message,
                                icon: 'success'
                            }).then(() => {
                                window.location.href = '{{ route("resident.common-areas.history") }}';
                            });
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
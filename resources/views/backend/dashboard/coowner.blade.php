@extends('backend.main.index')
@push('title', __('Co-owner Dashboard'))
@section('content')
    <div class="content-wrapper hold-transition">
        <div class="container-full">
            <section class="content">
                <div class="row align-items-end">
                    <div class="col-12">
                        <div class="box bg-gradient-primary-dark overflow-hidden pull-up">
                            <div class="box-body pe-0 ps-lg-50 ps-15 py-0">
                                <div class="row align-items-center">
                                    <div class="col-12 col-lg-8">
                                        <h1 class="fs-40 text-white">¡{{ trans('Hola') . ' ' . auth()->user()->name }}!</h1>
                                        <p class="text-white mb-0 fs-20">
                                            {{ __('Welcome to your co-owner portal. Here you can see the status of your properties and the community.') }}
                                        </p>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <img src="{{ asset($template . '/images/svg-icon/color-svg/custom-1.svg') }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Financial Status -->
                <div class="row">
                    <div class="col-12">
                        <h4 class="box-title">{{ __('My Status') }}</h4>
                    </div>
                    @foreach ($paymentSummary as $input)
                        <div class="col-sm-6 col-xl-3">
                            <x-card-component
                                label="{{ $input['label'] }}"
                                message="{{ $input['message'] }}"
                                end_text="{{ $input['end_text'] }}"
                                icon="{{ $input['icon'] }}"
                            ></x-card-component>
                        </div>
                    @endforeach
                    <div class="col-sm-6 col-xl-3">
                        <x-card-component
                            label="{{ __('Pending Debt') }}"
                            message="$ {{ number_format($data['financial_summary']['total_debt'], 2) }}"
                            end_text="{{ __('Across all units') }}"
                            icon="mdi mdi-alert-circle-outline text-danger"
                        ></x-card-component>
                    </div>
                </div>

                <div class="row mt-4">
                    <!-- Upcoming Assemblies -->
                    <div class="col-lg-6 col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">{{ __('Upcoming Assemblies') }}</h4>
                            </div>
                            <div class="box-body">
                                @if(count($data['upcoming_assemblies']) > 0)
                                    <div class="media-list media-list-hover">
                                        @foreach($data['upcoming_assemblies'] as $session)
                                            <div class="media">
                                                <div class="media-body">
                                                    <p><strong>{{ $session['title'] }}</strong></p>
                                                    <p class="text-muted"><i class="mdi mdi-calendar"></i> {{ \Carbon\Carbon::parse($session['scheduled_at'])->format('d/m/Y H:i') }}</p>
                                                    <p>{{ $session['location'] }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted text-center py-3">{{ __('No upcoming assemblies scheduled.') }}</p>
                                @endif
                                <div class="text-center mt-3">
                                    <a href="{{ route('assembly-sessions.index') }}" class="btn btn-primary-light btn-sm text-center">{{ __('Go to Assemblies') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Community Status -->
                    <div class="col-lg-6 col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">{{ __('Community Maintenance') }}</h4>
                            </div>
                            <div class="box-body">
                                <div class="row text-center mb-4">
                                    <div class="col-6">
                                        <h2 class="mb-0 text-info">{{ $data['community_status']['open_incidents'] }}</h2>
                                        <p class="text-muted mb-0">{{ __('Open Incidents') }}</p>
                                    </div>
                                    <div class="col-6">
                                        <h2 class="mb-0 text-success">{{ $data['community_status']['active_works'] }}</h2>
                                        <p class="text-muted mb-0">{{ __('Active Works') }}</p>
                                    </div>
                                </div>
                                <h5 class="mt-4 mb-2">{{ __('Latest Work Orders') }}</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Task') }}</th>
                                                <th>{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data['community_status']['latest_works'] as $work)
                                                <tr>
                                                    <td>{{ $work['title'] }}</td>
                                                    <td>
                                                        <span class="badge badge-sm badge-info">{{ ucfirst($work['status']) }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Bookings -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">{{ __('My Upcoming Bookings') }}</h4>
                            </div>
                            <div class="box-body">
                                @if(count($data['my_bookings']) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Area') }}</th>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data['my_bookings'] as $booking)
                                                    <tr>
                                                        <td>{{ $booking['common_area']['name'] }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($booking['start_time'])->format('d/m/Y H:i') }}</td>
                                                        <td><span class="badge badge-success">{{ ucfirst($booking['status']) }}</span></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <p class="text-muted">{{ __('You have no upcoming bookings.') }}</p>
                                        <a href="{{ route('bookings.create') }}" class="btn btn-info btn-sm mt-2">{{ __('Book Now') }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

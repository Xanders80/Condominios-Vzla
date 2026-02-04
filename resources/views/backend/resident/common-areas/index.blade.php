@extends('backend.main.index')
@push('title', __('Common Areas'))
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="me-auto">
                        <h3 class="page-title">{{ __('Common Areas') }}</h3>
                        <div class="d-inline-block align-items-center">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('coowner-dashboard') }}"><i
                                                class="mdi mdi-home-outline"></i></a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ __('Catalogue') }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('resident.common-areas.history') }}" class="btn btn-info btn-sm">
                            <i class="mdi mdi-history me-1"></i> {{ __('My Bookings') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    @forelse($areas as $area)
                        <div class="col-md-6 col-lg-4">
                            <div class="box box-bordered border-primary pull-up">
                                <div class="box-header with-border">
                                    <h4 class="box-title"><strong>{{ $area->name }}</strong></h4>
                                    <ul class="box-controls pull-right">
                                        <li><i class="mdi mdi-account-group text-primary"></i> <span
                                                class="badge badge-primary-light">{{ $area->max_occupancy ?? 'N/A' }}</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="box-body">
                                    <p class="text-muted fs-14 mb-20" style="height: 60px; overflow: hidden;">
                                        {{ $area->description ?? __('No description available.') }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h3 class="mb-0 text-dark">
                                                @if($area->booking_fee > 0)
                                                    {{ $area->currency }} {{ number_format($area->booking_fee, 2) }}
                                                    <small class="text-muted fs-12">/ {{ ucfirst($area->pricing_type) }}</small>
                                                @else
                                                    <span class="text-success font-weight-bold">{{ __('Free') }}</span>
                                                @endif
                                            </h3>
                                        </div>
                                        <a href="{{ route('resident.common-areas.calendar', $area->id) }}"
                                            class="btn btn-primary-light btn-sm">
                                            <i class="mdi mdi-calendar-plus me-1"></i> {{ __('Book Now') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center">
                            <div class="box p-50">
                                <i class="mdi mdi-tree mdi-48px text-muted"></i>
                                <h4 class="mt-20">{{ __('No common areas available at this time.') }}</h4>
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
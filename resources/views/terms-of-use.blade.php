@extends('backend.main.index')
@push('title', 'Privacy Policy')
@section('content')
    <div class="content-wrapper hold-transition">
        <div class="container-full">
            <div id="faqAccordion" class="container-fluid">
                <div class="row align-items-end">
                    <div class="col-12">
                        <div class="box bg-gradient-warning overflow-hidden pull-up">
                            <div class="box-body pe-0 ps-lg-50 ps-15 py-0">
                                <div class="row align-items-center">
                                    <div class="col-12 col-lg-8">
                                        <h1 class="fs-40 text-dark">¡{{ trans('Hola') . ' ' . $user->name }}!</h1>
                                        <p class="text-dark mb-0 fs-20">
                                            {!! trans('Welcome to') .
                                                ' ' .
                                                config('master.app.profile.name') .
                                                ', ' .
                                                trans('To ensure a transparent and secure experience, we encourage you to carefully read our Terms of Use') !!}
                                        </p>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <img src="{{ asset($template . '/images/svg-icon/color-svg/custom-1.svg') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card iq-document-card iq-doc-head">
                    <div class="tab-content">
                        <div class="tab-pane bd-heading-1 fade show active" id="content-accordion-prv" role="tabpanel">
                            <x-show-content-term></x-show-content-term>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<style>
    p {
        margin: 10px 0;
        /* Espaciado entre párrafos */
        line-height: 1.5;
        /* Altura de línea */
    }
</style>
<style>
    p {
        margin: 10px 0;
        /* Espaciado entre párrafos */
        line-height: 1.5;
        /* Altura de línea */
    }
</style>

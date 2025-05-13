@extends('backend.main.index')
@push('title', 'Privacy Policy')
@section('content')
    <div class="content-wrapper hold-transition">
        <div class="container-full">
            <div class="panel shadow-sm" style="border-radius: 10px;">
                <div class="container-fluid">
                    <div class="row align-items-end">
                        <div class="col-12">
                            <div class="box bg-gradient-warning overflow-hidden pull-up">
                                <div class="box-body pe-0 ps-lg-50 ps-15 py-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-lg-8">
                                            <h1 class="fs-40 text-dark">Hello {{ $user->name }}!</h1>
                                            <p class="text-dark mb-0 fs-20">
                                                Welcome to {!! config('master.app.profile.name') !!}, a CRUD Generator for Laravel
                                                {!! config('master.app.profile.laravel') !!} made easy and fast.
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
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="header-title">
                                        <h4 class="card-title">What is Lorem Ipsum?</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem
                                        Ipsum has been
                                        the industry's standard dummy text ever since the 1500s, when an unknown printer
                                        took a galley
                                        of type and scrambled it to make a type specimen book. It has survived not only five
                                        centuries,
                                        but also the leap into electronic typesetting, remaining essentially unchanged. It
                                        was
                                        popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum
                                        passages,
                                        and more recently with desktop publishing software like Aldus PageMaker including
                                        versions of
                                        Lorem Ipsum.</p>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="header-title">
                                        <h4 class="card-title">Why do we use it?</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p>It is a long established fact that a reader will be distracted by the readable
                                        content of a page
                                        when looking at its layout. The point of using Lorem Ipsum is that it has a
                                        more-or-less normal
                                        distribution of letters, as opposed to using 'Content here, content here', making it
                                        look like
                                        readable English. Many desktop publishing packages and web page editors now use
                                        Lorem Ipsum as
                                        their default model text, and a search for 'lorem ipsum' will uncover many web sites
                                        still in
                                        their infancy. Various versions have evolved over the years, sometimes by accident,
                                        sometimes on
                                        purpose (injected humour and the like). </p>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="header-title">
                                        <h4 class="card-title">Where does it come from?</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p>It is a long established fact that a reader will be distracted by the readable
                                        content of a page
                                        when looking at its layout. The point of using Lorem Ipsum is that it has a
                                        more-or-less normal
                                        distribution of letters, as opposed to using 'Content here, content here', making it
                                        look like
                                        readable English. Many desktop publishing packages and web page editors now use
                                        Lorem Ipsum as
                                        their default model text, and a search for 'lorem ipsum' will uncover many web sites
                                        still in
                                        their infancy. Various versions have evolved over the years, sometimes by accident,
                                        sometimes on
                                        purpose (injected humour and the like).</p>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="header-title">
                                        <h4 class="card-title">Where can I get some?</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p>It is a long established fact that a reader will be distracted by the readable
                                        content of a page
                                        when looking at its layout. The point of using Lorem Ipsum is that it has a
                                        more-or-less normal
                                        distribution of letters, as opposed to using 'Content here, content here', making it
                                        look like
                                        readable English. Many desktop publishing packages and web page editors now use
                                        Lorem Ipsum as
                                        their default model text, and a search for 'lorem ipsum' will uncover many web sites
                                        still in
                                        their infancy. Various versions have evolved over the years, sometimes by accident,
                                        sometimes on
                                        purpose (injected humour and the like).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@props(['image', 'isBackHome' => ''])

<div class="row align-items-end">
    <div class="col-12">
        <div class="box bg-gradient-warning overflow-hidden pull-up subpixel-antialiased">
            <div class="box-body pe-0 ps-lg-50 ps-15 py-0">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-8">
                        <div class="content-header">
                            <div class="d-flex align-items-center">
                                <div class="me-auto">
                                    <h2 class="page-title">
                                        <i class="{{ $page->icon }}"></i> {{ $page->title }}
                                    </h2>
                                    <div class="d-inline-block align-items-center">
                                        <nav>
                                            <ol class="breadcrumb">
                                                @if (!empty($isBackHome))
                                                    <li class="breadcrumb-item">
                                                        <a href="{!! url(config('master.app.url.backend') . '/' . $page->url) !!}">
                                                            <i class="mdi mdi-home-outline "></i>
                                                            {{ trans($isBackHome) }}
                                                        </a>
                                                    </li>
                                                @endif
                                                <li class="breadcrumb-item mt-1">
                                                    {{ trans('Welcome to') . ' ' . $page->subtitle }}
                                                </li>
                                            </ol>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <img src="{{ asset($template . '/images/svg-icon/color-svg/' . $image) }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('backend.main.menu.announcement')

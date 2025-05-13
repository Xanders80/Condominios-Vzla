@extends('backend.main.index')
@push('title', $page->title ?? 'Menu')

@section('content')
    <div class="content-wrapper hold-transition">
        <div class="container-full">
            @include('backend.main.menu.announcement')
            <section class="content">
                <x-show-header-breadcrumb image="custom-1.svg"></x-show-header-breadcrumb>

                <div class="row">
                    <div class="col-12">
                        <div class="box subpixel-antialiased p-4 shadow-lg">
                            <div class="box-header">
                                <h4 class="box-title">{{ trans('Content') }} {{ $page->title ?? trans('Page Name') }}</h4>
                                @if ($user->create)
                                    <button type="button" class="btn-action pull-right btn btn-success btn-sm"
                                        data-title="Add" data-action="create" data-url="{{ $page->url }}">
                                        <span class="mdi mdi-plus-circle "></span> {{ trans('Add') }}
                                    </button>
                                @endif
                            </div>
                            <div class="box-body bg-gradient-warning">
                                <div class="panel-container show">
                                    <div class="panel-content">
                                        {!! html()->form('POST', route($page->url . '.sorted'))->id('form-' . time())->acceptsFiles()->class('form form-horizontal')->open() !!}

                                        <div class="alert alert-success mb-0" role="alert">
                                            <div class="d-flex align-items-center">
                                                <div class="alert-icon width-3">
                                                    <span class="icon-stack icon-stack-sm">
                                                        <i
                                                            class="ni ni-list color-success-800 icon-stack-2x font-weight-bold"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-1">
                                                    <span class="h5 m-0 fw-700">
                                                        <i class="{{ $page->icon }}"></i> {{ trans('Menu List for') }}
                                                        {!! config('master.app.profile.name') !!}!
                                                    </span>
                                                    {{ trans('Please arrange the menu correctly') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col col-md-12 col-xl-12">
                                            <div class="panel-tag">
                                                <p class="loading text-center" style="display: none">
                                                    <button
                                                        class="btn btn-outline-dark rounded-pill waves-effect waves-themed text-center"
                                                        type="button" disabled>
                                                        <output class="spinner-border spinner-border-sm"
                                                            aria-hidden="true"></output>
                                                        {{ trans('Loading') }}
                                                    </button>
                                                </p>
                                                <div class="table-responsive">
                                                    <div class="dd p-10 fit-width" id="nestable" style="min-width: 100%;">
                                                        <div class="list">
                                                            {{--  Nestable Data --}}
                                                        </div>
                                                        <div>
                                                            {!! html()->textarea('sort')->id('nestable-output')->style('display:none')->class('form-control')->placeholder('Nestable Output') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-success">
                                            <a href="#" class="btn btn-sm btn-info-light submit-data">
                                                <i class="mdi mdi-content-save"></i> {{ trans('Save') }}
                                            </a>
                                        </div>

                                        {!! html()->hidden('function', 'sidebarMenu')->id('function') !!}
                                        {!! html()->form()->close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset($template . '/assets/vendor_components/nestable/jquery.nestable.js') }}"></script>
    <script src="{{ url('/js/' . $backend . '/' . $page->code . '/datatable.js') }}"></script>
    <script src="{{ url('/js/' . $backend . '/js/jquery-crud.js') }}"></script>
    <script>
        $(document).on("keyup", "#title", function() {
            let title = $(this).val();
            let value = title.replace(/ /g, "-").toLowerCase();
            $("#url").val(value);
            $("#code").val(value);
        });
    </script>
@endpush

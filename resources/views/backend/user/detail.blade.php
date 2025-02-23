@extends('backend.main.index')
@push('title', $page->title ?? 'User')

@section('content')
    <div class="content-wrapper hold-transition">
        <div class="container-full">
            <section class="content">
                <x-show-header-breadcrumb image="custom-1.svg"></x-show-header-breadcrumb>

                <div class="row">
                    <div class="col-12">
                        <div class="box" style="border-radius: 10px;">
                            <div class="box-header">
                                <h4 class="box-title">{{ trans('Content') }} {{ $page->title ?? trans('Page Name') }}</h4>
                            </div>
                            <div class="box-body bg-gradient-warning">
                                <div class="panel-body bg-white" style="border-radius: 10px;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-show-span condition=true dataUser="{{ $data->name }}"
                                                label="{{ trans('Name') }}" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-show-span condition=true dataUser="{{ $data->email }}"
                                                label="{{ trans('Email') }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-show-span condition=true dataUser="{{ $data->level->name }}"
                                                label="{{ trans('Use Access Level') }}" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-show-span condition=true dataUser="{{ $data->access_group->name }}"
                                                label="{{ trans('Use Access Group') }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger pull-right pull-up" onclick="window.close();">
                            <span class="mdi mdi-cancel "></span> {{ trans('Close Page') }}
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

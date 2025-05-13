@extends('backend.main.index')
@push('title', $page->title ?? 'Announcement')

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
                                    <div class="end-date">
                                        <span class="mdi mdi-calendar-today "></span>
                                        <small class="sidetitle">{{ trans('Effective Date') }}:
                                            {{ date('d M Y', strtotime($data->start)) }}
                                            {{ trans('to') }} {{ date('d M Y', strtotime($data->end)) }}
                                            <span class="ti-alarm-clock font-bold"></span> {{ trans('time left') }} <span
                                                id="countdown"></span>
                                        </small>
                                    </div>

                                    <div class="urgency">
                                        <span class="mdi mdi-alert-octagram "></span> {{ trans('Announcement Urgency') }}:
                                        <span class="badge badge-{!! config('master.content.announcement.color.' . $data->urgency) !!}">
                                            {!! config('master.content.announcement.status.' . $data->urgency) !!}
                                        </span>
                                    </div>

                                    <div class="box-comments p-10 mt-3">
                                        {!! $data->content !!}
                                    </div>

                                    <div class="file p-10 mt-3">
                                        <span class="mdi mdi-paperclip "></span> {{ trans('Supporting Files') }}:
                                        @if (!$data->file->isEmpty())
                                            <ul>
                                                @foreach ($data->file as $file)
                                                    <li>
                                                        <a href="{{ $file->link_download }}" class="mdi mdi-cloud-download "
                                                            aria-label="Descargar archivo"></a> |
                                                        <a href="{{ $file->link_stream }}" target="_blank"
                                                            class="mdi mdi-eye text-info" aria-label="Ver archivo"></a> |
                                                        {{ $file->file_name }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="badge badge-danger">{{ trans('No files available') }}</span>
                                        @endif
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
@push('js')
    <script>
        $(document).ready(function() {
            $(function() {
                initializeCountdown(new Date().toISOString(), "{{ $data->end }}", 'countdown');
            });
        });
    </script>
@endpush

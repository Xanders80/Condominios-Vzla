@extends('backend.main.index')
@push('title', $page->title ?? 'Level')

@section('content')
    <div class="content-wrapper hold-transition">
        <div class="container-full">
            @include('backend.main.menu.announcement')
            <section class="content">
                <x-show-header-breadcrumb image="custom-1.svg" isBackHome='FAQ'></x-show-header-breadcrumb>

                <div class="row">
                    <div class="col-12">
                        <div class="box subpixel-antialiased p-4 shadow-lg">
                            <div class="box-header">
                                <h4 class="box-title">{{ trans('Content') }} {{ $page->title ?? trans('Page Name') }}</h4>
                            </div>
                            <div class="box-body bg-gradient-warning shadow">
                                <div class="box" style="border-radius: 10px;">

                                    <div class="p-2">{!! $data->description !!}</div>

                                    @if ($data->file && $data->file->exists())
                                        <div class="form-group text-center">
                                            @switch($data->file->type)
                                                @case('image')
                                                    <img src="{{ url($data->file->link_stream) }}" class="img-fluid img-thumbnail"
                                                        alt="{{ $data->file->name }}" style="width: 50%">
                                                @break

                                                @case('video')
                                                    <video width="320" controls>
                                                        <source src="{{ url($data->file->link_stream) }}" type="video/mp4">
                                                        {{ trans('Your browser does not support the video tag') }}
                                                    </video>
                                                @break

                                                @case('file')
                                                    <object data="{{ url($data->file->link_stream) }}" type="application/pdf"
                                                        width="100%" height="600px">
                                                        <p>{{ trans('Alternative text - include a link') }} <a
                                                                href="{{ url($data->file->link_stream) }}">{{ trans('to the PDF!') }}</a>
                                                        </p>
                                                    </object>
                                                @break

                                                @case('audio')
                                                    <audio controls>
                                                        <source src="{{ url($data->file->link_stream) }}" type="audio/mpeg">
                                                        {{ trans('Your browser does not support the audio element') }}
                                                    </audio>
                                                @break

                                                @default
                                                    <a href="{{ url($data->file->link_stream) }}"
                                                        target="_blank">{{ $data->file->name }}</a>
                                            @endswitch
                                        </div>
                                    @endif

                                    <div class="box p-2 text-end">
                                        <p>{{ trans('Did this answer help you?') }}</p>
                                        <ul class="list-inline">
                                            <li>
                                                <a href="#" class="link-black text-sm send-response" data-code="yes">
                                                    <i class="margin-r-5 mdi mdi-thumb-up "></i>
                                                    {{ trans('Yes, It Helped') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="link-black text-sm send-response" data-code="no">
                                                    <i class="margin-r-5 mdi mdi-thumb-down "></i> {{ trans('No') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    @if ($data->family->count() > 0)
                                        <div class="box p-2">
                                            <h5>{{ trans('Related Questions:') }}</h5>
                                            <ul>
                                                @foreach ($data->family()->whereNot('id', $data->id)->cursor() as $faq)
                                                    <li>
                                                        <a href="{!! url(config('master.app.url.backend') . '/question/' . $faq->id) !!}">{{ trans($faq->title) }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger pull-right pull-up"
                            onclick="if (window.history.length > 1) { window.history.back(); } else { window.close(); }">
                            <span class="mdi mdi-backspace "></span> {{ trans('Close') }}
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // Send view count after 5 seconds
        setTimeout(function() {
            $.post('{!! url(config('master.app.url.backend') . '/question/viewer') !!}', {
                _token: '{!! csrf_token() !!}',
                id: "{!! $data->id !!}"
            }).done(function(data) {
                console.log(data);
            });
        }, 5000);

        // Handle response button clicks
        $('.send-response').on('click', function() {
            const code = $(this).data('code');
            resetResponseIcons();
            updateResponseIcon(code);
            showResponseMessage();
            sendResponse(code);
        });

        function resetResponseIcons() {
            $('[data-code="yes"]').find('i').removeClass('mdi mdi-thumb-up ').addClass('mdi mdi-thumb-up-outline ');
            $('[data-code="no"]').find('i').removeClass('mdi mdi-thumb-down ').addClass('mdi mdi-thumb-down-outline ');
        }

        function updateResponseIcon(code) {
            const iconClass = code === 'yes' ? 'mdi mdi-thumb-up-outline ' : 'mdi mdi-thumb-down ';
            const iconClassO = code === 'yes' ? 'mdi mdi-thumb-up ' : 'mdi mdi-thumb-down-outline ';
            $(this).find('i').removeClass(iconClassO).addClass(iconClass);
        }

        function showResponseMessage() {
            Swal.fire({
                title: "{{ trans('Thank You!') }}",
                text: "{{ trans('We appreciate your feedback!') }}",
                icon: "success",
                button: "OK",
                showClass: {
                    popup: `
                    animate__animated
                    animate__fadeInUp
                    animate__faster
                    `
                },
                hideClass: {
                    popup: `
                    animate__animated
                    animate__fadeOutDown
                    animate__faster
                    `
                }
            });
        }

        function sendResponse(code) {
            $.ajax({
                url: '{!! url(config('master.app.url.backend') . '/question/response') !!}',
                type: 'POST',
                data: {
                    _token: '{!! csrf_token() !!}',
                    code: code,
                    id: "{!! $data->id !!}"
                },
                beforeSend: function() {
                    $('.send-response').attr('disabled', true);
                },
                success: function() {
                    $('.send-response').attr('disabled', false);
                },
                error: function() {
                    $('.send-response').attr('disabled', false);
                }
            });
        }
    </script>
@endpush

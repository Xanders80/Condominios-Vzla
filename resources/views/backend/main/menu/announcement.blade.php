@if ($page && $page->announcement()->count() > 0)
    <div id="content-announcement" class="content mb-0">
        @foreach ($page->announcement as $announcement)
            <div id="alert-content-{!! $announcement->id !!}" class="box box-inverse bg-{!! config('master.content.announcement.color.' . $announcement->urgency) !!}"
                data-announcement-id="{!! $announcement->id !!}" data-announcement-end="{{ $announcement->end }}">
                <div class="box-header with-border">
                    <h4 class="box-title"><span class="mdi mdi-bullhorn mdi-36px"></span>
                        <strong> {{ $announcement->title }}</strong>
                        <small class="sidetitle">{{ trans('Effective Date') }}:
                            {{ date('d M Y', strtotime($announcement->start)) }}
                            {{ trans('to') }} {{ date('d M Y', strtotime($announcement->end)) }}
                            <span class="mdi mdi-timer font-bold"></span> {{ trans('time left') }} <span
                                id="countdown-{!! $announcement->id !!}"></span>
                        </small>
                    </h4>
                    <div class="box-tools pull-right">
                        <ul class="box-controls">
                            <li>
                                <a class="close-alert btn-close" data-code="{!! $announcement->id !!}" href="#">
                                    <span class="sr-only">Cerrar</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="box-body box-shadowed box-outline-{!! config('master.content.announcement.color.' . $announcement->urgency) !!} text-dark">
                    {!! \App\support\Helper::sortText($announcement->content, 1000) !!}
                    <div class="pull-right">
                        <a href="{{ $announcement->link }}" target="_blank"
                            class="btn btn-sm btn-{!! config('master.content.announcement.color.' . $announcement->urgency) !!}">{{ trans('Read More') }}</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @push('js')
        <script>
            $(document).ready(function() {
                let content = $('#content-announcement');

                // Eliminar anuncios basados en cookies al cargar la página
                $('.box[data-announcement-id]').each(function() {
                    var announcementId = $(this).data('announcement-id');
                    if (document.cookie.indexOf('content-' + announcementId) > -1) {
                        $(this).remove();
                        if (content.children().length === 0) {
                            content.remove();
                        }
                    }
                });

                // Cerrar el anuncio y establecer la cookie
                $('.close-alert').click(function() {
                    var announcementId = $(this).data('code');
                    document.cookie = 'content-' + announcementId + '=hide; path=/;'; // Agregado path=/
                    $(this).closest('.box').remove(); // Eliminar el anuncio
                    if (content.children().length === 0) {
                        content.remove();
                    }
                });

                // Inicializar los contadores al cargar la página
                $('.box[data-announcement-end]').each(function() {
                    var announcementId = $(this).data('announcement-id');
                    var endDate = $(this).data('announcement-end');
                    initializeCountdown(new Date().toISOString(), endDate, 'countdown-' + announcementId);
                });
            });
        </script>
    @endpush
@endif

<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class="form-group">
                <x-input-label id="title" label="{{ trans('Title') }}" onlyLabel="true" /> {!! $data->title !!}
            </div>
            <div class="form-group">
                <x-input-label id="title" label="{{ trans('Topic Menu') }}" onlyLabel="true" />
                {!! $data->menu->title !!}
            </div>
            <div class="form-group">
                <x-input-label id="description" label="{{ trans('Description') }}" onlyLabel="true" />
                {!! $data->description !!}
            </div>
            <div class="form-group">
                <span class="mdi mdi-eye-outline mdi-18px text-info"></span> {!! $data->visitors !!} |
                <span class="mdi mdi-thumb-up-outline mdi-18px text-primary"></span> {!! $data->like !!} |
                <span class="mdi mdi-thumb-down-outline mdi-18px text-danger"></span> {!! $data->dislike !!} |
                <span class='badge {{ $data->publish ? 'badge-success' : 'badge-danger' }}'>
                    {{ $data->publish ? trans('Displayed') : trans('Not Displayed') }}
                </span>
            </div>
            @if ($data->file && $data->file->exists())
                <div class="form-group text-center">
                    @switch($data->file->type)
                        @case('image')
                            {!! html()->img(url($data->file->link_stream), $data->file->name)->class('img-fluid img-thumbnail')->style('width: 50%') !!}
                        @break

                        @case('video')
                            <video width="320" controls>
                                <source src="{{ url($data->file->link_stream) }}" type="video/mp4">
                                {{ trans('Your browser does not support the video tag') }}
                            </video>
                        @break

                        @case('file')
                            <object data="{{ url($data->file->link_stream) }}" type="application/pdf" width="100%"
                                height="600px">
                                <p>{{ trans('Alternative text - include a link') }} <a
                                        href="{{ url($data->file->link_stream) }}">{{ trans('to the PDF!') }}</a></p>
                            </object>
                        @break

                        @case('audio')
                            <audio controls>
                                <source src="{{ url($data->file->link_stream) }}" type="audio/mpeg">
                                {{ trans('Your browser does not support the audio element') }}
                            </audio>
                        @break

                        @default
                            <a href="{!! url($data->file->link_stream) !!}" target="_blank">{!! $data->file->name !!}</a>
                    @endswitch
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .modal-lg {
        max-width: 1000px !important;
    }
</style>

<script>
    $('.submit-data').hide();
    $('.modal-title').html(
        '<i class="mdi mdi mdi-eye mdi-24px text-info"></i> - {{ trans('Show Data') }} {{ $page->title }}');
</script>

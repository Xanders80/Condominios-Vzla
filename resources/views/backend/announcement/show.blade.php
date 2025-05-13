<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <x-input-label id="title" label="{{ trans('Title') }}" onlyLabel="true" />:
                    {{ $data->title }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <x-input-label id="menu_title" label="{{ trans('Target Menu') }}" onlyLabel="true" />:
                    {{ $data->menu->title }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <x-input-label id="urgency" label="{{ trans('Importance Level') }}" onlyLabel="true" />:
                    <span class="badge badge-{{ config('master.content.announcement.color.' . $data->urgency) }}">
                        {{ config('master.content.announcement.status.' . $data->urgency) }}
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <x-input-label id="publish" label="{{ trans('Publication Status') }}" onlyLabel="true" />:
                    <span class='badge {{ $data->publish ? 'badge-success' : 'badge-danger' }}'>
                        {{ $data->publish ? trans('Displayed') : trans('Not Displayed') }}
                    </span>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <x-input-label id="effective_date" label="{{ trans('Effective Date') }}" onlyLabel="true" />:
                    <small class="sidetitle">{{ trans('Effective Date') }}:
                        {{ date('d M Y', strtotime($data->start)) }}
                        {{ trans('to') }} {{ date('d M Y', strtotime($data->end)) }}
                        <span class="mdi mdi-timer font-bold"></span> {{ trans('time left') }} <span
                            id="countdown"></span>
                    </small>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <x-input-label id="content" label="{{ trans('Announcement Content') }}" onlyLabel="true" />:
                    <div class="p-10 shadow-sm">
                        {!! $data->content !!}
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <x-input-label id="file" label="{{ trans('Supporting Files') }}" onlyLabel="true" />:
                    @if (!$data->file->isEmpty())
                        <ul>
                            @foreach ($data->file as $file)
                                <li>
                                    <a href="{{ $file->link_download }}" class="mdi mdi-cloud-download "></a> |
                                    <a href="{{ $file->link_stream }}" target="_blank"
                                        class="mdi mdi-eye text-info"></a> |
                                    {{ $file->file_name }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <span class="badge badge-danger">{{ trans('No files available') }}</span>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <x-input-label id="parent" label="{{ trans('Relation') }}" onlyLabel="true" />:
                    @if ($data->parent)
                        <a href="#" type="button" class="btn-action" data-title="{{ trans('Detail') }}"
                            data-action="show" data-url="announcement" data-id="{{ $data->parent_id }}"
                            title="{{ trans('Show') }}"> {{ $data->parent->title }}</a>
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-lg {
        max-width: 1000px !important;
    }
</style>

<script>
    $(document).ready(function() {
        $('.submit-data').hide();
        $('.modal-title').html(
            '<i class="mdi mdi mdi-eye mdi-24px text-info"></i> - {{ trans('Show Data') }} {{ $page->title }}'
            );
        getNotification();

        $(function() {
            initializeCountdown(new Date().toISOString(), "{{ $data->end }}", 'countdown');
        });
    });
</script>

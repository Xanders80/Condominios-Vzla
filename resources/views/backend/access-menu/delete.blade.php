{!! html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() !!}
<x-body-delete>
    @if ($data->access_menu->isNotEmpty())
        @foreach ($data->access_menu as $key => $value)
            @if (!in_array($key, ['id', 'created_at', 'updated_at', 'deleted_at']))
                <div class="col-auto m-2">
                    <code>{{ $loop->iteration }}</code>
                    <span class="text-danger">{{ trans(':') }}</span>
                    <span class="text-info">{!! $value->menu->title !!}</span>
                </div>
            @endif
        @endforeach
        <div class="m-2">
            {{ trans('Total menu access: ') }} <b>{!! $data->access_menu->count() !!}</b><br>
            {{ trans('Total users: ') }} <b>{!! $data->users->count() !!}</b> {{ trans('people') }}
        </div>
    @else
        <p class="text-info">
            {{ trans('There is no menu access data to delete') }}
        </p>
        <p class="mt-2 text-info">
            {{ trans('Please delete the access group') }} <b class="text-uppercase">{!! $data->name !!}</b>
            <a class="link-danger" href="{!! url(config('master.app.url.backend') . '/access-group') !!}">{{ trans('here') }}</a>
            {{ trans('if it is not in use') }}
        </p>
        <script>
            $('.submit-data').hide();
        </script>
    @endif
</x-body-delete>

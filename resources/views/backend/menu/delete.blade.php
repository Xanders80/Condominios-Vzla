{!! html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() !!}
<x-body-delete>
    @foreach (collect(json_decode($data, true))->except(['id', 'created_at', 'updated_at']) as $key => $value)
        <p>
            <code>{{ $key }}</code>
            <span class="text-danger">:</span>
            <span class="text-info">{{ $value }}</span>
        </p>
    @endforeach

    <div class="mt-3">
        @if ($data->access_menu->count() > 0)
            <p>
                <span class="text-info">{{ trans('Menu used by') }}:</span>
                @foreach ($data->access_menu as $access)
                    <span class="badge badge-info">{{ $access->access_group->name }}</span>
                @endforeach
            </p>
            <p>
                <span
                    class="text-danger">{{ trans('If you delete this data the related access menu data will also be deleted') }}</span>
            </p>
        @endif
    </div>
</x-body-delete>

<x-input-checkbox id="{{ 'menu_' . $menu->id }}" name="menu_id[]" label="{{ trans($menu->title) }}"
    dataUser="{{ in_array($menu->id, $data->access_menu()->pluck('menu_id')->toArray()) }}" valor="{{ $menu->id }}"
    class="checkbox" onlyLabel="true" />

@if ($menu->children->isNotEmpty())
    <ul class="list-group">
        @foreach ($menu->children as $child)
            @include('backend.access-menu.menu.submenu', ['child' => $child, 'data' => $data])
        @endforeach
    </ul>
@endif

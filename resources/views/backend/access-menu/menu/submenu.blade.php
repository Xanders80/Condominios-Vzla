<li class="list-group-item no-border">
    <x-input-checkbox id="{{ 'menu_' . $child->id }}" name="menu_id[]" label="{{ trans($child->title) }}"
        dataUser="{{ in_array($child->id, $data->access_menu()->pluck('menu_id')->toArray()) }}"
        valor="{{ $child->id }}" class="checkAll checkbox" />

    @if ($child->children->isNotEmpty())
        <ul class="list-group">
            @foreach ($child->children as $subChild)
                @include('backend.access-menu.menu.submenu', ['child' => $subChild, 'data' => $data])
            @endforeach
        </ul>
    @endif
</li>

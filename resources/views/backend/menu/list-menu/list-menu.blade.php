<ol class="dd-list">
    @foreach($menu as $item)
        <li class="dd-item dd3-item" data-id="{{ $item->id }}">
            <div class="dd-handle dd3-handle"></div>
            <div class="dd3-content" title="{{ $item->title ?? '' }}">
                @if($item->icon)
                    <span class="{{ $item->icon }}"></span>
                @endif
                {{ $item->title }}

                <div class="pull-right btn-group">
                    <button type="button" class="btn-action btn btn-xs btn-outline"
                            title="{{ trans('Edit menu item') }}"
                            data-title="{{ trans('Edit') }}"
                            data-action="edit"
                            data-url="{{ $page->url }}"
                            data-id="{{ $item->id }}">
                        <i class="mdi mdi-pencil-box-outline text-warning"></i>
                    </button>
                    <button type="button" class="btn-action btn btn-xs btn-outline"
                            title="{{ trans('Delete menu item') }}"
                            data-title="{{ trans('Delete') }}"
                            data-action="delete"
                            data-url="{{ $page->url }}"
                            data-id="{{ $item->id }}">
                        <i class="mdi mdi-delete text-danger"></i>
                    </button>
                </div>
            </div>
            @include($backend.'.menu.list-menu.list-menu', ['menu' => $item->children])
        </li>
    @endforeach
</ol>

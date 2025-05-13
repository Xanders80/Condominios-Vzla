<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            @foreach (collect($data['menu'])->whereNull('parent_id')->sortBy('sort') as $menu)
                <div class="col-auto">
                    <div class="box box-shadowed">
                        <div class="box-body bg-gradient-warning">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    @include('backend.access-menu.menu.menu', [
                                        'menu' => $menu,
                                        'data' => $data,
                                    ])
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
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
        $('input[type="checkbox"]').prop('disabled', true);
    });
</script>

{!! html()->modelForm($data, 'PUT', route($page->url . '.update', $data->id))->id('form-create-' . $page->code)->acceptsFiles()->class('form form-horizontal')->open() !!}

<div class="row">
    @foreach (collect($data['menu'])->whereNull('parent_id')->sortBy('sort') as $menu)
        <div class="col-xl-3 col-md-6 col-12">
            <div class="box box-shadowed">
                <div class="box-header">
                    <x-input-checkbox id="{{ 'parent_' . $menu->id }}" name="checkAll" label="{{ trans('Select All') }}"
                        dataUser="{{ $data->publish }}" class="checkbox checkAll" />
                </div>
                <div class="box-body bg-gradient-warning">
                    <ul class="list-group">
                        <li class="list-group-item">
                            @include('backend.access-menu.menu.menu', ['menu' => $menu, 'data' => $data])
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @endforeach
</div>

{!! html()->hidden('access_group_id', $data->id)->id('access_group_id') !!}
{!! html()->hidden('function', 'sidebarMenu()')->id('function') !!}
{!! html()->form()->close() !!}

<style>
    .modal-lg {
        max-width: 1300px !important;
    }
</style>

<script>
    $('.modal-title').html(
        '<i class="mdi mdi-tooltip-edit mdi-24px text-warning"></i> - {{ trans('Edit Data') }} {{ $page->title }}');
    $('.submit-data').html('<i class="mdi mdi-content-save "></i> {{ trans('Save') }} ');


    $(document).on('click', '.checkAll', function() {
        let checkboxes = $(this).closest('.box-header').siblings('.box-body').find('input[type="checkbox"]');
        checkboxes.prop('checked', this.checked);
    });
</script>

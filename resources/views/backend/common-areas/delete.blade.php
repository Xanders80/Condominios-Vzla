{{ html()->form('DELETE', route($page->code . '.destroy', $data->id))->id('form-delete-' . $page->code)->class('form form-horizontal')->open() }}
<x-body-delete>
    <strong>{{ trans('Title') }}:</strong> {{ $data->title }}<br>
    <strong>{{ trans('Capacity') }}:</strong> {{ $data->capacity }} {{ __('Persons') }}<br>
    <div class="alert alert-warning">
        {{ trans('Deleting this area will also affect historical bookings.') }}
    </div>
</x-body-delete>

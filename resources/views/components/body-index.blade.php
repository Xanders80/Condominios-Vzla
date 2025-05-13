@props([
    'showAdd' => false,
    'columns' => '',
    'month' => '',
    'filteredMonths' => [],
    'years' => [],
    'dataArray' => [],
])
<div class="content-wrapper hold-transition">
    <div class="container-full">
        <section class="content">
            <x-show-header-breadcrumb image="custom-1.svg"></x-show-header-breadcrumb>

            <div class="row">
                <div class="col-12">
                    <div class="box subpixel-antialiased p-4 shadow-lg">
                        <div class="box-header d-flex align-items-center justify-content-between">
                            <h4 class="box-title my-4">{{ trans('Content') }} {{ $page->title ?? trans('Page Name') }}
                            </h4>
                            @if ($showAdd)
                                <div class="d-flex align-items-center">
                                    @if ($page->title === 'Pagos')
                                        <!-- Select para los meses -->
                                        <div class="form-group me-2">
                                            <x-input-select id="month" label="{{ trans('Month/Year') }}">
                                                {!! html()->select('month', $filteredMonths, $month)->class('form-control')->id('month')->placeholder(trans('Select a month'))->required() !!}
                                                <select class="form-control" id="year" name="year">
                                                    @foreach ($years as $year)
                                                        <option value="{{ $year }}">{{ $year }}</option>
                                                    @endforeach
                                                </select>
                                            </x-input-select>
                                        </div>
                                    @endif
                                    <x-button-button class="btn-action btn btn-success btn-sm flex text-nowrap my-4"
                                        data-title="Add" data-action="create" data-url="{{ $page->url }}">
                                        <span class="mdi mdi-plus-circle "></span> {{ trans('Add') }}
                                    </x-button-button>
                                </div>
                            @endif
                        </div>
                        <div class="box-body bg-gradient-warning">
                            @if ((auth()->user()->level->code !== 'user' && !empty($dataArray)) || $page->title === 'Pagos')
                                <div id="cardsContainer"
                                    class="row d-flex justify-content-between align-items-center flex-sm-row flex-column gap-10"
                                    style="position: relative;">
                                    @foreach ($dataArray as $input)
                                        <div class="{{ $columns }}">
                                            <x-card-component label="{{ $input['label'] }}"
                                                message="{{ $input['message'] }}"
                                                sub_message="{{ $input['sub_message'] }}"
                                                end_text="{{ $input['end_text'] }}" icon="{{ $input['icon'] }}">
                                            </x-card-component>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <x-build-data-table>
                                {{ $slot }}
                            </x-build-data-table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@push('js')
    <script src="{{ url('/js/' . $backend . '/' . $page->code . '/datatable.js') }}"></script>
@endpush

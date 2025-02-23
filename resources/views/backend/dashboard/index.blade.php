@extends('backend.main.index')
@push('title', 'Dashboard')
@section('content')
    <div class="content-wrapper hold-transition">
        <div class="container-full">
            <section class="content">
                <div class="row align-items-end">
                    <div class="col-12">
                        <div class="box bg-gradient-warning overflow-hidden pull-up">
                            <div class="box-body pe-0 ps-lg-50 ps-15 py-0">
                                <div class="row align-items-center">
                                    <div class="col-12 col-lg-8">
                                        <h1 class="fs-40 text-dark">¡{{ trans('Hola') . ' ' . $user->name }}!</h1>
                                        <p class="text-dark mb-0 fs-20">
                                            {!! trans('Welcome to') .
                                                ' ' .
                                                config('master.app.profile.name') .
                                                ', ' .
                                                trans('Condominium management system to Salamanca urbanization condominiums') !!}
                                        </p>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <img src="{{ asset($template . '/images/svg-icon/color-svg/custom-14.svg') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('backend.main.menu.announcement')
                </div>
                <!-- Select para los años -->
                <div class="form-group col-sm-2 col-xl-2">
                    <x-input-label id="year" label="{{ trans('Select a Year') }}" onlyLabel="true" />
                    <select class="form-control" id="year" name="year">
                        @foreach ($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="cardsContainer"
                    class="row d-flex justify-content-between align-items-center flex-sm-row flex-column gap-10"
                    style="position: relative;">
                    @foreach ($data as $input)
                        <div class="col-sm-6 col-xl-3 d-flex flex-sm-column flex-row align-items-start justify-content-between">
                            <x-card-component label="{{ $input['label'] }}" message="{{ $input['message'] }}"
                                end_text="{{ $input['end_text'] }}" icon="{{ $input['icon'] }}"></x-card-component>
                        </div>
                    @endforeach
                </div>

                <div class="row align-items-end">
                    <div class="col-6 mt-4 mb-3">
                        <x-card-chart id="chart-line-pay-all" title="{{ trans('Total Payments per Year') }}"
                            subtitle="{{ trans('Lifetime statistics') }}" message="{{ trans('just updated') }}"
                            sub_message="{{ trans('schedule') }}"></x-card-chart>
                    </div>
                    <div class="col-6 mt-4 mb-3">
                        <x-card-chart id="chart-line-pay-month" title="{{ trans('Total Payments per Month') }}"
                            subtitle="{{ trans('Statistics. Year Select') }}" message="{{ trans('just updated') }}"
                            sub_message="{{ trans('schedule') }}"></x-card-chart>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ asset($template . '/assets/vendor_plugins/chart/chart.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const yearSelect = document.getElementById('year');
            const cardsContainer = document.getElementById('cardsContainer');
            const chartAll = initializeChart('chart-line-pay-all');
            const chartMonth = initializeChart('chart-line-pay-month');

            function updateCards() {
                const year = parseInt(yearSelect.value, 10); // Convierte el valor del año a entero

                fetch(`${window.location.href}/data-cards/${year}`)
                    .then(response => response.json())
                    .then(data => {
                        // Limpiar el contenedor de tarjetas
                        cardsContainer.innerHTML = '';
                        // Crear y agregar las nuevas tarjetas
                        data.forEach(input => {
                            const cardElement = document.createElement('div');
                            cardElement.className =
                                'col-sm-6 col-xl-3 flex-sm-row flex-row align-items-start justify-content-between';
                            cardElement.innerHTML = `
                                <x-card-component
                                    label="${input.label}"
                                    message="${input.message}"
                                    end_text="${input.end_text}"
                                    icon="${input.icon}"
                                ></x-card-component>
                            `;
                            cardsContainer.appendChild(cardElement);
                        });
                    })
                    .catch(error => console.error('Error al actualizar las tarjetas:', error));
            }

            // Escuchar cambios en el select del año
            yearSelect.addEventListener('change', function() {
                const year = this.value;
                updateCharts(year, chartAll, chartMonth);
                updateCards();
            });

            // Ejecutar updateCards al cargar la página
            updateCards();

            // Inicializar los gráficos con el año actual
            updateCharts(yearSelect.value, chartAll, chartMonth);
        });
    </script>
@endpush

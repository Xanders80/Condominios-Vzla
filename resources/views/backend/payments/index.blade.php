@extends('backend.main.index')
@push('title', $page->title ?? 'Payments')
@section('content')
    <x-body-index showAdd="{{ ($user->create && $unitCount !== 0) || auth()->user()->level->code !== 'user' }}"
        columns="col-sm-4 col-xl-4" month="{{ $month }}" :filteredMonths="$filteredMonths" :years="$years" :dataArray="$data">
        <th class="w-0">{{ __('N°') }}</th>
        <th>{{ __('Dweller') }}</th>
        <th>{{ __('Reference') }}</th>
        <th>{{ __('Amount') }}</th>
        <th>{{ __('Paid') }}</th>
        <th>{{ __('Confirmed') }}</th>
        <th>{{ __('Verified') }}</th>
        <th class="text-center w-0">{{ __('Action') }}</th>
    </x-body-index>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const monthSelect = document.getElementById('month');
            const yearSelect = document.getElementById('year');
            const cardsContainer = document.getElementById('cardsContainer');

            function updateCards() {
                const month = parseInt(monthSelect.value, 10);
                const year = parseInt(yearSelect.value, 10);

                fetch(`${window.location.href}/data-cards/${month}/${year}`)
                    .then(response => response.json())
                    .then(data => {
                        cardsContainer.innerHTML = '';
                        data.data.forEach(input => {
                            const cardElement = document.createElement('div');
                            cardElement.className =
                                'col-sm-6 col-xl-3 flex-sm-row flex-row align-items-start justify-content-between';
                            cardElement.innerHTML = `
                                <x-card-component
                                    label="${input.label}"
                                    message="${input.message}"
                                    sub_message=""
                                    end_text="${input.end_text}"
                                    icon="${input.icon}"
                                ></x-card-component>
                            `;
                            cardsContainer.appendChild(cardElement);
                        });
                    })
                    .catch(error => console.error('Error al actualizar las tarjetas:', error));
            }

            function updateMonths(year) {
                fetch(`${window.location.href}/get-months/${year}`)
                    .then(response => response.json())
                    .then(months => {
                        monthSelect.innerHTML = '';
                        months.forEach(month => {
                            const monthName = new Date(year, month - 1).toLocaleString('default', {
                                month: 'long'
                            });
                            const option = document.createElement('option');
                            option.value = month;
                            option.textContent = monthName;
                            monthSelect.appendChild(option);
                        });
                        updateCards();
                    })
                    .catch(error => console.error('Error al obtener los meses:', error));
            }

            // Check if monthSelect and yearSelect is not null before adding event listener
            if (monthSelect && yearSelect) {
                monthSelect.addEventListener('change', updateCards);
                yearSelect.addEventListener('change', function() {
                    const selectedYear = parseInt(yearSelect.value, 10);
                    updateMonths(selectedYear);
                });
                const defaultYear = parseInt(yearSelect.value, 10);
                updateMonths(defaultYear);
                updateCards();
            }
        });
    </script>
@endpush

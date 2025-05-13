{{--<script>--}}
    $(function () {
        // negative block
        $(document).on('input', '.negative-block', function (e) {
            if (e.keyCode === 109 || e.keyCode === 189) e.preventDefault();
            if ($(this).val() < 0) $(this).val(0);
        });
        // end negative block

        //remove all whitespace
        $(document).on('input', '.remove-whitespace', function (e) {
            $(this).val($(this).val().replace(/\s/g, ''));
        });
        $(document).on('change', '.remove-whitespace', function (e) {
            $(this).val($(this).val().replace(/\s/g, ''));
        });
        // end remove all whitespace

        // remove whitespace on first and last
        $(document).on('input', '.remove-whitespace-first-last', function (e) {
            $(this).val($(this).val().replace(/^\s+/, '').replace(/\s+$/, ''));
        });
        $(document).on('change', '.remove-whitespace-first-last', function (e) {
            $(this).val($(this).val().replace(/^\s+/, '').replace(/\s+$/, ''));
        });
        // end remove whitespace on first and last

        //regex only number
        $(document).on('input', '.only-number', function (e) {
            $(this).val($(this).val().replace(/[^0-9]/g, ''));
        });
        $(document).on('change', '.only-number', function (e) {
            $(this).val($(this).val().replace(/[^0-9]/g, ''));
        });
        // end regex only number

        //regex only alphabet
        $(document).on('input', '.only-alphabet', function (e) {
            $(this).val($(this).val().replace(/[^a-zA-Z]/g, ''));
        });
        $(document).on('change', '.only-alphabet', function (e) {
            $(this).val($(this).val().replace(/[^a-zA-Z]/g, ''));
        });
        // end regex only alphabet

        // format rupiah
        $(document).on('input', '.format-rupiah', function (e) {
            $(this).val(formatRupiah($(this).val(), 'Rp. '));
        });
        $(document).on('change', '.format-rupiah', function (e) {
            $(this).val(formatRupiah($(this).val(), 'Rp. '));
        });
        // end format rupiah

        //sort_text
        $('.sort_text').html(function (i, html) {
            let text = $(this).text();
            let text_sort = text.substring(0, 20);
            if (text.length > 20) {
                $(this).html('<i class="ti-user text-muted me-2"></i> ' + text_sort + '...');
            }
        });
        //end sort_text

        // Input File Validation
        $(window.document).on('change', 'input[type="file"]', function () {
            clearError();
            let id = $(this).attr('id');
            let size = $(this).data('size') || 0;
            let max_byte = size * 1024;
            const accept = $(this).attr('accept').split(',').map(item => item.trim());
            const fileInput = $(this)[0];

            $.each(fileInput.files, function (index, value) {
                if (value) {
                    if (size !== 0 && value.size > max_byte) {
                        errorBuilder({ [id]: `File ${value.name} exceeds the maximum limit. ${size / 1024} MB` });
                        fileInput.value = '';
                        return false;
                    }

                    let fileType = value.type.split('/');
                    let check = false;

                    accept.forEach(function (item) {
                        if (item.endsWith("/*")) {
                            // Match the main type (e.g., image, video) and allow any subtype
                            let mainType = item.split('/')[0];
                            if (fileType[0] === mainType || mainType === '*') {
                                check = true;
                            }
                        } else {
                            // Match both main type and subtype
                            let ty = item.split('/');
                            if (ty.length === 2 && ty[0] === fileType[0] && (ty[1] === fileType[1] || ty[1] === '*')) {
                                check = true;
                            }
                        }
                    });

                    if (!check) {
                        errorBuilder({ [id]: `File ${value.name} does not match the allowed file type.` });
                        fileInput.value = '';
                        return false;
                    }
                }
            });
        });
    });
    // End Input File Validation

    // Function to format bolívares
    function formatBolivares(number, prefix = 'Bs. ') {
        const numberString = number.toString().replace(/[^0-9]/g, '');
        const split = numberString.split(',');
        const bolivares = split[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        const result = split[1] ? `${bolivares},${split[1]}` : bolivares;
        return prefix + result;
    }
    // End Function to format bolívares

    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
    }

    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    // Show small notification
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-toggle="push-menu"]').forEach(function (element) {
            element.addEventListener('click', function () {
                if (document.body.classList.contains('sidebar-collapse')) {
                    localStorage.setItem('sidebar', 'false');
                    $('.small-notify').addClass('hide');
                } else {
                    localStorage.setItem('sidebar', 'true');
                    $('.small-notify').removeClass('hide');
                }
            });
        });

        if (localStorage.getItem('sidebar') === 'true') {
            document.body.classList.add('sidebar-collapse');
            $('.small-notify').addClass('hide');
        } else {
            document.body.classList.remove('sidebar-collapse');
            $('.small-notify').removeClass('hide');
        }
    });

    // public/js/form-helpers.js
    function initializeCountdown(startDate, endDate, countdownElementId) {
        "use strict";

        // Convertir fechas a milisegundos
        let start_date = new Date(startDate || new Date()).getTime();
        let end_date = new Date(endDate || new Date()).getTime();

        // Verificar si end_date es menor a la fecha actual
        let current_date = new Date().getTime();
        let countdown = document.getElementById(countdownElementId);

        if (end_date < current_date) {
            // Mostrar mensaje de "Tiempo vencido" en color rojo
            countdown.innerHTML = '<span style="color: red;">({{ trans('Time Expired') }})</span>';
        } else {
            // Variables for time units
            let days, hours, minutes, seconds;

            setInterval(function () {
                // **CORRECCIÓN: Calcular la diferencia entre end_date y la fecha actual**
                let current_date = new Date().getTime();
                let seconds_left = Math.floor((end_date - current_date) / 1000);

                if (seconds_left < 0) {
                    // Si el tiempo ha expirado durante el intervalo, mostrar el mensaje
                    countdown.innerHTML = '<span style="color: red;">({{ trans('Time Expired') }})</span>';
                    return; // Detener el intervalo
                }

                // do some time calculations
                let days = Math.floor(seconds_left / 86400);
                seconds_left %= 86400;

                let hours = Math.floor(seconds_left / 3600);
                seconds_left %= 3600;

                let minutes = Math.floor(seconds_left / 60);
                let seconds = seconds_left % 60;

                // Formatea la cadena de cuenta regresiva y establece el valor del tag
                countdown.innerHTML = '(' + days + ' D - ' + hours + ' H - ' + minutes + ' m - ' + seconds + ' s)';
            }, 1000);
        }
    }

    function updateCharts(year, chartAll, chartMonth) {
        // Obtener los datos acumulados para el gráfico de por vida
        fetch(`${window.location.href}/payment-by-year`)
            .then(response => response.json())
            .then(data => {
                // Actualizar el gráfico de pagos acumulados
                chartAll.data.labels = data.labels; // Años
                chartAll.data.datasets[0].data = data.data; // Valores acumulados
                chartAll.update();
            })
            .catch(error => console.error('Error fetching lifetime data:', error));

        // Obtener los datos del servidor para el año seleccionado
        fetch(`${window.location.href}/payment-by-month/${year}`)
            .then(response => response.json())
            .then(data => {
                // Actualizar el gráfico de pagos mensuales
                chartMonth.data.labels = data.labels; // Meses
                chartMonth.data.datasets[0].data = data.data; // Valores
                chartMonth.update();
            })
            .catch(error => console.error('Error fetching monthly data:', error));
    }

    function initializeChart(chartId) {
        const ctx = document.getElementById(chartId).getContext('2d');
        return new Chart(ctx, {
            type: "line",
            data: {
                labels: [], // Inicialmente vacío
                datasets: [{
                    label: "{{ trans('Amount') }}",
                    tension: 0.3,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: "#43A047",
                    pointBorderColor: "transparent",
                    borderColor: "#43A047",
                    backgroundColor: "transparent",
                    fill: true,
                    data: [], // Inicialmente vacío
                    maxBarThickness: 6
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [4, 4],
                            color: '#e5e5e5'
                        },
                        ticks: {
                            display: true,
                            padding: 10,
                            color: '#737373',
                            font: {
                                size: 14,
                                lineHeight: 2
                            },
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                            borderDash: [4, 4]
                        },
                        ticks: {
                            display: true,
                            color: '#737373',
                            padding: 10,
                            font: {
                                size: 14,
                                lineHeight: 2
                            },
                        }
                    },
                },
            },
        });
    }

    // Function to send file to the server
    function sendFile(file) {
        const data = new FormData();
        data.append("file", file);
        data.append("_token", "{{ csrf_token() }}");

        $.ajax({
            data: data,
            type: "POST",
            url: "{{ url(config('master.app.url.backend').'/file/upload-image-editor') }}",
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.status) {
                    const url = response.data.url;
                    $('#description').summernote('insertImage', url);
                }
            }
        });
    }

    // Check/Uncheck all checkboxes
    function checkAll(param, key) {
        let div = $('.' + param),
            checked = div.find('input[type="checkbox"]:checked').length,
            total = div.find('input[type="checkbox"]').length;
        div.find('input[type="checkbox"]').prop('checked', checked !== total)
        $('.check-all-' + key).html(checked !== total ? '<i class="fa fa-check"></i> ' + '{{ trans('Uncheck All') }}' : '<i class="fa fa-check"></i> ' + '{{ trans('Check All') }}');
    }

    // Check/Uncheck all checkboxes
    function checkAllLevel(param, obj) {
        $('.' + param).find('input[type="checkbox"]').prop('checked', $(obj).prop('checked'))
    }

    function initializeDataTable(ajaxUrl, columnsConfig) {
        if ( !$.fn.DataTable.isDataTable( '#datatable' ) ) {
            $('#datatable').DataTable({
                searchDelay: 2000,
                responsive: true,
                lengthChange: true,
                searching: true,
                processing: true,
                serverSide: true,
                lengthMenu: [[10, 25, 50, 100 ,200 , 500, -1], [10, 25, 50, 100 ,200 , 500, "All"]],
                ajax: ajaxUrl,
                language: {
                    url: "{{ asset(config('master.app.web.template').'/assets/vendor_components/datatable/spanish.json') }}"
                },
                columns: columnsConfig,
                dom: 'lBfrtip',
                buttons: [
                    {
                        extend: 'csv',
                        text: 'CSV',
                        className: 'btn btn-success btn-xs ms-10',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        text: 'Excel',
                        className: 'btn btn-info btn-xs',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        className: 'btn btn-warning btn-xs',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        text: "{{ trans('Print') }}",
                        className: 'btn btn-danger btn-xs me-10',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ]
            })
        };
    }
{{--</script>--}}

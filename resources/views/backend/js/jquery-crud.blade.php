{{--<script>--}}

(function() {
    const errorBuilder = (error, targetClass = 'modal-message') => {
        const errorValidation = (errors) => {
            for (const [index, value] of Object.entries(errors)) {
                const element = document.getElementById(index);
                if (element) {
                    const isSelect2 = $(element).hasClass('select2-hidden-accessible');
                    const isTextArea = $(element).is('textarea'); // Verificamos si es un textarea
                    const targetElement = isSelect2 ?
                        ($(element).next().find('.select2-selection')[0] || element) :
                        (['radio', 'checkbox'].includes($(element).attr('type')) ?
                            ($(element).parent()[0] || element) :
                            element);

                    targetElement.classList.add('is-invalid', 'border', 'border-danger');

                    // Aquí es donde modificamos la inserción del mensaje de error
                    const errorMessageElement = $('<span class="invalid-feedback" role="alert"><i class="mdi mdi-alert-octagram"></i> ' + value + '</span>');

                    if (isSelect2) {
                        // Para Select2, insertamos el mensaje de error justo después de la selección
                        $(targetElement).after(errorMessageElement);
                    } else {
                        // Para otros inputs, lo insertamos en el input-group
                        $(targetElement).closest('.input-group').append(errorMessageElement);
                    }
                    if (index === Object.keys(errors)[0]) targetElement.focus();
                }
            }
        };

        const errorMessage = (message) => {
            const oopsMessage = '{{ trans('Oops') }}';
            $(`.${targetClass}`).html('<div class="error-msg alert alert-danger text-white form-group m-15"><b>' + oopsMessage + '!</b> ${message}</div>');
        };

        if (error?.responseJSON?.errors) {
            errorValidation(error.responseJSON.errors);
        } else if (error?.responseJSON?.message) {
            errorMessage(error.responseJSON.message);
        } else if (error?.message) {
            errorMessage(error.message);
        } else {
            errorValidation(error);
        }
    };

    const clearError = (targetClass = 'error-msg') => {
        $(`.invalid-feedback, .alert-danger, .${targetClass}`).remove();
        $('.is-invalid').removeClass('is-invalid border-danger');
    };

    const formValidate = (formIds) => {
        let isValid = true;
        let errors = {};
        formIds.forEach(formId => {
            $(`#${formId} [required]`).each(function (e, field) {
                if (!field.value || (['radio', 'checkbox'].includes(field.type) && !field.checked)) {
                    errors[field.id] = "{{ trans('This field is required.')}}";
                    isValid = false;
                }
            });
        });

        if (!isValid) errorBuilder(errors);
        return isValid;
    };

    const targetFunction = _target => {
        _target.split(',').forEach((func) => {
            if (typeof window[func] === "function") {
                window[func]();
            }
        });
    };

    $(window.document).on('click', '.btn-action', function (e) {
        e.preventDefault();
        e.stopPropagation(); // Agregamos esta línea para evitar la propagación del evento

        const id = $(this).data('id') ?? '';
        const url = $(this).data('url') ?? window.location.href;
        const action = $(this).data('action') ?? '';
        const title = $(this).data('title') ?? '';
        const modalId = $(this).data('modalId') ?? 'modal-master';
        const bgClass = $(this).data('bgClass') ?? 'bg-default';
        const arguments = $(this).data('options') ?? '';
        const actionUrls = {
            'create': 'create',
            'edit': `${id}/edit`,
            'delete': `delete/${id}`,
            'show': `${id}`
        };
        const urlExtension = actionUrls[action] || '';
        const modalOptions = {
            url: urlExtension ? `${url}/${urlExtension}${arguments}` : url,
            id: modalId,
            dlgClass: 'fade',
            bgClass: bgClass,
            title: title,
            width: 'whatever',
            modal: {
                keyboard: false,
                backdrop: 'static',
            },
            ajax: {
                dataType: 'html',
                method: 'GET',
                cache: false,
                beforeSend() {
                    $.showLoading();
                },
                success(response) { // Aquí se agrega el parámetro response
                    $.hideLoading();
                    $(`#${modalId} .modal-body`).html(response); // Asegúrate de insertar el contenido en el modal
                    $(`#${modalId}`).modal('show');
                },
                error: function (xhr) {
                    $.hideLoading();
                    $.showError(xhr.status + ' ' + xhr.statusText);
                }
            },
        };
        $('#modal-master .modal-body').empty();

        $.loadModal(modalOptions);
    });

    $(window.document).off('click', '.submit-data').on('click', '.submit-data', function (e) {
        e.preventDefault();

        const btnSubmit = e.target;
        const textBtn = btnSubmit.innerText;
        const parent = $(this).parents('.modal').length ? $(this).parents('.modal') : $(this).parents();
        const form = btnSubmit.form ?? parent.find('form');
        const formId = form.id ?? form.attr('id');
        const progress = $('.progress-bar');
        const dismiss = parent.find('[data-bs-dismiss]');
        clearError();

        if (!formValidate([formId])) {
            return false;
        }
        $('#' + formId).ajaxForm({
            dataType: 'json',
            uploadProgress: function (event, position, total, percentComplete) {
                const percentVal = percentComplete + '%';
                progress.width(percentVal);
                progress.html(percentComplete === 100 ? "{{ trans('Please wait...') }}" : percentVal);
            },
            beforeSubmit: function () {
                progress.width('0%');
                progress.html('¡0% ' + "{{ trans('Completed')}}");
                dismiss.prop('disabled', true);
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i>  ' + "{{ trans('Processing...') }}";
                $('.progress').show();
            },
            success: function (response, status, xhr, $form) {
                $('.progress').hide();
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fa fa-save"></i> ' + textBtn;
                dismiss.prop('disabled', false);

                if (response.status === true) {
                    const _targetTable = $form.find('input[name="table-id"]').val();
                    const _targetFunction = $form.find('input[name="function"]').val();
                    const _redirect = $form.find('input[name="redirect"]').val() || '';

                    Swal.fire({
                        title: response.title || "{{ trans('Good job!')}}",
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        showClass: {
                            popup: `
                            animate__animated
                            animate__fadeInUp
                            animate__faster
                            `
                        },
                        hideClass: {
                            popup: `
                            animate__animated
                            animate__fadeOutDown
                            animate__faster
                            `
                        }
                    });

                    if (_targetTable) {
                        _targetTable.split(',').forEach((tableId) => {
                            $(`#${tableId}`).DataTable().ajax.reload();
                        });
                    }

                    if (_targetFunction) {
                        targetFunction(_targetFunction);
                    }

                    if (_redirect) {
                        window.location.href = _redirect;
                    }
                    $('.modal').modal('hide');
                } else {
                    if (response.hasOwnProperty('data')) {
                        errorBuilder(response.data);
                    } else {
                        Swal.fire({
                            title: response.title || "{{ trans('Oops')}}",
                            text: response.message,
                            icon: 'error',
                            timer: 2500,
                            showConfirmButton: false,
                            timerProgressBar: true,
                            showClass: {
                                popup: `
                                animate__animated
                                animate__fadeInUp
                                animate__faster
                                `
                            },
                            hideClass: {
                                popup: `
                                animate__animated
                                animate__fadeOutDown
                                animate__faster
                                `
                            }
                        });
                    }
                }
            },
            error: function (xhr) {
                errorBuilder(xhr);
                $('.progress').hide();
                dismiss.prop('disabled', false);
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fa fa-save"></i> ' + textBtn;
            }
        }).submit();
    });

    $(window.document).on('click', '.delete-file', function (e) {
        e.preventDefault();
        let btn = $(this);
        Swal.fire({
            title: btn.data('title'),
            text: btn.data('message'),
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "{{ trans('Yes, Delete!')}}",
            cancelButtonText: "{{ trans('No, Cancel!')}}",
            closeOnConfirm: false,
            closeOnCancel: false,
            showClass: {
                popup: `
                animate__animated
                animate__fadeInUp
                animate__faster
                `
            },
            hideClass: {
                popup: `
                animate__animated
                animate__fadeOutDown
                animate__faster
                `
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: btn.data('url'),
                    success: function (data) {
                        if (data.status === true) {
                            $('#' + btn.data('id')).remove();
                            Swal.fire({
                                title: "{{ trans('Success!') }}",
                                text: "{{ trans('Deleted successfully!') }}",
                                icon: "success",
                                showClass: {
                                    popup: `
                                    animate__animated
                                    animate__fadeInUp
                                    animate__faster
                                    `
                                },
                                hideClass: {
                                    popup: `
                                    animate__animated
                                    animate__fadeOutDown
                                    animate__faster
                                    `
                                }
                            });
                        }
                    },
                    error: function (e) {
                        Swal.fire({
                            title: "{{ ('Oops!') }}",
                            text: e.responseJSON.message,
                            icon: "error",
                            showClass: {
                                popup: `
                                animate__animated
                                animate__fadeInUp
                                animate__faster
                                `
                            },
                            hideClass: {
                                popup: `
                                animate__animated
                                animate__fadeOutDown
                                animate__faster
                                `
                            }
                        });
                    }
                });
            } else {
                Swal.close();
            }
        });
    });

    $(window.document).on('click', '.show-hide-password', function () {
        const input = $(this).parent().find('input');
        const icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('mdi mdi-eye-off-outline ').addClass('mdi mdi-eye-outline ');
        } else {
            input.attr('type', 'password');
            icon.removeClass('mdi-eye-outline  ').addClass('mdi mdi-eye-off-outline ');
        }
    })

    // Password confirmation validation
    $(window.document).on('keyup', '#password_confirmation', function () {
        const password = $('#password');
        const passwordConfirmation = $(this);
        const isMatch = password.val() === passwordConfirmation.val();

        // Set border color based on match
        const borderColor = isMatch ? 'green' : 'red';
        password.css('border-color', borderColor);
        passwordConfirmation.css('border-color', borderColor);
    });

})();
{{--</*jshint>--}}

{{--<script>--}}
$(function () {
    const _token = $('meta[name="csrf-token"]').attr('content');

    $(document).keypress(function (e) {
        if (e.key === 'Enter') $('#go-login').click();
    });

    $('#go-login').on('click', function () {
        const password = $('[name="password"]');
        const email = $('[name="email"]');
        const login_info = $('.info-login');

        // Validación de campos
        if (!password.val() || !email.val()) {
            password.toggleClass('is-invalid', !password.val());
            email.toggleClass('is-invalid', !email.val());
            return login_info.html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + "{{ trans('Please fill in the form') }}" + '</span>');
        }

        $(this).html('<i class="fa fa-spinner fa-spin"></i> ' + "{{ trans('Loading...') }}");
        $('.form-control').removeClass('is-invalid');

        const form = new FormData($('#login-form')[0]);
        form.append('_token', _token);
        form.append('device_name', "web");
        form.set('remember', form.get('remember') === 'true');

        $.ajax({
            url: 'sign-in',
            type: 'POST',
            data: form,
            processData: false,
            contentType: false,
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                login_info.html('');
            },
            success: function (response) {
                $('#go-login').html("{{ trans('Sign In') }}");
                if (response.status === 200) {
                    // Login exitoso
                    login_info.html('<i class="mdi mdi-account-check text-success"></i> ' + "{{ trans('Login successful!, redirecting...') }}");
                    window.location.href = 'login';
                } else {
                    // Manejo de otros errores
                    login_info.html('<span class="text-info"><i class="mdi mdi-alert-outline "></i> ' + response.message + '</span>');
                }
            },
            error: function (xhr) {
                $('#go-login').html("{{ trans('Sign In') }}");
                const error = JSON.parse(xhr.responseText);
                $('.input-error').html('');
                if (error.data) {
                    $.each(error.data, function (field, messages) {
                        $('#' + field).addClass('is-invalid');
                        if (Array.isArray(messages)) {
                            $('#' + field + '-error').html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + messages.join('<br/> ') + '</span>');
                        } else {
                            // Manejar el caso en que messages no es un array
                            if (error.status === 410) {
                                // Reenviar Verificación de Email
                                login_info.html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + error.message + '</span>');
                                // Aquí se maneja el caso de verificación de email expirada
                                $('#verification-link-request').html(`
                                    <form id="verification-form" action="{{ route('verification.send') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="email" value="${email.val()}">
                                        <button type="submit" id="btn-link" class="btn btn-link">{{ trans('Request a new verification link')}}</button>
                                        <div id="response-message" style="display: none;"></div>
                                    </form>
                                `);

                                document.getElementById('verification-form').addEventListener('submit', function(event) {
                                    event.preventDefault(); // Evita que el formulario se envíe de forma tradicional

                                    fetch('{{ route('verification.send') }}', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({ email: email.val() }) // Envía el email como parte del cuerpo
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        const responseMessage = document.getElementById('response-message');
                                        responseMessage.style.display = 'block';
                                        responseMessage.innerHTML = data.message;

                                        if (data.status === 200) {
                                            responseMessage.className = 'alert alert-success';
                                        } else {
                                            responseMessage.className = 'alert alert-danger';
                                        }
                                    })
                                    .catch(error => {
                                        const responseMessage = document.getElementById('response-message');
                                        responseMessage.style.display = 'block';
                                        responseMessage.innerHTML = '{{ trans('Error sending email verification') }}';
                                        responseMessage.className = 'alert alert-danger';
                                    });
                                });
                            } else {
                                $('#' + field + '-error').html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + messages + '</span>');
                            }
                        }
                    });
                } else {
                    login_info.html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + error.message + '</span>');
                }
            }
        });
    });

    $('#go-forgot-password').on('click', function () {
        const email = $('[name="email"]');
        const forgot_password_info = $('.info-forgot-password');

        // Validación de campos
        if (!email.val()) {
            email.toggleClass('is-invalid', !email.val());
            return forgot_password_info.html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + "{{ trans('Please fill in the form') }}" + '</span>');
        }

        $(this).html('<i class="fa fa-spinner fa-spin"></i> ' + "{{ trans('Loading...') }}");
        $('.form-control').removeClass('is-invalid');

        const form = new FormData($('#forgot-password-form')[0]);
        form.append('_token', _token);
        form.append('device_name', "web");

        $.ajax({
            url: 'forgot-password',
            type: 'POST',
            data: form,
            processData: false,
            contentType: false,
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                forgot_password_info.html('');
            },
            success: function (response) {
                $('#go-forgot-password').html("{{ trans('Email Password Reset Link') }}");
                if (response.status) {
                    forgot_password_info.html('<span class="font-medium text-sm text-success"><i class="mdi mdi-account-check text-success"></i> ' + response.message + '</span>');
                } else {
                    forgot_password_info.html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + response.message + '</span>');
                }
            },
            error: function (xhr) {
                $('#go-forgot-password').html("{{ trans('Email Password Reset Link') }}");
                const error = JSON.parse(xhr.responseText);
                $('.input-error').html('');
                if (error.data) {
                    $.each(error.data, function (field, messages) {
                        $('#' + field).addClass('is-invalid');
                        if (Array.isArray(messages)) {
                            $('#' + field + '-error').html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + messages.join('<br/> ') + '</span>');
                        } else {
                            // Manejar el caso en que messages no es un array
                            $('#' + field + '-error').html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + messages + '</span>');
                        }
                    });
                } else {
                    forgot_password_info.html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + error.message + '</span>');
                }
            }
        });
    });

    $('#go-reset-password').on('click', function () {
        const email = $('[name="email"]');
        const password = $('[name="password"]');
        const password_confirmation = $('[name="password_confirmation"]')
        const reset_password_info = $('.info-reset-password');

        // Validación de campos
        if (!email.val() || !password.val() || !password_confirmation.val()) {
            email.toggleClass('is-invalid', !email.val());
            password.toggleClass('is-invalid', !password.val());
            password_confirmation.toggleClass('is-invalid', !password_confirmation.val());
            return reset_password_info.html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + "{{ trans('Please fill in the form') }}" + '</span>');
        }

        $(this).html('<i class="fa fa-spinner fa-spin"></i> ' + "{{ trans('Loading...') }}");
        $('.form-control').removeClass('is-invalid');

        const form = new FormData($('#reset-password-form')[0]);
        form.append('_token', _token);
        form.append('device_name', "web");

        $.ajax({
            url: 'store',
            type: 'POST',
            data: form,
            processData: false,
            contentType: false,
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                reset_password_info.html('');
            },
            success: function (response) {
                $('#go-reset-password').html("{{ trans('Reset Password') }}");
                if (response.data === null) {
                    reset_password_info.html('<span class="font-medium text-sm text-success"><i class="mdi mdi-account-check text-success"></i> ' + response.message + ' ' + "{{ trans('Redirecting...') }}" + '</span>');
                    // Redirigir a la página de login después de un restablecimiento exitoso
                    setTimeout(function() {
                        window.location.href = "{{ route('login') }}";
                    }, 2000); // Espera 2 segundos antes de redirigir
                } else {
                    reset_password_info.html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + response.message + '</span>');
                }
            },
            error: function (xhr) {
                $('#go-reset-password').html("{{ trans('Reset Password') }}");
                const error = JSON.parse(xhr.responseText);
                $('.input-error').html('');

                if (error.data) {
                    $.each(error.data, function (field, messages) {
                        $('#' + field).addClass('is-invalid');
                        if (Array.isArray(messages)) {
                            $('#' + field + '-error').html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + messages.join('<br/> ') + '</span>');
                        } else {
                            // Manejar el caso en que messages no es un array
                            $('#' + field + '-error').html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + messages + '</span>');
                        }
                    });
                } else {
                    reset_password_info.html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + error.message + '</span>');
                }
            }
        });
    });

    $('#go-register').on('click', function () {
        const firstname = $('[name="first_name"]');
        const lastname = $('[name="last_name"]');
        const email = $('[name="email"]');
        const password = $('[name="password"]');
        const password_confirmation = $('[name="password_confirmation"]');
        const register_info = $('.info-register');
        const send_mail_info = $('.info-send-email');

        // Validación de campos
        if (!firstname.val() || !lastname.val() || !email.val() || !password.val() || !password_confirmation.val()) {
            firstname.toggleClass('is-invalid', !firstname.val());
            lastname.toggleClass('is-invalid', lastname.val());
            email.toggleClass('is-invalid', !email.val());
            password.toggleClass('is-invalid', !password.val());
            password_confirmation.toggleClass('is-invalid', !password_confirmation.val());
            return register_info.html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + "{{ trans('Please fill in the form') }}" + '</span>');
        }

        $(this).html('<i class="fa fa-spinner fa-spin"></i> ' + "{{ trans('Loading...') }}");
        $('.form-control').removeClass('is-invalid');

        const form = new FormData($('#register-form')[0]);
        form.append('_token', _token);
        form.append('device_name', "web");
        form.set('agree-terms', form.get('agree-terms') === 'on');

        $.ajax({
            url: 'sign-up',
            type: 'POST',
            data: form,
            processData: false,
            contentType: false,
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                register_info.html('');
            },
            success: function (response) {
                $('#go-register').html("{{ trans('Sign Up') }}");
                if (response.status) {
                    register_info.html('<i class="mdi mdi-account-check text-success"></i> ' + "{{ trans('Registration successful!') }}");
                    register_info.append('<p>{{ trans('A new verification link has been sent to the email address you provided during registration.') }}</p>');
                    // Redirigir a la página de login después de un restablecimiento exitoso
                    setTimeout(function() {
                        window.location.href = "{{ route('login') }}";
                    }, 3000); // Espera 3 segundos antes de redirigir
                } else {
                    register_info.html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + response.message + '</span>');
                }
            },
            error: function (xhr) {
                $('#go-register').html("{{ trans('Sign Up') }}");
                const error = JSON.parse(xhr.responseText);
                $('.input-error').html('');
                console.log(error.data);
                if (error.data) {
                    $.each(error.data, function (field, messages) {
                        $('#' + field).addClass('is-invalid');
                        if (Array.isArray(messages)) {
                            $('#' + field + '-error').html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + messages.join('<br/> ') + '</span>');
                        } else {
                            // Manejar el caso en que messages no es un array
                            $('#' + field + '-error').html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + messages + '</span>');
                        }
                    });
                } else {
                    register_info.html('<span class="text-danger"><i class="mdi mdi-alert-outline "></i> ' + error.message + '</span>');
                }
            }
        });
    });

    let email_input = $('#email');
    let password_input = $('#password');
    let confirm_password_input = $('#password_confirmation');
    let form_login = $('#login-form');
    let form_register = $('#register-form');
    let from_reset_password = $('#reset-password-form');

    email_input.on('focus', () => headerIconChanger('looking.png'));
    password_input.on('focus', () => headerIconChanger('close.png'));
    confirm_password_input.on('focus', () => headerIconChanger('close.png'));

    form_login.on('mouseover', () => headerIconChanger('standby.png'));
    form_register.on('mouseover', () => headerIconChanger('standby.png'));
    from_reset_password.on('mouseover', () => headerIconChanger('standby.png'));

    $('.show-hide-password').on('click', function () {
        const input = $(this).parent().find('input');
        const icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
             icon.removeClass('mdi mdi-eye-off-outline ').addClass('mdi mdi-eye-outline ');
            headerIconChanger('peeking.png')
        } else {
            input.attr('type', 'password');
            icon.removeClass('mdi-eye-outline  ').addClass('mdi mdi-eye-off-outline ');
            headerIconChanger('close.png')
        }
    })

    const headerIconChanger = (image) => {
        const src = $('#header-image-vector');
        if (src.length === 0) return;
        const url = src.attr('src').split('/');
        if (url[url.length - 1] !== image) {
            url[url.length - 1] = image;
            src.attr('src', url.join('/'));
        }
    }
});

{{--</script>--}}

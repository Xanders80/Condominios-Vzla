<x-guest-layout>
    <section class="register-content">
        <div class="row m-0 align-items-center bg-gray-400 vh-100">
            <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden">
                <img src="{{ asset(config('master.app.web.template') . '/images/auth-bg/05.png') }}"
                    class="img-fluid gradient-main animated-scaleX" alt="auth background">
            </div>
            <div class="col-md-6">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="card card-transparent auth-card shadow-none d-flex justify-content-center mb-0">
                            <div class="card-body">
                                <div class="bg-white rounded10 shadow-lg">
                                    <x-card-auth>
                                        <!-- Cabecera del Formulario -->
                                        <x-slot name="header" class="text-center text-xl">
                                            <div class="col-md-4 col-lg-4 align-items-center hidden-lg-down me-0">
                                                <img id="header-image-vector" class="pull-right p-5" width="90"
                                                    height="90"
                                                    src="{{ asset(config('master.app.web.template') . '/assets/vector/sleeping.png') }}"
                                                    alt="avatar">
                                            </div>
                                            <div
                                                class="content-top-agile col-md-8 col-lg-8 col-12 pull-left hidden-lg-down ms-0 text-center">
                                                <p class="display-6">{{ trans('Reset Password') }}</p>
                                                <p class="mb-0 font-medium text-sm text-success">{{ $request->email }}
                                                </p>
                                            </div>
                                        </x-slot>

                                        <!-- Cuerpo del Formulario -->
                                        <form action="{{ route('password.store') }}" method="POST"
                                            name="reset-password-form" id="reset-password-form">
                                            @csrf

                                            <!-- Token de Restablecimiento de Contraseña -->
                                            <input type="hidden" name="token" value="{{ $request->route('token') }}">
                                            <input type="hidden" name="email" value="{{ $request->email }}">

                                            <x-input-password name="password" label="{{ trans('New Password') }}"
                                                plHolder="{{ trans('Type here...') }}" icon="mdi mdi-key "
                                                id="password" isRequired=true autofocus />
                                            <x-input-error id="password" />

                                            <x-input-password name="password_confirmation"
                                                label="{{ trans('Confirm Password') }}"
                                                plHolder="{{ trans('Type here...') }}" icon="mdi mdi-key-variant "
                                                id="password_confirmation" isRequired=true autofocus />
                                            <x-input-error id="password_confirmation" />

                                            <span class="info-reset-password"></span>

                                            <div class="row mt-4">
                                                <div class="col-12 text-center mt-2">
                                                    <x-button-button class="ms-4 btn-dark" id="go-reset-password">
                                                        {{ trans('Reset Password') }}
                                                    </x-button-button>
                                                </div>
                                            </div>
                                        </form>

                                        <!-- Pie del Formulario -->
                                        <x-slot name="footer">
                                            <div class="text-center">
                                                <p class="mt-20 mb-10">{{ trans('Already have an account?') }}
                                                    <x-nav-link :href="route('login')" class="text-info fw-bold">
                                                        {{ trans('Sign In') }}
                                                    </x-nav-link>
                                                </p>
                                            </div>
                                        </x-slot>
                                    </x-card-auth>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    </section>
</x-guest-layout>

<script>
    $('#password_confirmation').on('keyup', function() {
        let password = $('#password');
        let password_confirmation = $('#password_confirmation');
        if (password.val() === password_confirmation.val()) {
            password_confirmation.css('border-color', 'green');
            password.css('border-color', 'green');
        } else {
            password_confirmation.css('border-color', 'red');
            password.css('border-color', 'red');
        }
    });
</script>

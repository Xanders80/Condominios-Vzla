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
                                            <div
                                                class="content-top-agile col-md-8 col-lg-8 col-12 pull-left hidden-lg-down ms-0">
                                                <p class="display-6 text-center">{{ trans('Forgotten Password') }}</p>
                                            </div>
                                        </x-slot>

                                        <!-- Cuerpo del Formulario -->
                                        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                            {{ trans('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                                        </div>

                                        <form method="post" name="forgot-password-form" id="forgot-password-form">
                                            @csrf

                                            <x-input-text icon="mdi mdi-email-variant " id="email" name="email"
                                                label="{{ trans('Email') }}" plHolder="{{ trans('Type here...') }}"
                                                isRequired=true autofocus />
                                            <x-input-error id="email" />

                                            <span class="info-forgot-password mt-2"></span>

                                            <div class="row mt-4">
                                                <div class="col-12 text-center">
                                                    <x-button-button class="ms-4 btn-dark" id="go-forgot-password">
                                                        {{ trans('Email Password Reset Link') }}
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

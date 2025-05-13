<x-guest-layout>
    <section class="login-content">
        <div class="row m-0 align-items-center bg-gray-400 vh-100">
            <div class="col-md-6">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="card card-transparent shadow-none d-flex justify-content-center mb-0 auth-card">
                            <div class="card-body">
                                <div class="bg-white rounded10 shadow-lg">
                                    <x-card-auth>
                                        <!-- Cabecera del Formulario -->
                                        <x-slot name="header" class="text-center text-xl">
                                            @if (config('master.app.web.header_animation') == 'on')
                                                <div class="col-md-4 col-lg-4 align-items-center hidden-lg-down me-0">
                                                    <img id="header-image-vector" class="pull-right p-5" width="90"
                                                        height="90"
                                                        src="{{ asset(config('master.app.web.template') . '/assets/vector/sleeping.png') }}"
                                                        alt="avatar">
                                                </div>
                                                <div
                                                    class="col-md-8 col-lg-8 col-12 pull-left hidden-lg-down ms-0 text-center">
                                                    <x-show-text textoH2="{!! config('master.app.profile.name') !!}"
                                                        texto="{{ trans('Sign in to continue to') }}"
                                                        value="{!! config('master.app.profile.short_name') !!}"></x-show-text>
                                                </div>
                                            @else
                                                <div class="col-12 content-top-agile text-center">
                                                    <x-show-text textoH2="{!! config('master.app.profile.name') !!}"
                                                        texto="{{ trans('Sign in to continue to') }}"
                                                        value="{!! config('master.app.profile.short_name') !!}"></x-show-text>
                                                </div>
                                            @endif
                                        </x-slot>
                                        <!-- Validation Errors -->
                                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                                        <!-- Cuerpo del Formulario -->
                                        <form method="post" name="login-form" id="login-form">
                                            <x-input-text icon="mdi mdi-email-variant " id="email" name="email"
                                                label="{{ trans('Email') }}" plHolder="{{ trans('Type here...') }}"
                                                isRequired=true autofocus />
                                            <x-input-error id="email" />
                                            <x-input-password icon="mdi mdi-account-key " id="password" name="password"
                                                label="{{ trans('Password') }}" plHolder="{{ trans('Type here...') }}"
                                                isRequired=true autofocus />
                                            <x-input-error id="password" />

                                            <span class="info-login mt-2"></span>

                                            <div class="row mt-4">
                                                <div class="col-6">
                                                    <x-input-checkbox id="md_checkbox" name="remember"
                                                        label="{{ trans('Remember Me') }}" class="checkbox"
                                                        valor="true" />
                                                </div>
                                                <div class="col-6">
                                                    <div class="fog-pwd text-end">
                                                        {{ trans('If you have forgotten your password, please click on the following link:') }}<br>
                                                        <x-nav-link :href="route('link.reset')" class="hover-warning">
                                                            {{ trans('Reset Password') }}
                                                        </x-nav-link>
                                                    </div>
                                                </div>
                                                <div class="col-12 text-center">
                                                    <x-button-button class="ms-4 btn-dark" id="go-login">
                                                        {{ trans('Sign In') }}
                                                    </x-button-button>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="text-center">
                                            <p class="mt-20 mb-10">
                                                {{ trans('If you are not a Portal user, you can register at the following link:') }}<br>
                                                <x-nav-link :href="route('register')" class="text-info fw-bold ms-5">
                                                    {{ trans('Sign Up Now') }}
                                                </x-nav-link>
                                            </p>
                                        </div>
                                        <!-- Pie del Formulario -->
                                        <x-slot name="footer">
                                            <div id="verification-link-request"></div>
                                        </x-slot>
                                    </x-card-auth>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden">
                <img src="{{ asset(config('master.app.web.template') . '/images/auth-bg/01.png') }}"
                    class="img-fluid gradient-main animated-scaleX" alt="auth background">
            </div>
        </div>
    </section>
</x-guest-layout>

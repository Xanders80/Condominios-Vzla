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
                                                <p class="display-6">{{ trans('Register User') }}</p>
                                            </div>
                                        </x-slot>

                                        <!-- Cuerpo del Formulario -->
                                        <form method="post" name="register-form" id="register-form">
                                            @csrf

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <x-input-text name="first_name" label="{{ trans('First name') }}"
                                                        plHolder="{{ trans('Type here...') }}"
                                                        icon="mdi mdi-account-outline " id="first_name" isRequired=true
                                                        autofocus />
                                                    <x-input-error id="first_name" />
                                                </div>

                                                <div class="col-md-6">
                                                    <x-input-text name="last_name" label="{{ trans('Last Name') }}"
                                                        plHolder="{{ trans('Type here...') }}" icon="mdi mdi-account "
                                                        id="last_name" isRequired=true autofocus />
                                                    <x-input-error id="last_name" />
                                                </div>
                                            </div>

                                            <x-input-text name="email" label="{{ trans('Email') }}"
                                                plHolder="{{ trans('Type here...') }}" icon="mdi mdi-email-variant "
                                                id="email" isRequired=true autofocus />
                                            <x-input-error id="email" />

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <x-input-password name="password" label="{{ trans('Password') }}"
                                                        plHolder="{{ trans('Type here...') }}" icon="mdi mdi-key "
                                                        id="password" isRequired=true autofocus />
                                                    <x-input-error id="password" />
                                                </div>

                                                <div class="col-md-6">
                                                    <x-input-password name="password_confirmation"
                                                        label="{{ trans('Confirm Password') }}"
                                                        plHolder="{{ trans('Type here...') }}"
                                                        icon="mdi mdi-key-variant " id="password_confirmation"
                                                        isRequired=true autofocus />
                                                    <x-input-error id="password_confirmation" />
                                                </div>
                                            </div>

                                            <span class="info-register"></span>

                                            <div class="row mt-4">
                                                <div class="row col-6">
                                                    <x-input-checkbox id="md_checkbox" name="agree-terms"
                                                        label="{{ trans('I agree to the') }}" class="checkbox" />
                                                </div>
                                                <div class="row col-6">
                                                    <x-nav-link class="hover-warning fw-bold" style="cursor: pointer;"
                                                        data-bs-toggle="modal" data-bs-target="#modalScrollable">
                                                        {{ trans('Terms') }}
                                                    </x-nav-link>
                                                </div>
                                                <div class="col-12 text-center mt-2">
                                                    <x-button-button class="ms-4 btn-dark" id="go-register">
                                                        {{ trans('Register') }}
                                                    </x-button-button>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="text-center">
                                            <p class="mt-20 mb-10">{{ trans('Already have an account?') }}
                                                <x-nav-link :href="route('login')" class="text-info fw-bold">
                                                    {{ trans('Sign In') }}
                                                </x-nav-link>
                                            </p>
                                        </div>

                                        <!-- Pie del Formulario -->
                                        <x-slot name="footer">
                                        </x-slot>
                                    </x-card-auth>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-guest-layout>
<!-- Modal -->
<div class="modal fade" id="modalScrollable" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalScrollableTitle">{{ trans('Terms') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <x-show-content-term></x-show-content-term>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-danger" data-bs-dismiss="modal"><i class="mdi mdi-cancel "></i>
                    {{ trans('Close') }} </button>
            </div>
        </div>
    </div>
</div>

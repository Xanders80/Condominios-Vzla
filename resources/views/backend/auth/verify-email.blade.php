@extends('backend.auth.index')
@push('title', config('master.app.profile.name') . ' - ' . trans('Resend Email'))
@section('content')
    <div class="container h-p100">
        <div class="row align-items-center justify-content-md-center h-p100 col-12">
            <div class="row justify-content-center g-0">
                <div class="col-lg-5 col-md-5 col-12">
                    <div class="bg-white rounded10 shadow-lg">
                        <x-card-auth>
                            <!-- Cabecera del Formulario -->
                            <x-slot name="header" class="text-center text-xl">
                                <div class="content-top-agile p-20 pb-0">
                                    <h3 class="text-primary">{{ trans('Resend Verification Email') }}</h3>
                                </div>
                            </x-slot>

                            <!-- Cuerpo del Formulario -->
                            <div class="container mt-5">
                                <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ trans("Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.") }}
                                </div>

                                @if (session('status') == 'verification-link-sent')
                                    <div class="mb-4 font-medium text-sm text-green-600">
                                        {{ trans('A new verification link has been sent to the email address you provided during registration.') }}
                                    </div>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf

                                <div class="row mt-4">
                                    <div class="col-12 text-center">
                                        <x-button-button class="ms-4 btn-dark" id="go-forgot-password">
                                            {{ trans('Resend Verification Email') }}
                                        </x-button-button>
                                    </div>
                                </div>
                            </form>

                            <!-- Pie del Formulario -->
                            <x-slot name="footer">
                                <div class="text-center">
                                    <x-nav-link :href="{{ route('logout') }}" class="text-info fw-bold">
                                        {{ trans('Log Out') }}
                                    </x-nav-link>
                                </div>
                            </x-slot>
                        </x-card-auth>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('resend-verification').addEventListener('click', function(event) {
            event.preventDefault();

            fetch('{{ route('verification.resend') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
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
                    console.error('Error:', error);
                    const responseMessage = document.getElementById('response-message');
                    responseMessage.style.display = 'block';
                    responseMessage.innerHTML = '{{ trans('Error sending email verification') }}';
                    responseMessage.className = 'alert alert-danger';
                });
        });
    </script>
@endsection

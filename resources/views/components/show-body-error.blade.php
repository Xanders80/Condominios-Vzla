@props(['codeError', 'message', 'detail'])
<div class="container-fluid p-0">
    <div class="iq-maintenance text-center">
        <img src="{{ asset(config('master.app.web.template') . '/images/auth-bg/' . $codeError . '.png') }}"
            class="mb-4" alt="" />
        <div class="maintenance-bottom text-white pb-0">
            <div class="bg-primary" style="background: transparent; height: 320px;">
                <div class="gradient-bottom">
                    <div class="bottom-text general-zindex">
                        <h2 class="mb-0 mt-4 text-white">{{ $message }}</h2>
                        <p class="mt-2 text-white">{{ $detail }}</p>
                        <a class="btn bg-white text-primary d-inline-flex align-items-center"
                            href="{{ route('dashboard') }}">{{ trans('Back to dashboard') }}</a>
                    </div>
                    <div class="c xl-circle">
                        <div class="c lg-circle">
                            <div class="c md-circle">
                                <div class="c sm-circle">
                                    <div class="c xs-circle"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sign-bg">
        <svg width="280" height="230" viewBox="0 0 431 398" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g opacity="0.05">
                <rect x="-157.085" y="193.773" width="543" height="77.5714" rx="38.7857"
                    transform="rotate(-45 -157.085 193.773)" fill="#3B8AFF" />
                <rect x="7.46875" y="358.327" width="543" height="77.5714" rx="38.7857"
                    transform="rotate(-45 7.46875 358.327)" fill="#3B8AFF" />
                <rect x="61.9355" y="138.545" width="310.286" height="77.5714" rx="38.7857"
                    transform="rotate(45 61.9355 138.545)" fill="#3B8AFF" />
                <rect x="62.3154" y="-190.173" width="543" height="77.5714" rx="38.7857"
                    transform="rotate(45 62.3154 -190.173)" fill="#3B8AFF" />
            </g>
        </svg>
    </div>
</div>

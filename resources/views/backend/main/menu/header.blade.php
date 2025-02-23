<header class="main-header">
    <div class="d-flex align-items-center logo-box justify-content-start">
        <button title="{!! config('master.app.profile.name') !!}"
            class="waves-effect waves-light nav-link d-none d-md-inline-block mx-10 push-btn bg-transparent text-white"
            data-toggle="push-menu" onKeyPress="handleKeyPress(event)">
            <i class="mdi mdi-television-guide mdi-24px">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
        </button>
        <a href="{{ route('dashboard') }}" class="logo">
            <div class="logo-lg">
                <i class="light-logo"><img src="{{ asset($template . config('master.app.web.logo_light')) }}"
                        alt="logo" width="140" height="70"></i>
                <i class="dark-logo"><img src="{{ asset($template) . config('master.app.web.logo_dark') }}"
                        alt="logo" width="140" height="70"></i>
            </div>
        </a>
    </div>
    <nav class="navbar navbar-static-top">
        <div class="app-menu">
            <ul class="header-megamenu nav">
                <li class="btn-group nav-item d-md-none">
                    <button class="waves-effect waves-light nav-link push-btn" data-toggle="push-menu">
                        <i class="mdi mdi-orbit mdi-24px text-dark">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </button>
                </li>
                <li class="btn-group nav-item d-lg-inline-flex d-none">
                    <a href="#" data-provide="fullscreen" class="waves-effect waves-light nav-link full-screen"
                        title="{{ trans('Full Screen') }}">
                        <i class="mdi mdi-arrow-expand-all"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                </li>
                <li class="btn-group d-lg-inline-flex d-none">
                    <div class="app-menu">
                        <div class="search-bx mx-5">
                            <div class="input-group">
                                <input type="search" class="form-control search-menu"
                                    placeholder={{ trans('Search menu here...') }} aria-label="Search"
                                    aria-describedby="button-addon2" name="search_menu">
                                <div class="input-group-append">
                                    <button class="btn" type="button" id="button-addon3"
                                        title="{{ trans('Search') }}"><i
                                            class="mdi mdi-search-web mdi-18px"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="navbar-custom-menu r-side">
            <ul class="nav navbar-nav">
                <li class="dropdown notifications-menu">
                    <a href="#" class="waves-effect waves-light dropdown-toggle" data-bs-toggle="dropdown"
                        title="{{ trans('Notifications') }}">
                        <div class="mdi mdi-bell-outline" id="notification-button"><span class="path1"></span><span
                                class="path2"></span></div>
                    </a>
                    <ul class="dropdown-menu animated bounceIn">
                        <li class="header">
                            <div class="p-20">
                                <div class="flexbox">
                                    <div>
                                        <h4 class="mb-0 mt-0">{{ trans('Notifications') }}</h4>
                                    </div>
                                    <div>
                                        <a href="#" class=" mdi mdi-notification-clear-all md1-18px text-danger"
                                            id="clear-notification" data-bs-toggle="tooltip"
                                            title="{{ trans('Clear') }}"></a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <ul class="menu sm-scrol notification-list">
                                {{-- list of notifications --}}
                            </ul>
                        </li>
                        <li class="footer">
                            <a href="{!! url(config('master.app.url.backend') . '/notification') !!}">{{ trans('View All') }}</a>
                        </li>
                    </ul>
                </li>

                {{-- Menú de Usuario --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar avatar-online mt-2">
                            <img src="{{ asset(config('master.app.web.template') . '/images/avatars/avtar_1.png') }}"
                                alt="" class="w-px-40 h-auto rounded-circle">
                        </div>
                    </a>
                    <ul class="dropdown-menu animated flipInX" aria-labelledby="navbarDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('user-detail', ['id' => auth()->user()->id]) }}">
                                <div class="caption ms-3 d-none d-md-block ">
                                    <h6 class="mb-0 caption-title">{{ auth()->user()->name ?? trans('User') }}</h6>
                                    <p class="mb-0 caption-sub-title text-capitalize">
                                        {{ str_replace('_', ' ', auth()->user()->level->name) ?? 'User Unknow' }}</p>
                                </div>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li class="user-body">
                            <button onclick="logout()" class="border-0 bg-transparent link text-center"
                                style="width: 100%;" data-bs-toggle="tooltip" title="{{ trans('Sign Out') }}">
                                <span class="mdi mdi-lock-reset mdi-24px text-mute">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </span>
                            </button>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

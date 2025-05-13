<footer class="main-footer">
    <div class="pull-right d-none d-sm-inline-block">
        <ul class="nav nav-primary nav-dotted nav-dot-separated justify-content-center justify-content-md-end">
            <li class="nav-item">
                <a class="nav-link" data-toggle="control-sidebar" data-page="{!! $page->code !!}" href="#"
                    title="{{ trans('Frequently Asked Questions') }}">FAQ</a>
            </li>
        </ul>
    </div>
    <ul class="left-panel list-inline mb-0 p-0">
        <li class="list-inline-item"><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
        <li class="list-inline-item"><a href="{{ route('term-of-use') }}">Terms of Use</a></li>
        &copy; {!! date('Y') !!} <a href="#"
            title="{!! config('master.app.profile.name') !!}">{!! config('master.app.profile.name') !!}</a>{{ trans(' made with') }} <i
            class="mdi mdi-heart text-danger"></i> {{ trans('for a better web Theme by') }} <a
            href="https://themeforest.net/" target="_blank">{{ trans('xanders80 | ThemeForest') }}</a>
    </ul>
</footer>

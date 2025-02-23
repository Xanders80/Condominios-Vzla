<aside class="control-sidebar">
    <div class="rpanel-title">
        <span class="pull-right btn btn-circle btn-danger"><i class="mdi mdi-close-outline mdi-18px"
                data-toggle="control-sidebar"></i></span>
    </div>
    <div class="tab-content">
        <div class="flexbox">
            <p><i class="mdi mdi-bullhorn mdi-18px"></i> {{ trans('Hi how can we help you?') }}</p>
        </div>
        <div class="lookup lookup-sm lookup-right d-lg-block">
            <input type="text" id="search-faq" name="search" placeholder={{ trans('Search here...') }}class="w-p100">
        </div>
        <div class="media-list media-list-hover mt-20">
            {{-- Content --}}
        </div>
        <div class="flexbox justify-content-center">
            <a href="{!! url(config('master.app.url.backend') . '/question') !!}" class="text-center">
                <i class="mdi mdi-playlist-check mdi-18px"></i> {{ trans('View All') }}
            </a>
        </div>
    </div>
</aside>
<div class="control-sidebar-bg"></div>

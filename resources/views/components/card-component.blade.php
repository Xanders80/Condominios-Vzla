@props(['label', 'message', 'sub_message' => '', 'end_text', 'icon'])

<div
    class="card subpixel-antialiased p-4 shadow-lg bg-{{ $color = !empty($sub_message) ? ($sub_message < 50 ? 'danger' : ($sub_message < 100 ? 'success' : 'primary')) : 'light' }} pull-up">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
                <h5 class="text-heading">{{ $label }}</h5>
                <div class="d-flex align-items-center my-1">
                    <h4 class="mb-0 me-2">{{ $message }}</h4>
                    @if (!empty($sub_message))
                        <p class="mb-0"><strong>({{ $sub_message }}%)</strong></p>
                    @endif
                </div>
                <small class="mb-0">{{ $end_text }}</small>
            </div>
            <div class="avatar" style="width: 50px;"> <!-- Fixed width for the icon container -->
                <span class="avatar-initial rounded bg-label-primary">
                    <i class="{{ $icon }}"></i>
                </span>
            </div>
        </div>
    </div>
</div>

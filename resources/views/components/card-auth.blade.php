<div class="mb-2">
    <div class="card-header font-bold text-center text-2xl subpixel-antialiased p-4 shadow-lg">
        {{ $header ?? 'Default Header' }}
    </div>
    <div class="card-body p-6">
        {{ $slot }}
    </div>
    <div class="card-footer bg-gray-50 text-center p-2 shadow-lg">
        {{ $footer ?? 'Default Footer' }}
    </div>
</div>

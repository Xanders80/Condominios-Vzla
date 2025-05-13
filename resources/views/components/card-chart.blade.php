@props(['id', 'title', 'subtitle', 'message', 'sub_message'])

<div class="card subpixel-antialiased p-4 shadow-lg bg-light pull-up">
    <div class="card-body">
        <h6 class="mb-0 ">{{ $title }}</h6>
        <p class="text-sm ">{{ $subtitle }}</p>
        <div class="pe-2">
            <div class="chart">
                <canvas id="{{ $id }}" class="chart-canvas" height="159"
                    style="display: block; box-sizing: border-box; height: 170px; width: 570px;" width="535"></canvas>
            </div>
        </div>
        <hr class="dark horizontal">
        <div class="d-flex ">
            <i class="material-symbols-rounded text-sm my-auto me-1">{{ $message }}</i>
            <p class="mb-0 text-sm">{{ $sub_message }}</p>
        </div>
    </div>
</div>

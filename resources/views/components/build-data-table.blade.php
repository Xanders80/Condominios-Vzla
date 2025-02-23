<div class="panel-body table-responsive .dataTables_wrapper subpixel-antialiased p-4 shadow-lg"
    style="background-color: #f2f2f2; border-radius: 10px;">
    <table id="datatable"
        class="table table-striped table-hover table-info align-items-center mb-0 nowrap compact border border-info no-border subpixel-antialiased"
        style="width: 100%; border-radius: 10px;">
        <thead>
            <tr>
                {{ $slot }}
            </tr>
        </thead>
        <tbody>
            <!-- Data will be populated here -->
        </tbody>
    </table>
</div>

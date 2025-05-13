<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <h5 class="text-uppercase">{{ trans('List of users with access rights') }}
                    <b>{{ trans($data->name) }}</b>:</h5>
            </div>
            <table id="user-datatable" class="table table-bordered table-responsive table-striped" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="w-0">{{ trans('N°') }}</th>
                        <th>{{ trans('Name') }}</th>
                        <th>{{ trans('Email') }}</th>
                        <th>{{ trans('Level') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data->users as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->level->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">{{ trans('No data available') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .modal-lg {
        max-width: 1000px !important;
    }
</style>

<script>
    $(document).ready(function() {
        $('.submit-data').hide();
        $('#user-datatable').DataTable();
        $('.modal-title').html(
            '<i class="mdi mdi mdi-eye mdi-24px text-info"></i> - {{ trans('Show Data') }} {{ $page->title }}'
            );
    });

    $('#user-datatable').DataTable({
        language: {
            {{-- Uncomment this line to use Spanish language --}}
            url: "{{ asset(config('master.app.web.template') . '/assets/vendor_components/datatable/spanish.json') }}"
        },
    });
</script>

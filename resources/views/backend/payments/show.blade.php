<div class="panel shadow-sm" style="border-radius: 10px;">
    <div class="panel-body">
        <div class="data-group mt-2"
            style="border: 1px solid #ccc; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <div class="row">
                <div class="col-md-3 form-group">
                    <x-show-span dataUser="{{ $data->nro_confirmation }}" label="{{ trans('Nro Confirmation') }}" />
                </div>

                <div class="col-md-3 form-group">
                    <x-show-span dataUser="{{ $data->amount }}" label="{{ trans('Amount') }}" />
                </div>

                <div class='form-group col-3'>
                    <x-show-span dataUser="{{ $data->date_pay }}" label="{{ trans('Date Pay') }}" />
                </div>

                <div class='form-group col-3'>
                    <x-show-span dataUser="{{ $data->date_confirm }}" label="{{ trans('Date Confirm') }}" />
                </div>
            </div>

            <div class="row">
                <div class="col-4 form-group">
                    <x-show-span dataUser="{{ $data->banks->name }}" label="{{ trans('Bank') }}" />
                </div>

                <div class="col-4 form-group">
                    <x-show-span dataUser="{{ $data->condominiums->name }}" label="{{ trans('Condominium') }}" />
                </div>

                <div class="col-4 form-group">
                    <x-show-span dataUser="{{ $data->waystopays->name }}" label="{{ trans('Way to Pay') }}" />
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <x-show-span dataUser="{{ $data->observations }}" label="{{ trans('Observations') }}" />
                </div>
            </div>
        </div>

        @if ($data->file && $data->file->exists())
            <div class="form-group text-center">
                @switch($data->file->type)
                    @case('image')
                        {!! html()->img(url($data->file->link_stream), $data->file->name)->class('img-fluid img-thumbnail')->style('width: 50%') !!}
                    @break

                    @case('file')
                        <object data="{{ url($data->file->link_stream) }}" type="application/pdf" width="100%"
                            height="600px">
                            <p>{{ trans('Alternative text - include a link') }} <a
                                    href="{{ url($data->file->link_stream) }}">{{ trans('to the PDF!') }}</a></p>
                        </object>
                    @break

                    @default
                        <a href="{!! url($data->file->link_stream) !!}" target="_blank">{!! $data->file->name !!}</a>
                @endswitch
            </div>
        @endif

    </div>
</div>
<style>
    .modal-lg {
        max-width: 1000px !important;
    }
</style>
<script>
    $('.submit-data').hide();
    $('.modal-title').html(
        '<i class="mdi mdi-eye mdi-24px text-info"></i> - {{ trans('Details') }} {{ trans($page->title) }} {{ trans('Dweller') }}: {{ $data->dweller->name }}' +
        ' <span class="badge {{ $data->conciliated ? 'badge-success' : 'badge-danger' }}">{{ $data->conciliated ? trans('Conciliated') : trans('Not Conciliated') }}</span>'
    );
</script>

<script src="{{ asset($template . '/js/vendors.min.js') }}"></script>
<script src="{{ asset($template . '/assets/vendor_components/jquery-blockUi/jquery.blockUi.js') }}"></script>
<script src="{{ asset($template . '/assets/vendor_components/select2/dist/js/select2.full.js') }}"></script>
<script src="{{ asset($template . '/assets/vendor_components/sweetalert/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset($template . '/assets/vendor_components/jquery-validation/lib/jquery.form.js') }}"></script>
<script src="{{ asset($template . '/assets/vendor_components/datatable/datatables.min.js') }}"></script>
<script src="{{ asset($template . '/js/template.js') }}"></script>
<script src="{{ url('/js/' . $backend . '/js/jquery-loadmodal.js') }}"></script>
<script src="{{ url('/js/' . $backend . '/js/jquery.js?time=' . time()) }}"></script>
<script src="{{ url('/js/' . $backend . '/js/form-helpers.js') }}"></script>
<script src="{{ url('/js/' . $backend . '/js/jquery-crud.js') }}"></script>

@stack('js')

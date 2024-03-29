@php
    // jika $data ada ISI-nya maka actionnya adalah edit, jika KOSONG : insert
    $is_edit = isset($data);
@endphp

<form method="post" action="{{ $page->url }}" role="form" class="form-horizontal" id="user-form" width="80%">
    @csrf
    {!! $is_edit ? method_field('PUT') : '' !!}
    <div id="modal-user" class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    {{ $page->title }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-message text-center"></div>
                <div class="form-group row mb-1">
                    <label for="menu_scope" class="col-sm-2 col-form-label">Lingkup</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control form-control-sm" id="menu_scope" placeholder="Lingkup"
                            name="menu_scope" value="{{ isset($data->menu_scope) ? $data->menu_scope : '' }}" />
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label for="menu_code" class="col-sm-2 col-form-label">Kode</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control form-control-sm" id="menu_code" placeholder="Kode"
                            name="menu_code" value="{{ isset($data->menu_code) ? $data->menu_code : '' }}" />
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label for="menu_name" class="col-sm-2 col-form-label">Nama</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control form-control-sm" id="menu_name" placeholder="Nama"
                            name="menu_name" value="{{ isset($data->menu_name) ? $data->menu_name : '' }}" />
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label for="menu_url" class="col-sm-2 col-form-label">URL</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control form-control-sm" id="menu_url" placeholder=""
                            name="menu_url" value="{{ isset($data->menu_url) ? $data->menu_url : '' }}" />
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label for="menu_level" class="col-sm-2 col-form-label">Level</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control form-control-sm" id="menu_level" placeholder="Level"
                            name="menu_level" value="{{ isset($data->menu_level) ? $data->menu_level : '' }}" />
                    </div>
                    <label for="order_no" class="col-sm-2 col-form-label">Urutan</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control form-control-sm" id="order_no" placeholder="Urutan"
                            name="order_no" value="{{ isset($data->order_no) ? $data->order_no : '' }}" />
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label for="class_tag" class="col-sm-2 col-form-label">Class</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control form-control-sm" id="class_tag" placeholder="Class"
                            name="class_tag" value="{{ isset($data->class_tag) ? $data->class_tag : '' }}" />
                    </div>
                    <label for="icon" class="col-sm-2 col-form-label">Icon</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control form-control-sm" id="icon" placeholder="Icon"
                            name="icon" value="{{ isset($data->icon) ? $data->icon : '' }}" />
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label for="parent_id" class="col-sm-2 col-form-label">Parent</label>
                    <div class="col-sm-10">
                        <select id="parent_id" name="parent_id" class="form-control form-control-sm parent"
                            style="width: 100%;">
                            <option value="">- Pilih -</option>

                            @foreach ($menu as $p)
                                <option value="{{ $p->menu_id }}">{{ $p->menu_level }} - {{ $p->menu_name }}
                                    ({{ $p->menu_code }})
                                </option>
                            @endforeach

                        </select>
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label for="Status" class="col-sm-2 col-form-label">Status</label>
                    <div class="col-sm-10 mt-1">
                        <div class="icheck-primary d-inline mr-2">
                            <input type="radio" id="radioPrimary1" name="is_active" value="1"
                                {{ isset($data->is_active) ? ($data->is_active == 1 ? 'checked' : '') : '' }}>
                            <label for="radioPrimary1">Aktif </label>
                        </div>
                        <div class="icheck-danger d-inline">
                            <input type="radio" id="radioPrimary2" name="is_active" value="0"
                                {{ isset($data->is_active) ? ($data->is_active == 0 ? 'checked' : '') : 'checked' }}>
                            <label for="radioPrimary2">Non-aktif</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        unblockUI();

        $('.parent').select2();

        @if ($is_edit)
            $(".parent").val("{{ $data->parent_id }}").trigger("change");
        @endif

        $("#user-form").validate({
            rules: {
                menu_code: {
                    required: true,
                    minlength: 3,
                    maxlength: 50
                },
                menu_name: {
                    required: true,
                    minlength: 5,
                    maxlength: 100
                },
                menu_level: {
                    required: true,
                    digits: true
                },
                order_no: {
                    required: true,
                    digits: true
                },
                class_tag: {
                    required: true,
                    minlength: 4,
                    maxlength: 20
                },
                icon: {
                    required: true,
                    minlength: 5,
                    maxlength: 50
                },
                is_active: {
                    required: true
                }
            },
            submitHandler: function(form) {
                $('.form-message').html('');
                blockUI(form);
                $(form).ajaxSubmit({
                    dataType: 'json',
                    success: function(data) {
                        unblockUI(form);
                        setFormMessage('.form-message', data);
                        if (data.stat) {
                            resetForm('#user-form', '.level_filter, .parent_filter');
                            dataMaster.draw(false);
                        }
                        closeModal($modal, data);
                    }
                });
            },
            validClass: "valid-feedback",
            errorElement: "div", // contain the error msg in a small tag
            errorClass: 'invalid-feedback',
            errorPlacement: erp,
            highlight: hl,
            unhighlight: uhl,
            success: sc
        });
    });
</script>

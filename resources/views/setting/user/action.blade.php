<?php
    $is_edit = isset($data);
?>

<form method="post" action="{{ $page->url }}" role="form" class="form-horizontal" id="form-master">
    @csrf
    {!! ($is_edit)? method_field('PUT') : '' !!}
    <div id="modal-master" class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-message text-center"></div>
                <div class="form-group required row mb-2">
                    <label for="group_id" class="col-sm-2 control-label col-form-label">Level</label>
                    <div class="col-sm-10">
                        <select type="text" class="form-control form-control-sm select2_combobox" id="group_id" name="group_id">
                            <option value="">-</option>
                            @foreach ($group as $r)
                                <option value="{{ $r->group_id }}">{{ $r->group_code }} - {{ $r->group_name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Level Pengguna</small>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label for="username" class="col-sm-2 control-label col-form-label">Username</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control form-control-sm" id="username" name="username" value="{{ isset($data->username) ? $data->username : '' }}" />
                        <small class="form-text text-muted">Username harus unik</small>
                    </div>
                </div>

                <div class="form-group required row mb-2">
                    <label for="name" class="col-sm-2 control-label col-form-label">Nama</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control form-control-sm" id="name" name="name" value="{{ isset($data->name) ? $data->name : '' }}" />
                        <small class="form-text text-muted">Nama lengkap pengguna</small>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="email" class="col-sm-2 control-label col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control form-control-sm" id="email" name="email" value="{{ isset($data->email) ? $data->email : '' }}" />
                        <small class="form-text text-muted">Alamat Email</small>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label for="is_active" class="col-sm-2 control-label col-form-label">Status</label>
                    <div class="col-sm-10 mt-2">
                        <div class="icheck-{{ $theme->button }} d-inline mr-2">
                            <input type="radio" id="radioAktif" name="is_active" value="1" <?php echo isset($data->is_active)? (($data->is_active == 1)? 'checked' : '') : '' ?>>
                            <label for="radioAktif">Aktif </label>
                        </div>
                        <div class="icheck-warning d-inline">
                            <input type="radio" id="radioNonAktif" name="is_active" value="0" <?php echo isset($data->is_active)? (($data->is_active == 0)? 'checked' : '') : 'checked' ?>>
                            <label for="radioNonAktif">Non-aktif</label>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-group @if(!$is_edit) required @endif row mb-2">
                    <label for="password" class="col-sm-2 control-label col-form-label">Password</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control form-control-sm" id="password" name="password" value="" />
                        @if($is_edit)
                            <small class="form-text text-muted">Abaikan jika tidak ingin mengganti password</small>
                        @else
                            <small class="form-text text-muted">Password minimal 6 karakter</small>
                        @endif
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

        @if($is_edit)
            $('#group_id').val({{$data->group_id}}).trigger('change');
        @endif

        $('.select2_combobox').select2();

        $("#form-master").validate({
            rules: {
                group_id: {
                    required: true,
                    digits: true
                },
                username: {
                    required: true,
                    minlength: 4,
                    maxlength: 20
                },
                name: {
                    required: true,
                    minlength: 4,
                    maxlength: 50
                },
                email: {
                    email: true,
                    maxlength: 50
                },
                is_active: {
                    required: true
                },
                password: {
                    @if(!$is_edit) required: true, @endif
                    minlength: 6
                },
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
                            resetForm('#form-master');
                            dataMaster.draw(false);
                        }
                        closeModal($modal, data);
                    }
                });
            },
            validClass: "valid-feedback",
            errorElement: "div",
            errorClass: 'invalid-feedback',
            errorPlacement: erp,
            highlight: hl,
            unhighlight: uhl,
            success: sc
        });
    });
</script>

<?php
    $is_edit = false;
    if(isset($data)){ // jika $data ada ISI-nya maka actionnya adalah edit, jika KOSONG : insert
        $is_edit = true;
    }
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
                    <label for="group_code" class="col-sm-2 control-label col-form-label">Kode</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control form-control-sm" id="group_code" name="group_code" value="{{ isset($data->group_code) ? $data->group_code : '' }}" />
                        <small class="form-text text-muted">Kode Group harus unik</small>
                    </div>
                </div>

                <div class="form-group required row mb-2">
                    <label for="group_name" class="col-sm-2 control-label col-form-label">Nama</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control form-control-sm" id="group_name" name="group_name" value="{{ isset($data->group_name) ? $data->group_name : '' }}" />
                        <small class="form-text text-muted">Nama Group Pengguna</small>
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

        $("#form-master").validate({
            rules: {
                group_code: {
                    required: true,
                    maxlength: 10
                },
                group_name: {
                    required: true,
                    minlength: 4,
                    maxlength: 20
                },
                is_active: {
                    required: true
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

<?php
// jika $data ada ISI-nya maka actionnya adalah edit, jika KOSONG : insert
$is_edit = isset($data);
?>

<form method="post" action="{{ $page->url }}" role="form" class="form-horizontal" id="form-master">
    @csrf
    {!! $is_edit ? method_field('PUT') : '' !!}
    <div id="modal-master" class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-message text-center"></div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Nama</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="periode_nama" name="periode_nama"
                            value="{{ isset($data->periode_nama) ? $data->periode_nama : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Direktur</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="periode_direktur"
                            name="periode_direktur"
                            value="{{ isset($data->periode_direktur) ? $data->periode_direktur : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">NIP</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="periode_nip" name="periode_nip"
                            value="{{ isset($data->periode_nip) ? $data->periode_nip : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Status</label>
                    <div class="col-sm-9 mt-2">
                        <div class="icheck-success d-inline mr-3">
                            <input type="radio" id="radioActive" name="is_active" value="1" <?php echo isset($data->is_active) ? ($data->is_active == 1 ? 'checked' : '') : 'checked'; ?>>
                            <label for="radioActive">Aktif </label>
                        </div>
                        <div class="icheck-danger d-inline mr-3">
                            <input type="radio" id="radioFailed" name="is_active" value="0" <?php echo isset($data->is_active) ? ($data->is_active == 0 ? 'checked' : '') : ''; ?>>
                            <label for="radioFailed">non-aktif</label>
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
                periode_id: {
                    required: true,
                    digits: true,
                    min: 2022,
                    max: 2099
                },
                periode_name: {
                    required: true,
                    maxlength: 100
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

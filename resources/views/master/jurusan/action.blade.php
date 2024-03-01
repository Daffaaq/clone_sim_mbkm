<?php
    // jika $data ada ISI-nya maka actionnya adalah edit, jika KOSONG : insert
    $is_edit = isset($data);
?>

<form method="post" action="{{ $page->url }}" role="form" class="form-horizontal" id="form-master">
    @csrf
    {!! ($is_edit)? method_field('PUT') : '' !!}
    <div id="modal-master" class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-message text-center"></div>

                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Jurusan</label>
                    <div class="col-sm-9">
                        <input @if($is_edit) disabled @endif type="text" class="form-control form-control-sm" id="jurusan_code" name="jurusan_code" value="{{ isset($data->jurusan_code) ? $data->jurusan_code : '' }}"/>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Keterangan</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="jurusan_name" name="jurusan_name" value="{{ isset($data->jurusan_name) ? $data->jurusan_name : '' }}"/>
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
    $(document).ready(function () {
        unblockUI();

        $("#form-master").validate({
            rules: {
                jurusan_code: {
                    required: true,
                    maxlength: 10
                },
                jurusan_name: {
                    required: true,
                    maxlength: 100
                }
            },
            submitHandler: function (form) {
                $('.form-message').html('');
                blockUI(form);
                $(form).ajaxSubmit({
                    dataType: 'json',
                    success: function (data) {
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

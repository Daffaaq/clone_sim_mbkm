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

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group required mb-2">
                            <label class="control-label">Nama Instruktur</label>
                            <input type="text" class="form-control form-control-sm" id="nama_instruktur"
                                name="nama_instruktur"
                                value="{{ isset($data->nama_instruktur) ? $data->nama_instruktur : '' }}" />
                        </div>

                        <div class="form-group required mb-2">
                            <label class="control-label">Email Instruktur</label>
                            <input type="email" class="form-control form-control-sm" id="instruktur_email"
                                name="instruktur_email"
                                value="{{ isset($data->instruktur_email) ? $data->instruktur_email : '' }}" />
                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="form-group required mb-2">
                            <label class="control-label">No Telephone Instruktur</label>
                            <input type="number" class="form-control form-control-sm" id="instruktur_phone"
                                name="instruktur_phone"
                                value="{{ isset($data->instruktur_phone) ? $data->instruktur_phone : '' }}" />
                        </div>

                        <div class="form-group required mb-2">
                            <label class="control-label">Password Instruktur</label>
                            <input type="password" class="form-control form-control-sm" id="password" name="password"
                                value="{{ isset($data->password) ? $data->password : '' }}" />
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
                dosen_name: {
                    required: true,
                },
                dosen_email: {
                    required: true,
                },
                dosen_phone: {
                    required: true,
                },
                dosen_gender: {
                    required: true,
                },
                dosen_tahun: {
                    required: true,
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

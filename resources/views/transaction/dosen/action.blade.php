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
                        <div class="form-group mb-2">
                            <label class="control-label">NIP Dosen</label>
                            <input type="text" class="form-control form-control-sm" id="dosen_nip" name="dosen_nip"
                                value="{{ isset($data->dosen_nip) ? $data->dosen_nip : '' }}" />
                        </div>

                        <div class="form-group mb-2">
                            <label class="control-label">NIDN Dosen</label>
                            <input type="text" class="form-control form-control-sm" id="dosen_nidn" name="dosen_nidn"
                                value="{{ isset($data->dosen_nidn) ? $data->dosen_nidn : '' }}" />
                        </div>

                        <div class="form-group required mb-2">
                            <label class="control-label">Nama Dosen</label>
                            <input type="text" class="form-control form-control-sm" id="dosen_name" name="dosen_name"
                                value="{{ isset($data->dosen_name) ? $data->dosen_name : '' }}" />
                        </div>

                        <div class="form-group required mb-2">
                            <label class="control-label">Email Dosen</label>
                            <input type="email" class="form-control form-control-sm" id="dosen_email"
                                name="dosen_email" value="{{ isset($data->dosen_email) ? $data->dosen_email : '' }}" />
                        </div>

                        <div class="form-group required mb-2">
                            <label class="control-label">No Telephone Dosen</label>
                            <input type="number" class="form-control form-control-sm" id="dosen_phone"
                                name="dosen_phone" value="{{ isset($data->dosen_phone) ? $data->dosen_phone : '' }}" />
                        </div>

                    </div>

                    <div class="col-md-6">
                        <div class="form-group required mb-2">
                            <label class="control-label">Jenis Kelamin Dosen</label>
                            <select class="form-control form-control-sm" id="dosen_gender" name="dosen_gender">
                                <option disabled selected value="">Pilih opsi</option>
                                <option value="L"
                                    {{ isset($data->dosen_gender) && $data->dosen_gender === 'L' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="P"
                                    {{ isset($data->dosen_gender) && $data->dosen_gender === 'P' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                        </div>
                        
                        <div class="form-group required mb-2">
                            <label class="control-label">Tahun Dosen</label>
                            <input type="number" class="form-control form-control-sm" id="dosen_tahun"
                                name="dosen_tahun" value="{{ isset($data->dosen_tahun) ? $data->dosen_tahun : '' }}" />
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

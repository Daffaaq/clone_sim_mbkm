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
                    <label class="col-sm-3 control-label col-form-label">Program</label>
                    <div class="col-sm-9">
                        <select data-testid="partner-category" class="form-control form-control-sm" id="program_id"
                            name="program_id" value="{{ isset($data->program_id) ? $data->program_id : '' }}">
                            <option disabled selected value="">Pilih opsi</option>
                            @foreach ($programs as $program)
                                <option value="{{ $program->program_id }}"
                                    {{ isset($data->program_id) ? ($program->program_id == $data->program_id ? 'selected' : '') : '' }}>
                                    {{ $program->program_kode }} | {{ $program->program_nama }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Nama Kegiatan</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="kegiatan_nama"
                            name="kegiatan_nama"
                            value="{{ isset($data->kegiatan_nama) ? $data->kegiatan_nama : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Skema</label>
                    <div class="col-sm-9">
                        <select data-testid="partner-category" class="form-control form-control-sm" id="kegiatan_skema"
                            name="kegiatan_skema"
                            value="{{ isset($data->kegiatan_skema) ? $data->kegiatan_skema : '' }}">
                            <option disabled selected value="">Pilih opsi</option>
                            <option value="S"
                                {{ isset($data->kegiatan_skema) ? ($data->kegiatan_skema == 'S' ? 'selected' : '') : '' }}>
                                Studi</option>
                            <option value="C"
                                {{ isset($data->kegiatan_skema) ? ($data->kegiatan_skema == 'C' ? 'selected' : '') : '' }}>
                                Course</option>
                            <option value="M"
                                {{ isset($data->kegiatan_skema) ? ($data->kegiatan_skema == 'M' ? 'selected' : '') : '' }}>
                                Magang</option>
                        </select>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Deskripsi Program</label>
                    <div class="col-sm-9">
                        <textarea type="text" class="form-control form-control-sm" id="kegiatan_deskripsi" name="kegiatan_deskripsi">{{ isset($data->kegiatan_deskripsi) ? $data->kegiatan_deskripsi : '' }}</textarea>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Kuota</label>
                    <div class="col-sm-9 mt-2">
                        <div class="icheck-success d-inline mr-3">
                            <input type="radio" id="radioActive" name="is_kuota" value="1" <?php echo isset($data->is_kuota) ? ($data->is_kuota == 1 ? 'checked' : '') : ''; ?>>
                            <label for="radioActive">Berkuota</label>
                        </div>
                        <div class="icheck-danger d-inline mr-3">
                            <input type="radio" id="radioFailed" name="is_kuota" value="0" <?php echo isset($data->is_kuota) ? ($data->is_kuota == 0 ? 'checked' : '') : ''; ?>>
                            <label for="radioFailed">Tidak ada kuota</label>
                        </div>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Pengajuan Mandiri</label>
                    <div class="col-sm-9 mt-2">
                        <div class="icheck-success d-inline mr-3">
                            <input type="radio" id="radioMandiriActive" name="is_mandiri" value="1"
                                <?php echo isset($data->is_mandiri) ? ($data->is_mandiri == 1 ? 'checked' : '') : ''; ?>>
                            <label for="radioMandiriActive">Pengajuan Mandiri</label>
                        </div>
                        <div class="icheck-danger d-inline mr-3">
                            <input type="radio" id="radioMandiriFailed" name="is_mandiri" value="0"
                                <?php echo isset($data->is_mandiri) ? ($data->is_mandiri == 0 ? 'checked' : '') : 'checked'; ?>>
                            <label for="radioMandiriFailed">Bukan Pengajuan Mandiri</label>
                        </div>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Perlu Submit Proposal</label>
                    <div class="col-sm-9 mt-2">
                        <div class="icheck-success d-inline mr-3">
                            <input type="radio" id="radioProposalActive" name="is_submit_proposal" value="1"
                                <?php echo isset($data->is_submit_proposal) ? ($data->is_submit_proposal == 1 ? 'checked' : '') : ''; ?>>
                            <label for="radioProposalActive">Ya</label>
                        </div>
                        <div class="icheck-danger d-inline mr-3">
                            <input type="radio" id="radioProposalFailed" name="is_submit_proposal" value="0"
                                <?php echo isset($data->is_submit_proposal) ? ($data->is_submit_proposal == 0 ? 'checked' : '') : 'checked'; ?>>
                            <label for="radioProposalFailed">Tidak</label>
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
                nama_program: {
                    required: true,
                    maxlength: 100
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

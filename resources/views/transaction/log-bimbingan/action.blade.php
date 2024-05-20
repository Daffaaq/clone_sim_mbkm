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
                    <label for="pembimbing_dosen_id" class="col-sm-3 control-label col-form-label">Dosen
                        Pembimbing</label>
                    <div class="col-sm-9 d-flex align-items-center">
                        <span id="pembimbing_dosen_name">{{ $dosen_name }}</span>
                        <input type="hidden" id="pembimbing_dosen_id" name="pembimbing_dosen_id"
                            value="{{ $pembimbingdosen_id }}">
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label for="instruktur_lapangan_id" class="col-sm-3 control-label col-form-label">Instruktur</label>
                    <div class="col-sm-9 d-flex align-items-center">
                        <span id="instruktur_name">{{ $instruktur_name }}</span>
                        <input type="hidden" id="instruktur_lapangan_id" name="instruktur_lapangan_id"
                            value="{{ $instrukturLapangan_id }}">
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Tanggal</label>
                    <div class="col-sm-9">
                        <input type="date" class="form-control form-control-sm" id="tanggal" name="tanggal"
                            value="{{ isset($data->tanggal) ? $data->tanggal : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Jam Mulai</label>
                    <div class="col-sm-9">
                        <input type="time" class="form-control form-control-sm" id="jam_mulai" name="jam_mulai"
                            value="{{ isset($data->jam_mulai) ? $data->jam_mulai : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Jam Selesai</label>
                    <div class="col-sm-9">
                        <input type="time" class="form-control form-control-sm" id="jam_selesai" name="jam_selesai"
                            value="{{ isset($data->jam_selesai) ? $data->jam_selesai : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Topik Bimbingan</label>
                    <div class="col-sm-9">
                        <textarea class="form-control form-control summernote " id="topik_bimbingan" name="topik_bimbingan" value="">
                        {!! isset($data->topik_bimbingan) ? $data->topik_bimbingan : '' !!}
                        </textarea>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Foto Kegiatan hari ini</label>
                    <div class="col-sm-9">
                        @if (isset($data->foto))
                            <input type="hidden" name="existing_foto" value="{{ $data->foto }}">
                        @endif
                        <input type="file" class="form-control-file" id="foto" name="foto">
                        <small id="fotoHelp" class="form-text text-muted">Max file size: 2048KB (2MB). Must be in .png,
                            .jpg, or .jpeg format.</small>
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
        $(document).ready(function() {
            // $('.summernote').summernote({
            //     height: 200, // Sesuaikan tinggi Summernote sesuai kebutuhan
            //     callbacks: {
            //         onBlur: function() {
            //             $(this).val($(this).summernote('code'));
            //         }
            //     }
            // });
            $('#topik_bimbingan').summernote({
                tabsize: 2,
                height: 200,
                dialogsInBody: true,
                codeviewFilter: true,
                codeviewIframeFilter: true,
                popover: {
                    air: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['fontname', ['fontname']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']],
                    ]
                }
            });
        });
        unblockUI();

        // @if ($is_edit)
        //     $('#pembimbing_dosen_id').val('{{ $data->pembimbing_dosen_id }}').trigger('change');
        //     $('#instruktur_lapangan_id').val('{{ $data->instruktur_lapangan_id }}').trigger('change');
        // @endif

        $("#form-master").validate({
            rules: {
                pembimbing_dosen_id: {
                    required: true,
                },
                instruktur_lapangan_id: {
                    required: true,
                },
                tanggal: {
                    required: true,
                },
                jam_mulai: {
                    required: true,
                },
                jam_selesai: {
                    required: true,
                },
                topik_bimbingan: {
                    required: true,
                },
                foto: {
                    // Tambahkan aturan 'required' hanya jika foto baru diunggah
                    required: function(element) {
                        // Periksa apakah ada file foto yang diunggah
                        return $('input[name=foto]').get(0).files.length > 0;
                    }
                }
            },
            submitHandler: function(form) {
                if ($('#topik_bimbingan').summernote('isEmpty')) {
                    setFormMessage('.form-message', {
                        stat: false,
                        msg: 'Deskripsi harus diisi',
                        msgField: {
                            'topik_bimbingan': 'Deskripsi harus diisi'
                        }
                    });
                    return false;
                }
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

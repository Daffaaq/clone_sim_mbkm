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
                    <label class="col-sm-3 control-label col-form-label">Dosen Pembimbing</label>
                    <div class="col-sm-9">
                        <select id="pembimbing_dosen_id" name="pembimbing_dosen_id"
                            class="form-control form-control-sm select2_combobox">
                            <option value="{{ isset($pembimbingdosen_id) ? $pembimbingdosen_id : '' }}" selected>
                                {{ $dosen_name }}</option>

                        </select>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Instruktur</label>
                    <div class="col-sm-9">
                        <select id="instruktur_lapangan_id" name="instruktur_lapangan_id"
                            class="form-control form-control-sm select2_combobox">
                            <option value="{{ isset($instrukturLapangan_id) ? $instrukturLapangan_id : '' }}" selected>
                                {{ $instruktur_name }}</option>

                        </select>

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
                        {{ isset($data->topik_bimbingan) ? $data->topik_bimbingan : '' }}
                        </textarea>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Foto Kegiatan hari ini</label>
                    <div class="col-sm-9">
                        @if (isset($data->foto))
                            <input type="hidden" name="existing_foto" value="{{ $data->foto }}">
                            <p>Foto saat ini: {{ $data->foto }}</p>
                        @endif
                        <input type="file" class="form-control-file" id="foto" name="foto">


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
            $('.summernote').summernote({
                height: 200,
                callbacks: {
                    onBlur: function() {
                        // Hapus karakter spasi dan tag br sebelum menyimpan konten
                        var content = $(this).summernote('code').replace(/&nbsp;/g, ' ')
                            .replace(/<br\s*[\/]?>/gi, '\n');
                        $(this).val(content);
                    },
                    onKeydown: function(e) {
                        if (e.keyCode === 13) { // Jika tombol enter ditekan
                            var cursorPosition = $(this).summernote('core.editor')
                                .getCursorPosition();
                            var contentAfterCursor = $(this).summernote('core.editor')
                                .getTextRange(cursorPosition, cursorPosition + 1);
                            if (contentAfterCursor === " " || contentAfterCursor === "\n") {
                                e
                                    .preventDefault(); // Hentikan penambahan baris baru jika hanya ada spasi atau baris baru setelah kursor
                                return false;
                            }
                        }
                    }
                },
                disableNormalizeContent: true
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

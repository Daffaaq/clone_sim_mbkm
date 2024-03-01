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
                {{-- <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Prodi</label>
                    <div class="col-sm-9">
                        <select data-testid="partner-category" class="form-control form-control-sm" id="prodi_id"
                            name="prodi_id" value="{{ isset($data->prodi_id) ? $data->prodi_id : '' }}">
                            <option disabled selected value="">Pilih opsi</option>
                            @foreach ($prodis as $prodi)
                                <option value="{{ $prodi->prodi_id }}"
                                    {{ isset($data->prodi_id) ? ($data->prodi_id == $prodi->prodi_id ? 'selected' : '') : '' }}>
                                    {{ $prodi->prodi_name }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Periode</label>
                    <div class="col-sm-9">
                        <select data-testid="partner-category" class="form-control form-control-sm" id="periode_id"
                            name="periode_id" value="{{ isset($data->periode_id) ? $data->periode_id : '' }}">
                            <option disabled selected value="">Pilih opsi</option>
                            @foreach ($periodes as $periode)
                                <option value="{{ $periode->periode_id }}"
                                    {{ isset($data->periode_id) ? ($periode->periode_id == $data->periode_id ? 'selected' : '') : '' }}>
                                    {{ $periode->semester }} - {{ $periode->tahun_ajar }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                </div> --}}
                @if ($is_edit)
                    <div class="form-group required row mb-2">
                        <label class="col-sm-3 control-label col-form-label">Kode Program</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-sm" id="program_kode"
                                name="program_kode" value="{{ isset($data->program_kode) ? $data->program_kode : '' }}"
                                @if ($is_edit) readonly @endif />
                        </div>
                    </div>
                @endif
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Nama Program</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="program_nama" name="program_nama"
                            value="{{ isset($data->program_nama) ? $data->program_nama : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Deskripsi Program</label>
                    <div class="col-sm-9">
                        <textarea type="text" class="form-control form-control-sm" id="program_deskripsi" name="program_deskripsi">{{ isset($data->program_deskripsi) ? $data->program_deskripsi : '' }}</textarea>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Bulan</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="program_bulan"
                            name="program_bulan"
                            value="{{ isset($data->program_bulan) ? $data->program_bulan : '' }}" />
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

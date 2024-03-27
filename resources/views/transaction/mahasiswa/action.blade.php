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
                @if (Auth::user()->group_id == 1)
                    <div class="form-group required row mb-2">
                        <label class="col-sm-3 control-label col-form-label">Prodi</label>
                        <div class="col-sm-9">
                            <select data-testid="partner-category" class="form-control form-control-sm" id="prodi_id"
                                name="prodi_id" value="{{ isset($data->prodi_id) ? $data->prodi_id : '' }}">
                                <option disabled selected value="">Pilih opsi</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->prodi_id }}"
                                        {{ isset($data->prodi_id) ? ($data->prodi_id == $prodi->prodi_id ? 'selected' : '') : '' }}>
                                        {{ $prodi->prodi_code }} - {{ $prodi->prodi_name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                @endif
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">NIM</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="nim" name="nim"
                            value="{{ isset($data->nim) ? $data->nim : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Nama Mahasiswa</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="nama_mahasiswa"
                            name="nama_mahasiswa"
                            value="{{ isset($data->nama_mahasiswa) ? $data->nama_mahasiswa : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Kelas</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="kelas" name="kelas"
                            value="{{ isset($data->kelas) ? $data->kelas : '' }}" />
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Email</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="email_mahasiswa"
                            name="email_mahasiswa"
                            value="{{ isset($data->email_mahasiswa) ? $data->email_mahasiswa : '' }}" />
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label class="col-sm-3 control-label col-form-label">No HP</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="no_hp" name="no_hp"
                            value="{{ isset($data->no_hp) ? $data->no_hp : '' }}" />
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Jenis Kelamin</label>
                    <div class="col-sm-9">
                        <select data-testid="partner-category" class="form-control form-control-sm" id="jenis_kelamin"
                            name="jenis_kelamin" value="{{ isset($data->jenis_kelamin) ? $data->jenis_kelamin : '' }}">
                            <option disabled selected value="">Pilih opsi</option>
                            <option value="1"
                                {{ isset($data->jenis_kelamin) ? ($data->jenis_kelamin == 1 ? 'selected' : '') : '' }}>
                                Laki-laki
                            </option>
                            <option value="0"
                                {{ isset($data->jenis_kelamin) ? ($data->jenis_kelamin == 0 ? 'selected' : '') : '' }}>
                                Perempuan
                            </option>
                        </select>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Nama Orang Tua</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="nama_ortu" name="nama_ortu"
                            value="{{ isset($data->nama_ortu) ? $data->nama_ortu : '' }}" />
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label class="col-sm-3 control-label col-form-label">HP Orang Tua</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="hp_ortu" name="hp_ortu"
                            value="{{ isset($data->hp_ortu) ? $data->hp_ortu : '' }}" />
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
                // nama_kegiatan: {
                //     required: true,
                //     maxlength: 100
                // }
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

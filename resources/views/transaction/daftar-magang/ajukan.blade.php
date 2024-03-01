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
                    <label class="col-sm-3 control-label col-form-label">Kegiatan</label>
                    <div class="col-sm-9">
                        <select data-testid="partner-category" class="form-control form-control-sm" id="kegiatan_id"
                            name="kegiatan_id">
                            <option disabled selected value="">Pilih Kegiatan</option>
                            @foreach ($kegiatans as $kegiatan)
                                <option value="{{ $kegiatan->kegiatan_id }}"
                                    {{ isset($data->kegiatan_id) && $data->kegiatan_id == $kegiatan->kegiatan_id ? 'selected' : '' }}>
                                    {{ $kegiatan->kegiatan_nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
               
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Nama Mitra</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="mitra_nama" name="mitra_nama"
                            value="{{ isset($data->mitra_nama) ? $data->mitra_nama : '' }}" />
                    </div>
                </div>

                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Durasi</label>
                    <div class="col-sm-9">
                        <select data-testid="partner-category" class="form-control form-control-sm" id="mitra_durasi"
                            name="mitra_durasi">
                            <option disabled selected value="">Pilih durasi</option>
                            @foreach (range(1, 12) as $month)
                                <option value="{{ $month }}"
                                    {{ isset($data->mitra_durasi) && $data->mitra_durasi == $month ? 'selected' : '' }}>
                                    {{ $month }} Bulan
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Alamat</label>
                    <div class="col-sm-9">
                        <textarea type="text" class="form-control form-control-sm" id="mitra_alamat" name="mitra_alamat">{{ isset($data->mitra_alamat) ? $data->mitra_alamat : '' }}</textarea>
                    </div>
                </div> --}}
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Provinsi</label>
                    <div class="col-sm-9">
                        <select data-testid="partner-category" class="form-control form-control-sm" id="provinsi_id"
                            name="provinsi_id">
                            <option disabled selected value="">Pilih provinsi</option>
                            @foreach ($provinsis as $provinsi)
                                <option value="{{ $provinsi->id }}"
                                    {{ isset($data->provinsi_id) && $data->provinsi_id == $provinsi->id ? 'selected' : '' }}>
                                    {{ $provinsi->nama_provinsi }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Kabupaten/Kota</label>
                    <div class="col-sm-9">
                        <select data-testid="partner-category" class="form-control form-control-sm" id="kota_id"
                            name="kota_id">
                            <option disabled selected value="">Pilih kabupaten/kota</option>
                            @foreach ($kabupatens as $kabupaten)
                                <option value="{{ $kabupaten->id }}"
                                    {{ isset($data->kota_id) && $data->kota_id == $kabupaten->id ? 'selected' : '' }}>
                                    {{ $kabupaten->nama_kab_kota }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Website</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="mitra_website"
                            name="mitra_website"
                            value="{{ isset($data->mitra_website) ? $data->mitra_website : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Deskripsi</label>
                    <div class="col-sm-9">
                        <textarea type="text" class="form-control form-control-sm" id="mitra_deskripsi" name="mitra_deskripsi">{{ isset($data->mitra_deskripsi) ? $data->mitra_deskripsi : '' }}</textarea>
                    </div>
                </div>
                {{-- <div class="form-group required row mb-2">
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
                </div> --}}
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

        $('#provinsi_id').change(function() {
            var provinsi_id = $(this).val();
            if (provinsi_id) {
                $.ajax({
                    url: "{{ url('kota') }}?provinsi_id=" + provinsi_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#kota_id').empty();
                        $('#kota_id').append(
                            '<option disabled selected value="">Pilih kabupaten/kota</option>'
                        );
                        $.each(data, function(key, value) {
                            $('#kota_id').append('<option value="' + value.id +
                                '">' + value.nama_kab_kota +
                                '</option>');
                        });
                    }
                });
            } else {
                $('#kota_id').empty();
                $('#kota_id').append(
                    '<option disabled selected value="">Pilih kabupaten/kota</option>');
            }
        });

        $("#form-master").validate({
            rules: {
                nama_perusahaan: {
                    required: true,
                },
                kategori: {
                    required: true
                },
                tipe_industri: {
                    required: true
                },
                alamat: {
                    required: true
                },
                provinsi_id: {
                    required: true
                },
                kota_id: {
                    required: true
                },
                profil_perusahaan: {
                    required: true
                },
                website: {
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

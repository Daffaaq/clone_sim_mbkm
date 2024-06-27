<?php
// Jika $data ada isinya maka actionnya adalah edit, jika KOSONG : insert
$is_edit = isset($data);
?>

<form method="post" action="{{ $page->url }}" role="form" class="form-horizontal" id="form-master">
    @csrf
    {!! $is_edit ? method_field('PUT') : '' !!}
    <div id="modal-master" class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-message text-center"></div>
                @if ($is_edit)
                    <div class="form-group required row mb-2">
                        <label class="col-sm-3 control-label col-form-label">Dosen Pembahas</label>
                        <div class="col-sm-9">
                            <select id="dosen_pembahas_id" name="dosen_pembahas_id"
                                class="form-control form-control-sm select2_combobox">
                                <option value="">- Pilih -</option>
                                @foreach ($dosen as $r)
                                    <option value="{{ $r->dosen_id }}"
                                        {{ isset($data) && $data->dosen_pembahas_id == $r->dosen_id ? 'selected' : '' }}>
                                        {{ $r->dosen_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Tanggal Sidang</label>
                    <div class="col-sm-9">
                        <input type="date" id="tanggal_sidang" name="tanggal_sidang"
                            class="form-control form-control-sm"
                            value="{{ isset($datajadwal) ? $datajadwal->tanggal_sidang : '' }}" required>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Deadline nilai</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="number" class="form-control form-control-sm" disabled
                                value="{{ $semhasDaftar->semhas->deadline_nilai }}">
                            <div class="input-group-append">
                                <label class="input-group-text form-control-sm custom-deadline_nilai-label"
                                    for="deadline_nilai">Hari</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Deadline Penilaian</label>
                    <div class="col-sm-9">
                        <p id="deadline_penilaian_display"></p>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Jam Mulai Sidang</label>
                    <div class="col-sm-9">
                        <input type="time" id="jam_sidang_mulai" name="jam_sidang_mulai"
                            class="form-control form-control-sm"
                            value="{{ isset($datajadwal) ? $datajadwal->jam_sidang_mulai : '' }}" required>
                    </div>
                </div>

                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Jam Selesai Sidang</label>
                    <div class="col-sm-9">
                        <input type="time" id="jam_sidang_selesai" name="jam_sidang_selesai"
                            class="form-control form-control-sm"
                            value="{{ isset($datajadwal) ? $datajadwal->jam_sidang_selesai : '' }}" required>
                    </div>
                </div>

                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Jenis Sidang</label>
                    <div class="col-sm-9">
                        <select id="jenis_sidang" name="jenis_sidang" class="form-control form-control-sm">
                            <option value="" disabled selected>Pilih Jenis Sidang</option>
                            <option value="online"
                                {{ isset($datajadwal) && $datajadwal->jenis_sidang == 'online' ? 'selected' : '' }}>
                                Online</option>
                            <option value="offline"
                                {{ isset($datajadwal) && $datajadwal->jenis_sidang == 'offline' ? 'selected' : '' }}>
                                Offline
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group required row mb-2" id="tempat_div">
                    <label class="col-sm-3 control-label col-form-label">Tempat Sidang</label>
                    <div class="col-sm-9">
                        <input type="text" id="tempat" name="tempat" class="form-control form-control-sm"
                            value="{{ isset($datajadwal) ? $datajadwal->tempat : '' }}" required>
                    </div>
                </div>

                <div class="form-group required row mb-2" id="gedung_div" style="display: none;">
                    <label class="col-sm-3 control-label col-form-label">Gedung Sidang</label>
                    <div class="col-sm-9">
                        <input type="text" id="gedung" name="gedung" class="form-control form-control-sm"
                            value="{{ isset($datajadwal) ? $datajadwal->gedung : '' }}" required>
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
    function calculateAndDisplayDeadline() {
        var tanggalSidang = $('#tanggal_sidang').val(); // Ambil nilai tanggal_sidang dari input form
        var deadlineNilai = {!! json_encode($semhasDaftar->semhas->deadline_nilai) !!}; // Ambil nilai deadline_nilai dari PHP (controller atau template)

        if (tanggalSidang) {
            var tanggalSidangMoment = moment(tanggalSidang, 'YYYY-MM-DD'); // Buat objek moment dari tanggal_sidang
            var deadlinePenilaian = tanggalSidangMoment.add(deadlineNilai, 'days').format(
                'DD-MM-YYYY'); // Hitung deadline_penilaian
            // Buat badge dengan kelas warna yang sesuai
            // Buat badge dengan kelas warna tetap 'badge-info'
            $('#deadline_penilaian_display').html(
                '<input type="text" class="form-control form-control-sm" disabled value="' + deadlinePenilaian +
                '">');
        } else {
            // Jika tanggalSidang kosong, tampilkan badge danger
            var badgeHTML = '<span class="badge badge-danger badge-lg">Belum ditentukan</span>';
            $('#deadline_penilaian_display').html(badgeHTML);
        }
    }

    function toggleGedungInput() {
        var jenisSidang = $('#jenis_sidang').val();
        if (jenisSidang === 'offline') {
            $('#gedung_div').show();
            $('#tempat_div').show();
            $('#gedung').prop('required', true);
        } else if (jenisSidang === 'online') {
            $('#gedung_div').hide();
            $('#tempat_div').show();
            $('#gedung').prop('required', false);
        }
    }
    $(document).ready(function() {
        unblockUI();
        calculateAndDisplayDeadline();

        // Panggil fungsi calculateAndDisplayDeadline saat nilai tanggal_sidang berubah
        $('#tanggal_sidang').change(function() {
            calculateAndDisplayDeadline();
        });
        // Fungsi untuk menampilkan atau menyembunyikan input gedung sidang berdasarkan jenis sidang

        // Panggil fungsi toggleGedungInput() saat halaman dimuat
        toggleGedungInput();

        // Panggil fungsi toggleGedungInput() saat nilai jenis sidang berubah
        $('#jenis_sidang').change(function() {
            toggleGedungInput();
        });
        $("#form-master").validate({
            rules: {
                tanggal_sidang: {
                    required: true
                },
                jam_sidang_mulai: {
                    required: true
                },
                jam_sidang_selesai: {
                    required: true
                },
                jenis_sidang: {
                    required: true
                },
                tempat: {
                    required: true
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

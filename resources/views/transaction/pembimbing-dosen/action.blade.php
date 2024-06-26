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
                    <label class="col-sm-3 control-label col-form-label">Mahasiswa</label>
                    <div class="col-sm-9">
                        <select id="mahasiswa_id" name="mahasiswa_id[]"
                            class="form-control form-control-sm select2_combobox" multiple>
                            <option value="" disabled>- Pilih -</option>
                            @if (count($mahasiswa) > 0)
                                {{-- @foreach ($mahasiswa as $mahasiswa_id => $r)
                                    <option value="{{ $mahasiswa_id }}" data-magang-id="{{ $r['magang_id'] }}">
                                        {{ $r['nama_mahasiswa'] }}
                                    </option>
                                @endforeach --}}
                                @foreach ($mahasiswa as $mahasiswa_id => $r)
                                    @php
                                        $nama_mahasiswa = $r['nama_mahasiswa'];
                                        $magang_id = $r['magang_id'];
                                        // Ambil informasi kode magang dari model Magang
                                        $kode_magang =
                                            \App\Models\Transaction\Magang::find($magang_id)->magang_kode ?? '';
                                    @endphp
                                    <option value="{{ $mahasiswa_id }}" data-magang-id="{{ $magang_id }}">
                                        {{ $nama_mahasiswa }} - {{ $kode_magang }}
                                    </option>
                                @endforeach
                            @else
                                <option disabled selected>Tidak ada mahasiswa yang diterima magang</option>
                            @endif
                        </select>
                        <span id="validation-message" class="text-danger" style="display: none;">Mohon pilih setidaknya
                            satu
                            mahasiswa.</span>
                    </div>
                </div>
                <div class="modal-body">
                    {{-- <div class="form-message text-center"></div> --}}
                    <div class="form-group required row mb-2">
                        <label class="col-sm-3 control-label col-form-label">Dosen Pembimbing</label>
                        <div class="col-sm-9">
                            <select id="dosen_id" name="dosen_id"
                                class="form-control form-control-sm select2_combobox">
                                <option value="">- Pilih -</option>
                                @foreach ($dosen as $r)
                                    <option value="{{ $r->dosen_id }}"
                                        {{ isset($data) && $data->dosen_id == $r->dosen_id ? 'selected' : '' }}>
                                        {{ $r->dosen_name }}
                                    </option>
                                @endforeach
                            </select>
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
{{-- <script src="/assets/js/select2.min.js"></script> --}}
<script>
    $(document).ready(function() {
        // function validateMahasiswaSelection() {
        //     var selectedMahasiswa = $('input[name="mahasiswa_id[]"]:checked').length;
        //     if (selectedMahasiswa === 0) {
        //         $('#validation-message').show(); // Tampilkan pesan validasi
        //         return false; // Batalkan pengiriman formulir
        //     } else {
        //         $('#validation-message').hide(); // Sembunyikan pesan validasi jika valid
        //         return true; // Lanjutkan pengiriman formulir
        //     }
        // }
        function validateForm() {
            var selectedMahasiswa = $('#mahasiswa_id').val();
            var isValid = true;

            if (!selectedMahasiswa || selectedMahasiswa.length === 0) {
                $('#validation-message').show(); // Tampilkan pesan validasi untuk Mahasiswa
                isValid = false; // Set isValid ke false jika Mahasiswa kosong
            } else {
                $('#validation-message')
                    .hide(); // Sembunyikan pesan validasi untuk Mahasiswa jika valid
            }

            return isValid; // Kembalikan nilai isValid
        }

        function checkInitialMahasiswaSelection() {
            var selectedMahasiswa = $('#mahasiswa_id').val();
            if (selectedMahasiswa && selectedMahasiswa.length > 0) {
                $('#validation-message').hide(); // Sembunyikan pesan validasi jika sudah ada pilihan
            } else {
                $('#validation-message').show(); // Tampilkan pesan validasi jika tidak ada pilihan
            }
        }

        // Memeriksa pilihan mahasiswa saat modal dibuka
        $('#modal-master').on('shown.bs.modal', function() {
            checkInitialMahasiswaSelection();
        });
        $('#mahasiswa_id').select2({
            placeholder: "Pilih satu atau lebih Mahasiswa",
            allowClear: true
        });
        unblockUI();


        @if ($is_edit)
            $('#mahasiswa_id').val('{{ $data->mahasiswa_id }}').trigger('change');
            $('#dosen_id').val('{{ $data->dosen_id }}').trigger('change');
        @endif

        $("#form-master").validate({
            rules: {
                'mahasiswa_id[]': {
                    required: true
                },
                dosen_id: {
                    required: true
                }
            },
            submitHandler: function(form) {
                if (!validateForm()) {
                    return false; // Batalkan pengiriman formulir jika validasi gagal
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

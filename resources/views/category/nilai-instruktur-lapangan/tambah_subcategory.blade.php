<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body p-0">
            <div id="success-message" class="alert alert-success" style="display: none;"></div>
            <div id="error-message" class="alert alert-danger text-center" style="display: none;"></div>
            <div class="form-group required row mb-2">
                <label class="col-sm-3 control-label col-form-label">Judul</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control form-control-sm"
                        value="{{ isset($data->name_kriteria_instruktur_lapangan) ? $data->name_kriteria_instruktur_lapangan : '' }}"
                        readonly />
                </div>
            </div>
            <div class="form-group required row mb-2">
                <label class="col-sm-3 control-label col-form-label">Bobot Kriteria</label>
                <div class="col-sm-8">
                    <input type="number" class="form-control form-control-sm"
                        value="{{ isset($data->bobot) ? $data->bobot : '' }}" readonly />
                </div>
            </div>
            <form id="update-status-form">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $id }}">
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Sub Kategori</label>
                    <div class="col-sm-8 d-flex flex-column text-left pr-0 justify-content-center">
                        <div id="skema_form">
                            <div class="form-group required row mb-2">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="sub-kriteria"
                                        name="name_kriteria_instruktur_lapangan[]"
                                        placeholder="Kemampuan dalam berkomunikasi" />
                                    <div class="input-group-append">
                                        <a class="ml-2 cursor-pointer remove-btn"><i
                                                class="text-danger fa fa-trash"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-success mt-2 cursor-pointer" id="tambah_skema">+ Tambah Skema</div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-warning">Keluar</button>
            <button type="submit" form="update-status-form" class="btn btn-primary">Simpan</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Handler untuk tombol tambah sub kategori
        $('#tambah_skema').click(function() {
            let form = $('#skema_form') // Ambil elemen terakhir
            var inputHtml = '<div class="form-group required row mb-2">' +
                '<div class="input-group">' +
                '<input type="text" class="form-control form-control-sm" id="name_kriteria_pembimbing_dosen[]" name="name_kriteria_instruktur_lapangan[]" />' +
                '<div class="input-group-append">' +
                '<a class="ml-2 cursor-pointer remove-btn"><i class="text-danger fa fa-trash"></i></a>' +
                '</div>' +
                '</div>' +
                '</div>';
            form.append(inputHtml); // Masukkan HTML baru ke dalam form
        });

        // Handler untuk tombol hapus sub kategori
        $(document).on('click', '.remove-btn', function() {
            // Menghitung jumlah form-group dalam kelompok yang sama dengan tombol yang diklik
            var formGroups = $(this).closest('.col-sm-8').find('.form-group');
            // Memastikan bahwa ada lebih dari satu form-group sebelum menghapusnya
            if (formGroups.length > 1) {
                // Jika lebih dari satu, hapus form-group yang terkait dengan tombol yang diklik
                $(this).closest('.form-group').remove();
            } else {
                // Jika hanya ada satu, tampilkan pesan bahwa elemen tidak bisa dihapus
                $('#error-message').text(
                        'Tidak bisa menghapus form sub Kriteria karena hanya ada satu form Kriteria')
                    .show();
            }
        });
        // Atur aturan validasi pada form
        $('#update-status-form').validate({
            rules: {
                'sub-kriteria': {
                    required: true
                }
            },
            messages: {
                name_kriteria_instruktur_lapangan: "Nama sub kategori harus diisi"
            },
            submitHandler: function(form) {
                var subKriteriaInput = $('input[name="name_kriteria_instruktur_lapangan[]"]');
                var isAnyEmpty =
                    false; // Variabel untuk menandai apakah setidaknya satu input kosong

                subKriteriaInput.each(function() {
                    if ($(this).val().trim() === '') {
                        isAnyEmpty = true;
                        return false; // Keluar dari loop jika menemukan input yang kosong
                    }
                });

                if (isAnyEmpty) {
                    // Jika setidaknya satu input kosong, tampilkan pesan kesalahan
                    $('#error-message').text(
                        'Tidak dapat menambahkan subkriteria karena inputan kosong').show();
                    return; // Hentikan proses submit
                } else {
                    // Jika semua input tidak kosong, sembunyikan pesan kesalahan dan lanjutkan proses submit
                    $('#error-message').hide();
                    var formData = {
                        'name_kriteria_instruktur_lapangan': $(
                            'input[name="name_kriteria_instruktur_lapangan[]"]').map(
                            function() {
                                return $(this).val();
                            }).get(),
                        'parent_id': $('input[name=parent_id]').val()
                    };

                    // Kirim permintaan AJAX ke server
                    $.ajax({
                        url: '{{ route('nilai-instruktur-lapangan.tambah_sub_category', ['id' => $id]) }}',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.stat) {
                                $('#success-message').text('Data berhasil ditambahkan')
                                    .show();
                                if (response.firstTimeAddition) {
                                    closeModal($modal, response);
                                    location.reload();
                                } else {
                                    closeModal($modal,
                                        response); // Jika tidak, cukup tutup modal saja
                                }
                                // Jika berhasil, tambahkan kode di sini untuk menangani respons yang diterima
                            } else {
                                alert('Gagal menambahkan data: ' + response.msg);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            }
        });
    });
</script>

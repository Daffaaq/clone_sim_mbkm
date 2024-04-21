<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body p-0">
            <div id="success-message" class="alert alert-success" style="display: none;"></div>
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
                                    <input type="text" class="form-control form-control-sm"
                                        name="name_kriteria_instruktur_lapangan[]" />
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
                '<input type="text" class="form-control form-control-sm" name="name_kriteria_instruktur_lapangan[]" />' +
                '<div class="input-group-append">' +
                '<a class="ml-2 cursor-pointer remove-btn"><i class="text-danger fa fa-trash"></i></a>' +
                '</div>' +
                '</div>' +
                '</div>';
            form.append(inputHtml); // Masukkan HTML baru ke dalam form
        });

        // Handler untuk tombol hapus sub kategori
        $(document).on('click', '.remove-btn', function() {
            $(this).closest('.form-group').remove();
        });
        // Atur aturan validasi pada form
        $('#update-status-form').validate({
            rules: {
                'name_kriteria_instruktur_lapangan[]': {
                    required: true
                }
            },
            messages: {
                name_kriteria_instruktur_lapangan: "Nama sub kategori harus diisi"
            },
            submitHandler: function(form) {
                var formData = {
                    'name_kriteria_instruktur_lapangan': $(
                        'input[name="name_kriteria_instruktur_lapangan[]"]').map(function() {
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
        });
    });
</script>

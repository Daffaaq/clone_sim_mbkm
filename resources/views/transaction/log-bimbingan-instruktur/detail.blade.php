<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body p-0">
            <div id="success-message" class="alert alert-success" style="display: none;"></div>
            <table class="table table-sm mb-0">
                <tr>
                    <th class="w-25 text-right">Tanggal</th>
                    <th class="w-1">:</th>
                    <td class="w-74">{{ $data->tanggal }}</td>
                </tr>
                <tr>
                    <th class="w-25 text-right">Jam Mulai</th>
                    <th class="w-1">:</th>
                    <td class="w-74">{{ $data->jam_mulai }}</td>
                </tr>
                <tr>
                    <th class="w-25 text-right">Jam Selesai</th>
                    <th class="w-1">:</th>
                    <td class="w-74">{{ $data->jam_selesai }}</td>
                </tr>
                <tr>
                    <th class="w-25 text-right">Status Dosen Pembimbing</th>
                    <th class="w-1">:</th>
                    <td class="w-74">
                        @if ($data->status1 == 0)
                            <span class="badge badge-warning">Menunggu</span>
                        @elseif ($data->status1 == 1)
                            <span class="badge badge-success">Menerima</span>
                        @elseif ($data->status1 == 2)
                            <span class="badge badge-danger">Menolak</span>
                        @else
                            {{ $data->status1 }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="w-25 text-right">Status Pembimbing Lapangan</th>
                    <th class="w-1">:</th>
                    <td class="w-74" id="status_instruktur_lapangan">
                        @if ($data->status2 == 0)
                            <span class="badge badge-warning">Menunggu</span>
                        @elseif ($data->status2 == 1)
                            <span class="badge badge-success">Menerima</span>
                        @elseif ($data->status2 == 2)
                            <span class="badge badge-danger">Menolak</span>
                        @else
                            {{ $data->status2 }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="w-25 text-right">Topik Bimbingan</th>
                    <th class="w-1">:</th>
                    <td class="w-74" style="word-wrap: break-word;">{{ $data->topik_bimbingan }}</td>
                </tr>
                <tr>
                    <th class="w-25 text-right">Foto</th>
                    <th class="w-1">:</th>
                    <td class="w-74">
                        @if ($data->foto)
                            <img src="{{ asset('storage/assets/logbimbingan/' . $data->foto) }}" alt="Foto Bimbingan"
                                style="max-width: 450px;">
                        @else
                            -
                        @endif

                    </td>
                </tr>
            </table>
            <form id="update-status-form">
                @csrf
                <input type="hidden" name="log_bimbingan_id" value="{{ $data->id }}">
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Status Dosen Pembimbing</label>
                    <div class="col-sm-9">
                        <select name="status2" class="form-control">
                            <option value="1" {{ $data->status2 == 1 ? 'selected' : '' }}>Menerima</option>
                            <option value="2" {{ $data->status2 == 2 ? 'selected' : '' }}>Menolak</option>
                        </select>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Nilai Dosen</label>
                    <div class="col-sm-9">
                        <input type="number" name="nilai_instruktur_lapangan" class="form-control"
                            value="{{ $data->nilai_instruktur_lapangan }}">
                    </div>
                </div>
            </form>
            <div id="error-message" class="alert alert-warning" style="display: none;"></div>

        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-warning">Keluar</button>
            <button type="submit" form="update-status-form" class="btn btn-primary">Simpan</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        unblockUI();
        var nilaiSebelumnya;
        // Atur aturan validasi pada form
        $('#update-status-form').validate({
            rules: {
                status2: {
                    required: true
                },
                nilai_instruktur_lapangan: {
                    required: true,
                    number: true
                }
            },
            messages: {
                status2: "Pilih salah satu status",
                nilai_instruktur_lapangan: {
                    required: "Masukkan nilai Instruktur Lapangan",
                    number: "Masukkan nilai dalam bentuk angka"
                }
            },
            submitHandler: function(form) {
                // Ambil data dari form
                var formData = {
                    'log_bimbingan_id': $('input[name=log_bimbingan_id]').val(),
                    'status2': $('select[name=status2]').val(),
                    'nilai_instruktur_lapangan': $('input[name=nilai_instruktur_lapangan]')
                        .val()
                };

                
                // Periksa apakah status dosen pembimbing adalah 'Menolak'
                if (formData.status2 == 2) {
                    // Jika status ditolak, atur nilai_instruktur_lapangan menjadi 0.00
                    formData.nilai_instruktur_lapangan = '0.00';
                    // $('input[name=nilai_instruktur_lapangan]').val('0');
                    $('input[name=nilai_instruktur_lapangan]').val('0').trigger('change');
                }

                // Validasi nilai_instruktur_lapangan jika status1 adalah 'Menolak'
                if (formData.status2 == 1 && parseFloat(formData.nilai_instruktur_lapangan) < 81) {
                    $('#error-message').text(
                        'Nilai pembimbing harus minimal 81 ketika status menerima.').show();
                    setTimeout(function() {
                        $('#error-message').fadeOut('slow');
                    }, 5000);
                    return false; // Menghentikan pengiriman form jika validasi gagal
                } else {
                    $('#error-message')
                        .hide(); // Sembunyikan pesan kesalahan jika validasi berhasil
                }
                if (formData.status2 == 1 && parseFloat(formData.nilai_instruktur_lapangan) > 100) {
                    $('#error-message').text(
                        'Nilai pembimbing harus maksimal 100 ketika status menerima.').show();
                    setTimeout(function() {
                        $('#error-message').fadeOut('slow');
                    }, 5000);
                    return false; // Menghentikan pengiriman form jika validasi gagal
                } else {
                    $('#error-message')
                        .hide(); // Sembunyikan pesan kesalahan jika validasi berhasil
                }
                var $modal = $('#modal-master');
                // Kirim permintaan AJAX ke server
                $.ajax({
                    url: '{{ route('update.logbimbingan.instruktur.modal', ['id' => $id]) }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Tanggapi respons dari server
                        if (response.success) {
                            dataMaster.draw(false);
                            $('#success-message').text('data berhasil diperbarui')
                                .show();
                            var statusBadge;

                            if (formData.status2 == 0) {
                                statusBadge =
                                    '<span class="badge badge-warning">Menunggu</span>';
                            } else if (formData.status2 == 1) {
                                statusBadge =
                                    '<span class="badge badge-success">Menerima</span>';
                            } else if (formData.status2 == 2) {
                                statusBadge =
                                    '<span class="badge badge-danger">Menolak</span>';
                            } else {
                                statusBadge = formData.status2;
                            }
                            // Update elemen HTML dengan ID "status-pembimbing-lapangan" dengan status baru
                            $('#status_instruktur_lapangan').html(statusBadge);
                            // Jeda untuk menampilkan pesan kemudian sembunyikan modal
                            setTimeout(function() {
                                $('#success-message').fadeOut('slow');
                            }, 5000);
                            setTimeout(function() {
                                $modal.modal('hide');
                            }, 2000);
                        } else {
                            // Jika pembaruan gagal, tampilkan pesan kesalahan
                            alert('Gagal memperbarui status dosen pembimbing!');
                            closeModal($modal, data);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Tanggapi kesalahan jika terjadi
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    });
</script>

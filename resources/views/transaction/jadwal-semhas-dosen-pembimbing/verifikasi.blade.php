<div id="modal-master" class="modal-dialog modal-xl" role="document" style="max-width: 100%">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body p-0">
            <div class="form-message text-center"></div>
            <div class="row">
                <div class="col-7">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th class="w-15 text-right">Nama Mahasiswa</th>
                                <th class="w-1">:</th>
                                <td class="w-84">{{ $data->magang->mahasiswa->nama_mahasiswa }}</td>
                            </tr>
                            <tr>
                                <th class="w-15 text-right">NIM Mahasiswa</th>
                                <th class="w-1">:</th>
                                <td class="w-84">{{ $data->magang->mahasiswa->nim }}</td>
                            </tr>
                            <tr>
                                <th class="w-15 text-right">Prodi Mahasiswa</th>
                                <th class="w-1">:</th>
                                <td class="w-84">{{ $data->magang->mahasiswa->prodi->prodi_name }}</td>
                            </tr>
                            <tr>
                                <th class="w-15 text-right">Magang ID</th>
                                <th class="w-1">:</th>
                                <td class="w-84">{{ $magang->magang_kode }}</td>
                            </tr>
                            <tr>
                                <th class="w-15 text-right">Nama Kegiatan</th>
                                <th class="w-1">:</th>
                                <td class="w-84">{{ $magang->mitra->kegiatan->kegiatan_nama }}</td>
                            </tr>
                            <tr>
                                <th class="w-15 text-right">Nama Mitra</th>
                                <th class="w-1">:</th>
                                <td class="w-84">
                                    <i class="far fa-building text-md text-primary"></i>
                                    {{ $magang->mitra->mitra_nama }}
                                </td>
                            </tr>
                            <tr>
                                <th class="w-15 text-right">Periode</th>
                                <th class="w-1">:</th>
                                <td class="w-84">{{ $magang->periode->periode_nama }}</td>
                            </tr>
                            <tr>
                                <th class="w-15 text-right">Skema</th>
                                <th class="w-1">:</th>
                                <td class="w-84">{{ $magang->magang_skema }}</td>
                            </tr>
                            <tr>
                                <th class="w-15 text-right">Judul</th>
                                <th class="w-1">:</th>
                                <td class="w-84">{{ $data->Judul }}</td>
                            </tr>
                            <tr>
                                <th class="w-15 text-right">Berkas Berita Acara</th>
                                <th class="w-1">:</th>
                                <td class="w-84 py-2">
                                    <table class="table table-sm text-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center w-5 p-1">No</th>
                                                <th>Nama Berkas</th>
                                                <th><em>Last Update</em></th>
                                                <th>Status</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($databeritaacaraall as $index => $dokumen)
                                                <tr>
                                                    <td class="text-center w-5 p-1">{{ $index + 1 }}
                                                    </td>
                                                    <td><a href="{{ asset('storage/assets/berita-acara/' . $dokumen->dokumen_berita_acara_file) }}"
                                                            target="_blank">Berita Acara</a></td>
                                                    <td>{{ \Carbon\Carbon::parse($dokumen->created_at)->format('d M Y H:i:s') }}
                                                    <td>
                                                        @if ($dokumen->dokumen_berita_status == 0)
                                                            <span class="badge badge-info">Menunggu</span>
                                                        @elseif($dokumen->dokumen_berita_status == 1)
                                                            <span class="badge badge-success">Diterima</span>
                                                        @elseif($dokumen->dokumen_berita_status == 2)
                                                            <span class="badge badge-danger">Ditolak</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $dokumen->dokumen_berita_acara_keterangan ?? '-' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">No
                                                        documents
                                                        found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <th class="w-15 text-right">Status Berita Acara</th>
                                <th class="w-1">:</th>
                                <td class="w-84">
                                    <div class="form-group required row mb-2">
                                        <div class="col-sm-10">
                                            <div class="icheck-success d-inline mr-3">
                                                <input type="radio" id="radioActive" name="dokumen_berita_status"
                                                    value="1">
                                                <label for="radioActive">Diterima </label>
                                            </div>
                                            <div class="icheck-danger d-inline mr-3">
                                                <input type="radio" id="radioFailed" name="dokumen_berita_status"
                                                    value="2">
                                                <label for="radioFailed">Ditolak</label>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- input --}}
                                    <div class="form-group required row mb-2">
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control form-control-sm"
                                                id="dokumen_berita_acara_keterangan"
                                                name="dokumen_berita_acara_keterangan" placeholder="Keterangan">
                                            <small class="text-muted">Catatan untuk Berita Acara</small><br />
                                            <small class="text-muted">** Mohon Bapak/Ibu Dosen Pembimbing agar melakukan
                                                <b>validasi(pengecekan)</b> terhadap Berita Acara yang disubmit oleh
                                                mahasiswa yang telah ditandatangani secara lengkap</small>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="border-0"></th>
                                <th class="border-0"></th>
                                <td colspan="3" class="border-0">
                                    {{-- @dd($databeritaacara->dokumen_berita_acara_id); --}}
                                    <button type="button" class="btn btn-primary" id="simpan"
                                        data-id="{{ $databeritaacara->dokumen_berita_acara_id }}">Simpan</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-5">
                    <embed
                        src="{{ asset('storage/assets/berita-acara/' . $databeritaacara->dokumen_berita_acara_file) }}"
                        type="application/pdf" width="100%" height="600px" />
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-warning">Keluar</button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        unblockUI();
        $("#simpan").click(function(e) {
            e.preventDefault(); // Prevent default form submission
            $('.form-message').html('');
            let blc = '#modal-master';
            blockUI(blc);

            let id = $(this).data('id'); // Ambil ID dari data attribute
            let status = $('input[name="dokumen_berita_status"]:checked').val();
            let keterangan = $('#dokumen_berita_acara_keterangan').val();

            // Kirim data via Ajax
            $.ajax({
                url: "{{ route('verifikasi.berita.acara') }}", // Pastikan $id tersedia di dalam view
                dataType: 'json',
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    dokumen_berita_status: status,
                    dokumen_berita_acara_keterangan: keterangan
                },
                success: function(data) {
                    setFormMessage('.form-message', data);
                    if (data.status) {
                        $('.form-message').html('<div class="alert alert-success">' + data
                            .message + '</div>');
                        dataMaster.draw(false);
                    } else {
                        $('.form-message').html('<div class="alert alert-danger">' + data
                            .error + '</div>');
                        dataMaster.draw(false);
                    }
                    closeModal($modal, data);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    // Tampilkan pesan error jika terjadi kesalahan Ajax
                    $('.form-message').html(
                        '<div class="alert alert-danger">Terjadi kesalahan saat memproses data. Silakan coba lagi.</div>'
                    );
                }
            });
            return false;
        });
    });
</script>

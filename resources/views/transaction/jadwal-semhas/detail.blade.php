<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        {{-- <div class="modal-body p-0">
            <table class="table table-sm mb-0">
                <tr>
                    <th class="w-25 text-right">Nama Mahasiswa</th>
                    <th class="w-1">:</th>
                    <td class="w-74">{{ $data->nama_mahasiswa }}</td>
                </tr>
                <tr>
                    <th class="w-25 text-right">Jenis</th>
                    <th class="w-1">:</th>
                    <td class="w-74">{{ $magang->mitra->kegiatan->kegiatan_nama }}</td>
                </tr>
                <tr>
                    <th class="w-25 text-right">Mitra Kegiatan</th>
                    <th class="w-1">:</th>
                    <td class="w-74">{{ $magang->mitra->mitra_nama }}</td>
                </tr>
                <tr>
                    <th class="w-25 text-right">Periode</th>
                    <th class="w-1">:</th>
                    <td class="w-74">{{ $magang->periode->periode_nama }}</td>
                </tr>
                <tr>
                    <th class="w-25 text-right">Dosen Pembimbing Lapangan</th>
                    <th class="w-1">:</th>
                    <td class="w-74">{{ $data->nama_instruktur }}</td>
                </tr>
                <tr>
                    <th class="w-25 text-right">Dosen Pembimbing</th>
                    <th class="w-1">:</th>
                    <td class="w-74">{{ $data->nama_dosen }}</td>
                </tr>
                <tr>
                    <th class="w-25 text-right">tanggal_daftar</th>
                    <th class="w-1">:</th>
                    <td class="w-74">{{ $data->tanggal_daftar }}</td>
                </tr>
                <tr>
                    <th class="w-25 text-right">Judul</th>
                    <th class="w-1">:</th>
                    <td class="w-74">{{ $data->Judul }}</td>
                </tr>
                <tr>
                    <th class="w-25 text-right">link_github</th>
                    <th class="w-1">:</th>
                    <td class="w-74">
                        @if ($data->link_github)
                            <a href="{{ $data->link_github }}" target="_blank">{{ $data->link_github }}</a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="w-25 text-right">link_laporan</th>
                    <th class="w-1">:</th>
                    <td class="w-74">
                        @if ($data->link_laporan)
                            <a href="{{ $data->link_laporan }}" target="_blank">{{ $data->link_laporan }}</a>
                        @else
                            -
                        @endif
                    </td>
                </tr>

            </table>
        </div> --}}

        {{-- perubahan pembagi kolom --}}
        <div class="modal-body p-0">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm mb-0 text-left">
                        <tr>
                            <th>Nama Mahasiswa</th>
                            <th>:</th>
                            <th style="font-weight: 400">{{ $data->nama_mahasiswa }}</th>
                        </tr>
                        <tr>
                            <th>Jenis</th>
                            <th>:</th>
                            <td>{{ $magang->mitra->kegiatan->kegiatan_nama }}</td>
                        </tr>
                        <tr>
                            <th>Mitra Kegiatan</th>
                            <th>:</th>
                            <td>{{ $magang->mitra->mitra_nama }}</td>
                        </tr>
                        <tr>
                            <th>Periode</th>
                            <th>:</th>
                            <td>{{ $magang->periode->periode_nama }}</td>
                        </tr>
                        <tr>
                            <th>Dosen Pembimbing Lapangan</th>
                            <th>:</th>
                            <th style="font-weight: 400">{{ $data->nama_instruktur }}</th>
                        </tr>
                        <tr>
                            <th>Dosen Pembimbing</th>
                            <th>:</th>
                            <th style="font-weight: 400">{{ $data->nama_dosen }}</th>
                        </tr>
                        <tr>
                            <th>Tanggal Daftar</th>
                            <th>:</th>
                            <td>{{ $data->tanggal_daftar }}</td>
                        </tr>
                        <tr>
                            <th>Judul</th>
                            <th>:</th>
                            <td>{{ $data->Judul }}</td>
                        </tr>
                        <tr>
                            <th>Link Github</th>
                            <th>:</th>
                            <td>
                                <div class="col-md-12 text-left">
                                    @if ($data->link_github)
                                        <a href="{{ $data->link_github }}" target="_blank">{{ $data->link_github }}</a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Link Laporan</th>
                            <th>:</th>
                            <td>
                                <div class="col-md-12 text-left">
                                    @if ($data->link_github)
                                        <a href="{{ $data->link_github }}"
                                            target="_blank">{{ $data->link_github }}</a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                @if ($datajadwal)
                    <div class="col-md-6">
                        {{-- tabel disembunyikan --}}
                        <table class="table table-sm mb-0 text-left" id="hiddenTable">
                            <!-- Isi tabel untuk bagian kanan (akan disembunyikan) -->
                            <tr>
                                <th>Dosen Pembahas</th>
                                <th>:</th>
                                <td>{{ $data->nama_dosen_pembahas }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Sidang</th>
                                <th>:</th>
                                <td>{{ $datajadwal->tanggal_sidang }}</td>
                            </tr>
                            <tr>
                                <th>Waktu Sidang</th>
                                <th>:</th>
                                <td>{{ $datajadwal->jam_sidang_mulai }} - {{ $datajadwal->jam_sidang_selesai }}</td>
                            </tr>
                            <tr>
                                <th>Jenis Sidang</th>
                                <th>:</th>
                                <td>{{ $datajadwal->jenis_sidang }}</td>
                            </tr>
                            <tr>
                                <th>Tempat Sidang</th>
                                <th>:</th>
                                <td>{{ $datajadwal->tempat }}</td>
                            </tr>
                        </table>
                    </div>
                @else
                @endif
            </div>
            <button type="button" class="btn btn-primary mb-3" id="showTableBtn"
                @if (!$datajadwal) disabled @endif>Tombol</button>
        </div>


        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-warning">Keluar</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        unblockUI();
    });
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Sembunyikan tabel bagian kanan saat halaman dimuat
        $("#hiddenTable").hide();

        // Tambahkan event listener untuk tombol
        $("#showTableBtn").click(function() {
            // Periksa apakah tabel bagian kanan sedang ditampilkan atau tidak
            if ($("#hiddenTable").is(":visible")) {
                // Sembunyikan tabel bagian kanan jika sedang ditampilkan
                $("#hiddenTable").hide();
            } else {
                // Tampilkan tabel bagian kanan jika sedang disembunyikan
                $("#hiddenTable").show();
            }
        });
    });
</script>
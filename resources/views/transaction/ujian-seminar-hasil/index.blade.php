@extends('layouts.template')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <section class="col-lg-12">
                <div class="card card-outline card-{{ $theme->card_outline }}">
                    <div class="card-header">
                        <h3 class="card-title mt-1">
                            <i class="fas fa-angle-double-right text-md text-{{ $theme->card_outline }} mr-1"></i>
                            {!! $page->title !!}
                        </h3>
                    </div>
                    @if (isset($message))
                        <div class="card-body p-0">
                            <div class="alert alert-danger" role="alert">
                                {{ $message }}
                            </div>
                        </div>
                    @else
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <th class="w-15 text-right">Waktu</th>
                                            <th class="w-1">:
                                            <td class="w-84">
                                                <i class="far fa-calendar-alt text-md text-primary"></i>
                                                {{ \Carbon\Carbon::parse($dataJadwalSeminar->tanggal_sidang)->translatedFormat('l, j F Y') }}
                                                &nbsp; <i class="far fa-clock text-md text-primary"></i>
                                                {{ $dataJadwalSeminar->jam_sidang_mulai }} -
                                                {{ $dataJadwalSeminar->jam_sidang_selesai }}
                                                WIB
                                            </td>
                                        </tr>
                                        @if ($dataJadwalSeminar->jenis_sidang == 'offline')
                                            <tr>
                                                <th class="w-15 text-right">Tempat</th>
                                                <th class="w-1">:</th>
                                                <td class="w-84"><i class="fas fa-door-closed text-md text-primary"></i>
                                                    {{ $dataJadwalSeminar->tempat }} &nbsp; <i
                                                        class="far fa-building text-md text-primary"></i>
                                                    {{ $dataJadwalSeminar->gedung }} </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <th class="w-15 text-right">Tempat</th>
                                                <th class="w-1">:</th>
                                                <td class="w-84"><a href="{{ $dataJadwalSeminar->tempat }}"
                                                        target="_blank" class="text-primary">
                                                        <i class="fas fa-external-link-alt"></i>
                                                        {{ $dataJadwalSeminar->tempat }}
                                                    </a></td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th class="w-15 text-right">Mahasiswa</th>
                                            <th class="w-1">:</th>
                                            <td class="w-84 py-0 pr-0">
                                                <table class="table table-striped table-sm text-sm mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>NIM</th>
                                                            <th>Nama Mahasiswa</th>
                                                            <th>Prodi</th>
                                                            <th>HP</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>{{ $data->magang->mahasiswa->nim }}</td>
                                                        <td>{{ $data->magang->mahasiswa->nama_mahasiswa }}</td>
                                                        <td>{{ $data->magang->mahasiswa->prodi->prodi_name }}</td>
                                                        <td>{{ $data->magang->mahasiswa->no_hp }}</td>
                                                        <td>
                                                            @if ($user->is_active == 1)
                                                                <span class="badge bg-success">Aktif</span>
                                                            @else
                                                                <span class="badge bg-danger">Tidak Aktif</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="w-15 text-right">Program Studi</th>
                                            <th class="w-1">:</th>
                                            <td class="w-84">{{ $data->magang->mahasiswa->prodi->prodi_name }}</td>
                                        </tr>
                                        <tr>
                                            <th class="w-15 text-right">Magang ID</th>
                                            <th class="w-1">:</th>
                                            <td class="w-84">{{ $data->magang->magang_kode }}</span></td>
                                        </tr>
                                        <tr>
                                            <th class="w-15 text-right">Nama Kegiatan</th>
                                            <th class="w-1">:</th>
                                            <td class="w-84">{{ $data->magang->mitra->kegiatan->kegiatan_nama }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="w-15 text-right">Nama Mitra</th>
                                            <th class="w-1">:</th>
                                            <td class="w-84">{{ $data->magang->mitra->mitra_nama }}</td>
                                        </tr>
                                        <tr>
                                            <th class="w-15 text-right">Periode</th>
                                            <th class="w-1">:</th>
                                            <td class="w-84">{{ $data->magang->periode->periode_nama }}</td>
                                        </tr>
                                        <tr>
                                            <th class="w-15 text-right">Durasi</th>
                                            <th class="w-1">:</th>
                                            <td class="w-84">{{ $data->magang->mitra->mitra_durasi }}</td>
                                        </tr>
                                        <tr>
                                            <th class="w-15 text-right">Skema</th>
                                            <th class="w-1">:</th>
                                            <td class="w-84">{{ $data->magang->magang_skema }}</td>
                                        </tr>
                                        <tr>
                                            <th class="w-15 text-right">Judul</th>
                                            <th class="w-1">:</th>
                                            <td class="w-84">{{ $data->Judul }}</td>
                                        </tr>
                                        {{-- <tr>
                                        <th class="w-15 text-right">Bidang</th>
                                        <th class="w-1">:</th>
                                        <td class="w-84"><span class="badge bg-info">Sistem Informasi</span><span
                                                class="badge bg-info">Tata kelola Teknologi Informasi</span></td>
                                    </tr> --}}
                                        {{-- <tr>
                                        <th class="w-15 text-right">Berkas Proposal</th>
                                        <th class="w-1">:</th>
                                        <td class="w-84 py-2">
                                            <table class="table table-sm text-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center w-5 p-1">No</th>
                                                        <th>Nama Berkas</th>
                                                        <th>Keterangan</th>
                                                        <th><em>Last Update</em></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-center w-5 p-1">1</td>
                                                        <td><a
                                                                href="#"></a>
                                                        </td>
                                                        <td>File Laporan Proposal</td>
                                                        <td>2023-12-10 09:45:22</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center w-5 p-1">2</td>
                                                        <td><a
                                                                href="#"></a>
                                                        </td>
                                                        <td>File Presentasi Proposal</td>
                                                        <td>2023-12-13 16:48:22</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr> --}}
                                        <tr>
                                            <th class="w-15 text-right">Link github/project</th>
                                            <th class="w-1">:</th>
                                            <td class="w-84">
                                                <a href="{{ asset($data->link_github) }}" target="_blank">
                                                    {{ $data->link_github }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="w-15 text-right">Repo Dokumen</th>
                                            <th class="w-1">:</th>
                                            <td class="w-84">
                                                <a href="{{ asset($data->link_laporan) }}" target="_blank">
                                                    {{ $data->link_laporan }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="w-15 text-right">Dosen Pembimbing</th>
                                            <th class="w-1">:</th>
                                            <td class="w-84">{{ $data->pembimbingDosen->dosen->dosen_name }}</td>
                                        </tr>
                                        <!-- Jika tanggal sidang telah berlalu atau hari ini adalah tanggal sidang dan waktu sekarang sudah setelah atau sama dengan jam sidang selesai -->
                                        <!-- Bagian HTML -->
                                        @if (!$data->Berita_acara == null)
                                            <tr>
                                                <th class="w-15 text-right">Berita Acara</th>
                                                <th class="w-1">:</th>
                                                <td class="w-84">
                                                    @if ($data->Berita_acara)
                                                        <a href="{{ asset('storage/assets/berita-acara/' . $data->Berita_acara) }}"
                                                            target="_blank">
                                                            Berita Acara Seminar Magang
                                                        </a>
                                                    @else
                                                        No file uploaded
                                                    @endif
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <th class="w-15 text-right">Berita Acara</th>
                                                <th class="w-1">:</th>
                                                <td class="w-84">
                                                    <form id="uploadForm" enctype="multipart/form-data" method="POST">
                                                        @csrf
                                                        <input type="file" name="berita_acara_file"
                                                            accept=".pdf,.doc,.docx" required>
                                                        <button type="submit">Upload</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endif
                                        @if (!$data->Berita_acara == null)
                                            {{-- @dd($datanilai); --}}
                                            {{-- @dd($data->semhas_daftar_id); --}}
                                            <tr>
                                                <th class="w-15 text-right">Nilai</th>
                                                <th class="w-1">:</th>
                                                <td class="w-84">
                                                    {{-- @dd($datanilai); --}}
                                                    @if (!$datanilai->isEmpty() && !$existingNilai == null)
                                                        <a href="#" data-block="body"
                                                            data-url="{{ $page->url }}/{{ $data->semhas_daftar_id }}/nilai-pembimbing"
                                                            class="ajax_modal btn btn-xs btn-warning tooltips text-secondary mr-2"
                                                            data-placement="left" data-original-title="Nilai Dosbing"><i
                                                                class="fas fa-chalkboard-teacher text-white"></i></a>
                                                    @endif
                                                    @if (!$datanilaiPembahas->isEmpty() && !$existingNilaiPembahas == null)
                                                        <a href="#" data-block="body"
                                                            data-url="{{ $page->url }}/{{ $data->semhas_daftar_id }}/nilai-pembahas"
                                                            class="ajax_modal btn btn-xs btn-info tooltips text-secondary mr-2"
                                                            data-placement="left" data-original-title="Nilai Dospem"><i
                                                                class="fas fa-user-tie text-white"></i></a>
                                                        <a href="#" data-block="body"
                                                            data-url="{{ $page->url }}/{{ $data->semhas_daftar_id }}/nilai-instruktur"
                                                            class="ajax_modal btn btn-xs tooltips text-secondary mr-2"
                                                            style="background-color: #FFD700;" data-placement="left"
                                                            data-original-title="Nilai Instruktur Lapangan"><i
                                                                class="fas fa-hard-hat text-white"></i></a>

                                                        <a href="#" data-block="body"
                                                            data-url="{{ $page->url }}/{{ $data->semhas_daftar_id }}/nilai-akhir"
                                                            class="ajax_modal btn btn-xs tooltips text-secondary"
                                                            style="background-color: #FF5733;" data-placement="left"
                                                            data-original-title="Nilai Akhir">
                                                            <i class="fas fa-medal text-white"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @else
                                        @endif
                                    </tbody>
                                </table>
                            </div> {{-- Form Anda bisa ditambahkan di sini --}}
                        </div>
                    @endif
            </section>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.tooltips').tooltip();
            $('#uploadForm').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: '{{ route('upload-berita-acara') }}',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log(response);
                        window.location.reload();
                        // Tambahkan logika untuk menampilkan pesan atau melakukan aksi lain
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert('Failed to upload file: ' + xhr.responseText);
                        // Tambahkan logika untuk menampilkan pesan atau melakukan aksi lain
                    }
                });
            });
        });
    </script>
@endsection

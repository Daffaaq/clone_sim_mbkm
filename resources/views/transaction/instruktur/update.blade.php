@php
    setlocale(LC_TIME, 'id_ID');
    \Carbon\Carbon::setLocale('id');
@endphp

@extends('layouts.template')

@section('content')
    <div class="container-fluid" id="container-daftar">
        <div class="row">
            <section class="col-lg-12">
                <div class="card card-outline card-{{ $theme->card_outline }}">
                    <div class="card-header">
                        <h3 class="card-title mt-1">
                            <i class="fas fa-angle-double-right text-md text-{{ $theme->card_outline }} mr-1"></i>
                            {!! $page->title !!}
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="form-message text-center"></div>
                        <table class="table table-sm">
                            <tbody>
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
                                    <th class="w-15 text-right">Durasi</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">
                                        <i class="far fa-clock text-md text-primary"></i>
                                        {{ $magang->mitra->mitra_durasi }}
                                        bulan
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Skema</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">{{ $magang->magang_skema }}</td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Batas Pendaftaran</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">
                                        <i class="far fa-calendar-alt text-md text-primary"></i>
                                        {{ \Carbon\Carbon::parse($magang->mitra->mitra_batas_pendaftaran)->format('d M Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Anggota</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">
                                        <table class="table table-sm text-sm table-bordered"
                                            style="table-layout:fixed;width:100%;" id="table-mhs">
                                            <thead>
                                                <tr>
                                                    <th style="width: 14%">No</th>
                                                    <th style="width: 22%">NIM</th>
                                                    <th style="width: 45%">Nama Mahasiswa</th>
                                                    <th style="width: 14%">Kelas</th>
                                                    <th style="width: 14%">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($anggota as $key => $a)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $a->mahasiswa->nim }}</td>
                                                        <td>{{ $a->mahasiswa->nama_mahasiswa }}@if ($a->magang_tipe == 0)
                                                                <span class="badge badge-pill badge-primary">Ketua</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $a->mahasiswa->kelas }}</td>
                                                        <td>
                                                            @if ($a->magang_tipe == 1)
                                                                @if ($a->is_accept == 0)
                                                                    <span class="badge badge badge-warning">Menunggu</span>
                                                                @elseif ($a->is_accept == 1)
                                                                    <span class="badge badge badge-success">Menerima</span>
                                                                @elseif ($a->is_accept == 2)
                                                                    <span class="badge badge badge-danger">Menolak</span>
                                                                @endif
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Instruktur Lapangan</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84 py-2">
                                        @if ($instruktur)
                                            <!-- Tampilkan informasi bahwa mahasiswa sudah memiliki instruktur lapangan -->
                                            <div class="alert alert-info">
                                                {{ $instruktur->instruktur->nama_instruktur }}
                                            </div>
                                        @else
                                            <form method="post" action="{{ route('create_instruktur') }}" role="form"
                                                class="form-horizontal" id="form-sb" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="magang_id" value="{{ $magang->magang_id }}">
                                                @foreach ($anggotas as $anggota)
                                                    <div class="form-check">
                                                        {{-- @if ($anggota->mahasiswa) --}}
                                                        {{-- @dd($anggota->mahasiswa->mahasiswa_id) --}}
                                                        <input class="form-check-input" type="checkbox"
                                                            name="mahasiswa_id[]"
                                                            value="{{ $anggota->mahasiswa->mahasiswa_id }}">
                                                        <label
                                                            class="form-check-label">{{ $anggota->mahasiswa->nama_mahasiswa }}</label>
                                                        {{-- @endif --}}
                                                    </div>
                                                @endforeach
                                                <!-- Tambahkan input untuk data instruktur -->
                                                <div class="form-group required">
                                                    <label for="nama_instruktur" class="control-label">Nama
                                                        Instruktur</label>
                                                    <input type="text" class="form-control" id="nama_instruktur"
                                                        name="nama_instruktur" required>
                                                </div>
                                                <div class="form-group required">
                                                    <label for="instruktur_email" class="control-label">Email
                                                        Instruktur</label>
                                                    <input type="email" class="form-control" id="instruktur_email"
                                                        name="instruktur_email" required>
                                                </div>
                                                <div class="form-group required">
                                                    <label for="instruktur_phone" class="control-label">Nomor Telepon
                                                        Instruktur</label>
                                                    <input type="text" class="form-control" id="instruktur_phone"
                                                        name="instruktur_phone" required>
                                                </div>
                                                <div class="form-group required">
                                                    <label for="password" class="control-label">Password</label>
                                                    <input type="password" class="form-control" id="password"
                                                        name="password" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@push('content-js')
    <script>
        $(document).ready(function() {
            function validateMahasiswaSelection() {
                var selectedMahasiswa = $('input[name="mahasiswa_id[]"]:checked').length;
                if (selectedMahasiswa === 0) {
                    alert('Mohon pilih setidaknya satu mahasiswa.');
                    return false; // Batalkan pengiriman formulir
                }
                return true; // Lanjutkan pengiriman formulir
            }
            // Submit form
            $('#form-sb').submit(function(event) {
                event.preventDefault(); // Prevent default form submission
                if (!validateMahasiswaSelection()) {
                    return; // Batalkan pengiriman formulir jika validasi gagal
                }
                // Send AJAX request to submit form
                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: $(this).serialize(),
                    success: function(response) {
                        // Reload the page after successful form submission
                        window.location.reload();
                    },
                    error: function(xhr, status, error) {
                        // Tangani kesalahan jika permintaan AJAX gagal
                        console.error(error);
                        handleResponse(response);
                    }
                });
            });
        });
    </script>
@endpush

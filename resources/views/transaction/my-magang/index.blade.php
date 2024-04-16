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
                    <div class="card-body p-0">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th class="w-50" colspan="2"><span
                                            class="badge {{ $pembimbingDosen && $pembimbingDosen->dosen ? 'badge-primary' : 'badge-danger' }}">Pembimbing
                                            Institusi</span></th>
                                    <th class="w-50" colspan="2"><span
                                            class="badge {{ $instrukturLapangan && $instrukturLapangan->instruktur ? 'badge-info' : 'badge-danger' }}">Instruktur
                                            Lapangan</span></th>
                                </tr>
                                <tr>
                                    <th class="w-5">Nama</th>
                                    <td class="w-45"> <b>:</b>
                                        {{ $pembimbingDosen && $pembimbingDosen->dosen ? $pembimbingDosen->dosen->dosen_name : 'Belum ada pembimbing dosen' }}
                                    </td>
                                    <th class="w-5">Nama</th>
                                    <td class="w-45"> <b>:</b>
                                        {{ $instrukturLapangan && $instrukturLapangan->instruktur ? $instrukturLapangan->instruktur->nama_instruktur : 'Belum ada instruktur lapangan' }}
                                    </td>
                                </tr>
                                <tr>
                                    @if (
                                        $pembimbingDosen &&
                                            $pembimbingDosen->dosen &&
                                            ($pembimbingDosen->dosen->dosen_nip || $pembimbingDosen->dosen->dosen_nidn))
                                        @if ($pembimbingDosen->dosen->dosen_nip && !$pembimbingDosen->dosen->dosen_nidn)
                                            <th class="">NIP</th>
                                            <td class=""> <b>:</b>
                                                {{ $pembimbingDosen->dosen->dosen_nip }}
                                            </td>
                                        @else
                                            <th class="">NIDN</th>
                                            <td class=""> <b>:</b>
                                                {{ $pembimbingDosen->dosen->dosen_nidn }}
                                            </td>
                                        @endif
                                    @else
                                        <th class="">NIP/NIDN</th>
                                        <td class=""> <b>:</b>
                                            Belum ada instruktur lapangan
                                        </td>
                                    @endif

                                    <th class="">Email</th>
                                    <td class=""> <b>:</b>
                                        {{ $instrukturLapangan && $instrukturLapangan->instruktur ? $instrukturLapangan->instruktur->instruktur_email : 'Belum ada instruktur lapangan' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="">Telp</th>
                                    <td class="">
                                        <b>:</b>&nbsp;
                                        @if ($pembimbingDosen && $pembimbingDosen->dosen)
                                            <a href="{{ 'https://wa.me/' . $pembimbingDosen->dosen->dosen_phone }}"
                                                target="_blank">{{ $pembimbingDosen->dosen->dosen_phone }}</a>
                                        @else
                                            Belum ada pembimbing dosen
                                        @endif
                                    </td>
                                    <th class="">Telp</th>
                                    <td class="">
                                        <b>:</b>&nbsp;
                                        @if ($instrukturLapangan && $instrukturLapangan->instruktur)
                                            <a href="{{ 'https://wa.me/' . $instrukturLapangan->instruktur->instruktur_phone }}"
                                                target="_blank">{{ $instrukturLapangan->instruktur->instruktur_phone }}</a>
                                        @else
                                            Belum ada instruktur lapangan
                                        @endif
                                    </td>
                                </tr>

                                @if (!$instrukturLapangan || (!$instrukturLapangan->instruktur && !$pembimbingDosen) || !$pembimbingDosen->dosen)
                                    <tr>
                                        <td class="text-center text-danger" colspan="4">
                                            Segera menghubungi admin / koordinator jurusan untuk pembagian Dosen Pembimbing
                                            Institusi dan terus silahkan menuju link <a
                                                href="{{ url('transaksi/instruktur') }}">Disini</a> untuk mengisi akun
                                            pembimbing lapangan
                                        </td>
                                    </tr>
                                @elseif (!$instrukturLapangan || !$instrukturLapangan->instruktur)
                                    <tr>
                                        <td class="text-center text-danger" colspan="4">
                                            Silahkan menuju link <a href="{{ url('transaksi/instruktur') }}">Disini</a>
                                            untuk mengisi akun pembimbing lapangan
                                        </td>
                                    </tr>
                                @elseif (!$pembimbingDosen || !$pembimbingDosen->dosen)
                                    <tr>
                                        <td class="text-center text-danger" colspan="4">
                                            Segera menghubungi admin / koordinator jurusan untuk pembagian Dosen Pembimbing
                                            Institusi
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="text-center text-danger" colspan="4">
                                            Segera menghubungi dosen Pembimbing institusi dan instruktur Lapangan untuk
                                            memudahkan proses magang/pkl, Selamat Menjalani PKL/ MAGANG
                                        </td>
                                    </tr>
                                @endif
                                <tr></tr>
                            </tbody>
                        </table>

                        {{-- Form Anda bisa ditambahkan di sini --}}
                    </div>
                    <div class="card-body p-1">
                        <div class="form-group row mb-2">
                            <label class="col-12 col-md-2 control-label col-form-label text-md-end">Pengusul</label>
                            <div class="col-12 col-md-10" id="add_member">
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
                                        <td>{{ $magang->mahasiswa->nim }}</td>
                                        <td>{{ $magang->mahasiswa->nama_mahasiswa }}</td>
                                        <td>{{ $magang->mahasiswa->prodi->prodi_name }}</td>
                                        <td>{{ $magang->mahasiswa->no_hp }}</td>
                                        <td>
                                            @if ($user->is_active == 1)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                                {{-- Form Anda bisa ditambahkan di sini --}}
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-12 col-md-2 control-label col-form-label text-md-end">Magang ID</label>
                            <div class="col-12 col-md-10 d-flex justify-content-left align-items-center">
                                {{ $magang->magang_kode }}
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-12 col-md-2 control-label col-form-label text-md-end">Nama Kegiatan</label>
                            <div class="col-12 col-md-10 d-flex justify-content-left align-items-center">
                                {{ $magang->mitra->kegiatan->kegiatan_nama }}
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-12 col-md-2 control-label col-form-label text-md-end">Nama Mitra</label>
                            <div class="col-12 col-md-10 d-flex justify-content-left align-items-center">
                                {{ $magang->mitra->mitra_nama }}
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-12 col-md-2 control-label col-form-label text-md-end">Periode </label>
                            <div class="col-12 col-md-10 d-flex justify-content-left align-items-center">
                                {{ $magang->periode->periode_nama }}
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-12 col-md-2 control-label col-form-label text-md-end">Durasi </label>
                            <div class="col-12 col-md-10 d-flex justify-content-left align-items-center">
                                {{ $magang->mitra->mitra_durasi }}
                                bulan
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-12 col-md-2 control-label col-form-label text-md-end">Skema </label>
                            <div class="col-12 col-md-10 d-flex justify-content-left align-items-center">
                                {{ $magang->magang_skema }}
                            </div>
                        </div>
                    </div>
            </section>
        </div>
    </div>
@endsection

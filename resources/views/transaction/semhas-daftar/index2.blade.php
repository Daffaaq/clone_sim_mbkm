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
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <th class="col-2">Tanggal dibuka</th>
                                        <th>:</th>
                                        <td class="col-10">
                                            {{ \Carbon\Carbon::parse($semhasData['tanggal_mulai_pendaftaran'])->isoFormat('DD MMMM YYYY') }}
                                            -
                                            {{ \Carbon\Carbon::parse($semhasData['tanggal_akhir_pendaftaran'])->isoFormat('DD MMMM YYYY') }}
                                            &nbsp;
                                            @if (now() >= \Carbon\Carbon::parse($semhasData['tanggal_mulai_pendaftaran']) &&
                                                    now() <= \Carbon\Carbon::parse($semhasData['tanggal_akhir_pendaftaran']))
                                                <span class="badge badge-success">Registrasi {{ $semhasData->judul_semhas }}
                                                    telah dibuka</span>
                                            @elseif(now() > \Carbon\Carbon::parse($semhasData['tanggal_akhir_pendaftaran']))
                                                <span class="badge badge-danger">Registrasi {{ $semhasData->judul_semhas }}
                                                    telah ditutup</span>
                                            @endif
                                    </tr>
                                    <tr>
                                        <th class="col-2">Syarat min. bimbingan</th>
                                        <th>:</th>
                                        <td class="col-10">
                                            {{ $semhasData->kuota_bimbingan }}× bimbingan yang sudah Disetujui oleh dosen
                                            Pembimbing Institusi dan Pembimbing Lapangan
                                    </tr>
                                    <tr>
                                        <th class="col-2">Deskripsi</th>
                                        <th>:</th>
                                        <td class="col-10">
                                            Pembukaan pendaftaran gelombang {{ $semhasData->gelombang }}
                                            {{ $semhasData->judul_semhas }} Jurusan {{ $jurusanName }}
                                    </tr>
                                    @if (now() >= \Carbon\Carbon::parse($semhasData['tanggal_mulai_pendaftaran']) &&
                                            now() <= \Carbon\Carbon::parse($semhasData['tanggal_akhir_pendaftaran']))
                                        <tr>
                                            <th class="col-2">Keterangan</th>
                                            <th>:</th>
                                            <td class="col-10 bg-danger-opacity">
                                                <div>{{ $message }}</div>
                                            </td>
                                        </tr>
                                    @elseif(now() > \Carbon\Carbon::parse($semhasData['tanggal_akhir_pendaftaran']))
                                        <tr>
                                            <th class="col-2">Keterangan</th>
                                            <th>:</th>
                                            <td class="col-10 bg-danger-opacity">
                                                <div>{{ $message }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th>:</th>
                                            <td class="col-10 bg-danger-opacity">
                                                <div>Mohon Maaf Pendaftaran ditutup mohon hubungi admin Jurusan
                                                    {{ $jurusanName }} atau koordinator
                                                    prodi {{ $prodi_name }} </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table> {{-- <div class="alert alert-danger" role="alert">
                                {{ $message }}
                            </div> --}}
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
@endsection

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
                                                @foreach ($anggotas as $key => $a)
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
                                @if ($magang->mitra->kegiatan->is_submit_proposal)

                                    @if (!$magang->proposal_exist)
                                        @if ($magang->ketua)
                                            <tr>
                                                <th class="w-15 text-right">Berkas Proposal</th>
                                                <th class="w-1">:</th>
                                                <td class="w-84 py-2">
                                                    @if (!$magang->can_upload_proposal)
                                                        <span class="badge badge-danger">Belum bisa upload dikarenakan ada
                                                            anggota yang belum menerima ajakan</span>
                                                    @else
                                                        <form method="post"
                                                            action="{{ route('dokumen.upload_proposal') }}" role="form"
                                                            class="form-horizontal" id="form-proposal"
                                                            enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="d-flex">
                                                                <div class="form-control-sm custom-file">
                                                                    <input type="hidden" value="{{ $magang->magang_id }}"
                                                                        name="magang_id" />
                                                                    <input type="file"
                                                                        class="form-control-sm custom-file-input"
                                                                        data-target="0" id="proposal" name="proposal"
                                                                        data-rule-filesize="1"
                                                                        data-rule-accept="application/pdf"
                                                                        accept="application/pdf" />
                                                                    <label
                                                                        class="form-control-sm custom-file-label file_label_0"
                                                                        for="proposal">Choose
                                                                        file</label>
                                                                </div>
                                                                <button type="submit"
                                                                    class="ml-2 btn btn-sm btn-primary text-white">Upload</button>
                                                            </div>
                                                            <small class="form-text text-danger">Pilih file proposal dengan
                                                                format
                                                                .pdf</small>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @else
                                        <tr>
                                            <th class="w-15 text-right">Berkas Proposal</th>
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
                                                        <tr>
                                                            <td class="text-center w-5 p-1">1</td>
                                                            <td>
                                                                <a
                                                                    href="{{ asset('assets/proposal/' . $magang->proposal->dokumen_magang_file) }}">{{ $magang->proposal->dokumen_magang_file }}</a>
                                                            </td>
                                                            <td>
                                                                {{ \Carbon\Carbon::parse($magang->proposal->created_at)->format('d M Y H:i') }}
                                                            </td>
                                                            <td>
                                                                @if ($magang->proposal->dokumen_magang_status == '1')
                                                                    <span class="badge badge-success">Disetujui</span>
                                                                @elseif ($magang->proposal->dokumen_magang_status == '0')
                                                                    <span class="badge badge-danger">Ditolak</span>
                                                                @else
                                                                    <span class="badge badge-warning">Menunggu</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ $magang->proposal->dokumen_magang_keterangan ?? '-' }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        @if (!$magang->surat_pengantar_exist)
                                            {{-- must ketua --}}
                                            @if ($magang->proposal->dokumen_magang_status == '1')
                                            @if ($magang->ketua)
                                                <tr>
                                                    <th class="w-15 text-right">Surat Pengantar</th>
                                                    <th class="w-1">:</th>
                                                    <td class="w-84 py-2">
                                                        <form method="post"
                                                            action="{{ route('generate.surat_pengantar') }}" role="form"
                                                            class="form-horizontal" id="form-generate-sp">
                                                            @csrf
                                                            <div class="form-group required mb-0">
                                                                <label
                                                                    class="control-label col-form-label text-left font-weight-normal">Alamat
                                                                    Mitra</label>
                                                                <div class="">
                                                                    <input type="hidden" name="magang_kode"
                                                                        value="{{ $magang->magang_kode }}">
                                                                    <textarea class="form-control form-control-sm" id="surat_pengantar_alamat_mitra" name="surat_pengantar_alamat_mitra"></textarea>
                                                                    <small class="form-text text-muted">
                                                                        Masukkan alamat lengkap mitra
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            <div class="form-group required mb-0">
                                                                <label
                                                                    class="control-label col-form-label text-left font-weight-normal">Awal
                                                                    Pelaksanaan</label>
                                                                <div class="">
                                                                    {{-- loop januari until desember --}}
                                                                    <select class="form-control form-control-sm"
                                                                        id="surat_pengantar_awal_pelaksanaan"
                                                                        name="surat_pengantar_awal_pelaksanaan">
                                                                        <option value="" disabled selected>Pilih
                                                                            bulan</option>
                                                                        @foreach ($bulans as $key => $bulan)
                                                                            <option value="{{ $key + 1 }}">
                                                                                {{ $bulan }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <small class="form-text text-muted">
                                                                        Masukkan bulan awal pelaksanaan magang
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            <div class="form-group mb-2">
                                                                <label
                                                                    class="control-label col-form-label text-left font-weight-normal">Akhir
                                                                    Pelaksanaan</label>
                                                                <div class="">
                                                                    {{-- loop januari until desember --}}
                                                                    <input type="hidden"
                                                                        id="surat_pengantar_akhir_pelaksanaan"
                                                                        name="surat_pengantar_akhir_pelaksanaan">
                                                                    <select class="form-control form-control-sm"
                                                                        id="surat_pengantar_akhir"
                                                                        name="surat_pengantar_akhir" readonly disabled>
                                                                        <option value="" disabled selected>Pilih
                                                                            bulan awal pelaksanaan dahulu</option>
                                                                        @foreach ($bulans as $key => $bulan)
                                                                            <option value="{{ $key + 1 }}">
                                                                                {{ $bulan }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <button id="generate-btn" type="button"
                                                                class="btn btn-sm btn-primary text-white">Generate</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <th class="w-15 text-right">Surat Pengantar</th>
                                                    <th class="w-1">:</th>
                                                    <td class="w-84 py-2">
                                                        -
                                                    </td>
                                                </tr>
                                            @endif
                                            @endif
                                        @else
                                            <tr>
                                                <th class="w-15 text-right">Surat Pengantar</th>
                                                <th class="w-1">:</th>
                                                <td class="w-84 py-2">
                                                    <a href="{{ url('surat_pengantar/' . $magang->magang_kode) }}"
                                                        target="_blank"
                                                        class="ml-2 btn btn-sm btn-success text-white text-decoration-none">
                                                        <i class="fas fa-download"></i>
                                                        Download</a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                @endif
                                @if (!$magang->surat_balasan_exist)
                                    @if ($magang->ketua)
                                        @if ($magang->mitra->kegiatan->is_submit_proposal)
                                            @if ($magang->proposal_exist && $magang->surat_pengantar_exist)
                                                <tr>
                                                    <th class="w-15 text-right">Surat Balasan</th>
                                                    <th class="w-1">:</th>
                                                    <td class="w-84 py-2">
                                                        <form method="post"
                                                            action="{{ route('dokumen.upload_surat_balasan') }}"
                                                            role="form" class="form-horizontal" id="form-sb"
                                                            enctype="multipart/form-data">
                                                            @csrf
                                                            <div
                                                                class="form-group required d-flex mb-2 align-items-center">
                                                                <label
                                                                    class="control-label col-form-label font-weight-normal text-left">Status</label>
                                                                <div class="">
                                                                    <div class="icheck-success d-inline mr-3">
                                                                        <input type="radio" id="radioActive"
                                                                            name="dokumen_magang_tipe" value="1">
                                                                        <label for="radioActive">Diterima </label>
                                                                    </div>
                                                                    <div class="icheck-danger d-inline mr-3">
                                                                        <input type="radio" id="radioFailed"
                                                                            name="dokumen_magang_tipe" value="0">
                                                                        <label for="radioFailed">Ditolak</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group mb-2 required">
                                                                <label
                                                                    class="col-12 col-md-2 control-label col-form-label font-weight-normal text-left">Surat
                                                                    Balasan</label>
                                                                <div class="col-12 col-md-12">
                                                                    <div class="d-flex">
                                                                        <div class="form-control-sm custom-file">
                                                                            <input type="hidden"
                                                                                value="{{ $magang->magang_id }}"
                                                                                name="magang_id" />
                                                                            <input type="file"
                                                                                class="form-control-sm custom-file-input"
                                                                                data-target="0" id="berita_doc_0"
                                                                                name="surat_balasan"
                                                                                data-rule-filesize="1"
                                                                                data-rule-accept="application/pdf"
                                                                                accept="application/pdf" />
                                                                            <label
                                                                                class="form-control-sm custom-file-label file_label_0"
                                                                                for="berita_doc_0">Choose
                                                                                file</label>
                                                                        </div>
                                                                        <button type="submit"
                                                                            class="ml-2 btn btn-sm btn-primary text-white">Upload</button>
                                                                    </div>
                                                                    <small class="form-text text-muted">Pilih surat balasan
                                                                        untuk
                                                                        Diupload</small>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endif
                                        @else
                                            <tr>
                                                <th class="w-15 text-right">Surat Balasan</th>
                                                <th class="w-1">:</th>
                                                <td class="w-84 py-2">
                                                    <form method="post"
                                                        action="{{ route('dokumen.upload_surat_balasan') }}"
                                                        role="form" class="form-horizontal" id="form-sb"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="form-group required d-flex mb-2 align-items-center">
                                                            <label
                                                                class="control-label col-form-label font-weight-normal text-left">Status</label>
                                                            <div class="">
                                                                <div class="icheck-success d-inline mr-3">
                                                                    <input type="radio" id="radioActive"
                                                                        name="dokumen_magang_tipe" value="1">
                                                                    <label for="radioActive">Diterima </label>
                                                                </div>
                                                                <div class="icheck-danger d-inline mr-3">
                                                                    <input type="radio" id="radioFailed"
                                                                        name="dokumen_magang_tipe" value="0">
                                                                    <label for="radioFailed">Ditolak</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group mb-2 required">
                                                            <label
                                                                class="col-12 col-md-2 control-label col-form-label font-weight-normal text-left">Surat
                                                                Balasan</label>
                                                            <div class="col-12 col-md-12">
                                                                <div class="d-flex">
                                                                    <div class="form-control-sm custom-file">
                                                                        <input type="hidden"
                                                                            value="{{ $magang->magang_id }}"
                                                                            name="magang_id" />
                                                                        <input type="file"
                                                                            class="form-control-sm custom-file-input"
                                                                            data-target="0" id="berita_doc_0"
                                                                            name="surat_balasan" data-rule-filesize="1"
                                                                            data-rule-accept="application/pdf"
                                                                            accept="application/pdf" />
                                                                        <label
                                                                            class="form-control-sm custom-file-label file_label_0"
                                                                            for="berita_doc_0">Choose
                                                                            file</label>
                                                                    </div>
                                                                    <button type="submit"
                                                                        class="ml-2 btn btn-sm btn-primary text-white">Upload</button>
                                                                </div>
                                                                <small class="form-text text-muted">Pilih surat balasan
                                                                    untuk
                                                                    Diupload</small>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                @else
                                    <tr>
                                        <th class="w-15 text-right">Surat Balasan</th>
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
                                                    <tr>
                                                        <td class="text-center w-5 p-1">1</td>
                                                        <td>
                                                            <a
                                                                href="{{ asset('assets/suratbalasan/' . $magang->surat_balasan->dokumen_magang_file) }}">{{ $magang->surat_balasan->dokumen_magang_file }}
                                                            </a>
                                                            @if ($magang->surat_balasan->dokumen_magang_tipe == 1)
                                                                <span class="badge badge-success">Diterima</span>
                                                            @else
                                                                <span class="badge badge-danger">Ditolak</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ \Carbon\Carbon::parse($magang->surat_balasan->created_at)->format('d M Y H:i') }}
                                                        </td>
                                                        <td>
                                                            @if ($magang->surat_balasan->dokumen_magang_status == '1')
                                                                <span class="badge badge-success">Disetujui</span>
                                                            @elseif ($magang->surat_balasan->dokumen_magang_status == '0')
                                                                <span class="badge badge-danger">Ditolak</span>
                                                            @else
                                                                <span class="badge badge-warning">Menunggu</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ $magang->surat_balasan->dokumen_magang_keterangan ?? '-' }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="modal fade" id="d">
        <div id="modal-confirm-generate" class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Konfirmasi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="mb-0 form-message text-center"></div>
                    <div class="alert alert-warning mb-0 rounded-0">
                        Apakah anda yakin akan generate surat pengantar dengan detail sebagai berikut?
                        <section class="landing">
                            <div class="container">
                                <dl class="row mb-0">

                                </dl>
                            </div>
                        </section>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Keluar</button>
                    <button type="button" class="btn btn-primary" id="btn-confirm">Ya, Yakin</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('content-js')
    <script>
        var stepper1Node = document.querySelector('#stepper1')
        var stepper1 = new Stepper(document.querySelector('#stepper1'))

        stepper1Node.addEventListener('show.bs-stepper', function(event) {
            console.warn('show.bs-stepper', event)
        })
        stepper1Node.addEventListener('shown.bs-stepper', function(event) {
            console.warn('shown.bs-stepper', event)
        })

        var stepper2 = new Stepper(document.querySelector('#stepper2'), {
            linear: false,
            animation: true
        })
        var stepper3 = new Stepper(document.querySelector('#stepper3'), {
            animation: true
        })
        var stepper4 = new Stepper(document.querySelector('#stepper4'))
    </script>
    <script>
        var loadFile = function(event) {
            $('input.custom-file-input').on('change', function() {
                // Get the file name
                var fileName = $(this).val().split('\\').pop();

                // Set the label text to the file name
                $(this).next('.custom-file-label').html(fileName);
            });

        };

        $(document).ready(function() {
            // unblockUI();
            // bsCustomFileInput.init();

            loadFile()
            // hide modal #modal-confirm-generate
            // $('#modal-confirm-generate').modal('hide')

            // #surat_pengantar_awal_pelaksanaan on change will count the duration with $magang->mitra->mitra_durasi
            // with mitra_durasi only number eg 6, then surat_pengantar_akhir_pelaksanaan will calculate the duration
            // with adding 6 month from surat_pengantar_awal_pelaksanaan
            //then if december, will count back to january, etc
            $('#surat_pengantar_awal_pelaksanaan').on('change', function() {
                const durasi = parseInt('{{ $magang->mitra->mitra_durasi }}')
                const awal = parseInt($(this).val())
                let akhir = awal + durasi
                if (akhir > 12) {
                    akhir = akhir - 12
                }
                $('#surat_pengantar_akhir').val(akhir - 1)
                $('#surat_pengantar_akhir_pelaksanaan').val(akhir - 1)
            })

            $("#form-proposal").submit(function() {
                $('.form-message').html('');
                let blc = '#container-daftar';
                blockUI(blc);
                $(this).ajaxSubmit({
                    dataType: 'json',
                    success: function(data) {
                        refreshToken(data);
                        unblockUI(blc);
                        setFormMessage('.form-message', data);
                        if (data.stat) {
                            //reload this page
                            window.location.reload();
                        }
                    }
                });
                return false;
            });

            $("#form-sb").submit(function() {
                $('.form-message').html('');
                let blc = '#container-daftar';
                blockUI(blc);
                $(this).ajaxSubmit({
                    dataType: 'json',
                    success: function(data) {
                        refreshToken(data);
                        unblockUI(blc);
                        setFormMessage('.form-message', data);
                        if (data.stat) {
                            //reload this page
                            window.location.reload();
                        }
                    }
                });
                return false;
            });

            $('#generate-btn').click(function() {
                const awal = $('#surat_pengantar_awal_pelaksanaan').val()
                const akhir = $('#surat_pengantar_akhir_pelaksanaan').val()
                const alamat = $('#surat_pengantar_alamat_mitra').val()
                if (awal == '' || akhir == '' || alamat == '') {
                    setFormMessage('.form-message', {
                        stat: false,
                        mc: false,
                        msg: 'Lengkapi form terlebih dahulu'
                    });
                    return
                }
                const info = {
                    'Alamat Mitra': alamat,
                    'Awal Pelaksanaan': $('#surat_pengantar_awal_pelaksanaan option:selected').text(),
                    'Akhir Pelaksanaan': $('#surat_pengantar_akhir option:selected').text()
                }
                $('#modal-confirm-generate .modal-title').html('Konfirmasi Generate Surat Pengantar')
                $('#modal-confirm-generate .modal-body .landing dl').html('')
                $.each(info, function(k, v) {
                    $('#modal-confirm-generate .modal-body .landing dl').append(`
                        <dt class="col-sm-5 text-right"><strong>${k}:</strong></dt>
                        <dd class="col-sm-7 mb-0">${v}</dd>
                    `)
                })
                // $('#modal-confirm-generate .modal-footer button[type="submit"]').html('Generate')
                $('#d').modal('show')
            })

            $("#btn-confirm").click(function() {
                $('.form-message').html('');
                let blc = '#container-daftar';
                blockUI(blc);
                const url = $('#form-generate-sp').attr('action')
                //get the form data
                const data = $('#form-generate-sp').serializeArray();
                const method = 'POST'

                console.log(url, data)
                // return
                $(this).ajaxSubmit({
                    url: url,
                    type: method,
                    data: data,
                    dataType: 'json',
                    success: function(data) {
                        refreshToken(data);
                        unblockUI(blc);
                        setFormMessage('.form-message', data);
                        if (data.stat) {
                            //reload this page
                            window.location.reload();
                        }
                    }
                });
                return false;
            });

            $("#form-kuota").submit(function() {
                $('.form-message').html('');
                let blc = '#modal-kuota';
                blockUI(blc);
                $(this).ajaxSubmit({
                    dataType: 'json',
                    success: function(data) {
                        refreshToken(data);
                        unblockUI(blc);
                        setFormMessage('.form-message', data);
                        if (data.stat) {
                            if (typeof dataDetail != 'undefined') dataDetail.draw();
                            if (typeof dataMaster != 'undefined') dataMaster.draw(false);
                            if (typeof tableFile != 'undefined') tableFile.draw();
                            if (typeof tableQuiz != 'undefined') tableQuiz.draw();
                            if (typeof tableAssignment != 'undefined') tableAssignment.draw();
                        }
                        closeModal($modal, data);
                    }
                });
                return false;
            });

            $("#btn-cari-mhs").click(function() {
                $('.form-message').html('');
                const nim = $('#search').val()

                $.ajax({
                    url: "{{ url('mahasiswa') }}" + "/" + nim + "/cari",
                    type: "GET",
                    success: function(response) {
                        // Handle the success response here
                        console.log(response);
                        if (response.stat) {
                            $('#btn-tambah-mhs').attr('disabled', false)
                            $('#mhs_id').val(response.data.mahasiswa_id)
                            $('#mhs_nim').val(response.data.nim)
                            $('#mhs_nama').val(response.data.nama_mahasiswa)
                            $('#mhs_kelas').val(response.data.kelas)
                        } else {
                            setFormMessage('.form-message', response);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle errors here
                        console.error(xhr.responseText);
                    }
                });
            })

            $('#btn-tambah-mhs').click(function() {
                var rowCount = $('#table-mhs tbody tr').length;

                if (rowCount > 2) return

                const mhs_id = $('#mhs_id').val()
                const nim = $('#mhs_nim').val()
                const nama = $('#mhs_nama').val()
                const kelas = $('#mhs_kelas').val()
                $('#table-mhs tbody').append(`
                    <tr data-id="${rowCount+1}">
                        <td>${rowCount + 1}</td>
                        <td>${nim}</td>
                        <td>${nama}</td>
                        <td>${kelas}</td>
                        <td><a class="cursor-pointer" id="remove-mhs" data-id="${rowCount +1}"><i class="fa fa-trash text-danger"></i></a></td>
                    </tr>
                `);
                $('#data-mhs').append(`
                    <input type="hidden" class="form-control form-control-sm" name="mahasiswa[]" value="${mhs_id}" data-id="${rowCount+1}" />
                `);
                $('#btn-tambah-mhs').attr('disabled', true)
                $('#mhs_nim').val('')
                $('#mhs_nama').val('')
                $('#mhs_kelas').val('')
                $('#search').val('')
            })

            $(document).on('click', '#remove-mhs', function() {
                const index = $(this).data('id')
                console.log(index)
                //remove index <tr></tr> in table #table-mhs
                $(`#table-mhs tr[data-id="${index}"]`).remove();
                //remove too in <input name="mahasiswa[]" with data-id=index
                $(`#data-mhs input[name="mahasiswa[]"][data-id="${index}"]`).remove();
            });
        });
    </script>
@endpush

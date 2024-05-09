@extends('layouts.template')

@section('content')
    <div class="container-fluid">
        <div class="row">
            @if (isset($success))
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
                                        <th class="col-2">Tanggal dibuka</th>
                                        <th>:</th>
                                        <td class="col-10">
                                            {{ \Carbon\Carbon::parse($semhasData['tanggal_mulai_pendaftaran'])->isoFormat('DD MMMM YYYY') }}
                                            -
                                            {{ \Carbon\Carbon::parse($semhasData['tanggal_akhir_pendaftaran'])->isoFormat('DD MMMM YYYY') }}
                                            &nbsp;
                                            @if (now() >= \Carbon\Carbon::parse($semhasData['tanggal_mulai_pendaftaran']) &&
                                                    now() <= \Carbon\Carbon::parse($semhasData['tanggal_akhir_pendaftaran']))
                                                <span class="badge badge-success">Registrasi
                                                    {{ $semhasData->judul_semhas }}
                                                    telah dibuka</span>
                                            @elseif(now() > \Carbon\Carbon::parse($semhasData['tanggal_akhir_pendaftaran']))
                                                <span class="badge badge-danger">Registrasi {{ $semhasData->judul_semhas }}
                                                    telah ditutup</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="col-2">Syarat min. bimbingan</th>
                                        <th>:</th>
                                        <td class="col-10">
                                            {{ $semhasData->kuota_bimbingan }}× bimbingan yang sudah Disetujui oleh dosen
                                            Pembimbing Institusi dan Pembimbing Lapangan
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="col-2">Deskripsi</th>
                                        <th>:</th>
                                        <td class="col-10">
                                            Pembukaan pendaftaran gelombang {{ $semhasData->gelombang }}
                                            {{ $semhasData->judul_semhas }} Jurusan {{ $jurusanName }}
                                        </td>
                                    </tr>
                                    @if (now() >= \Carbon\Carbon::parse($semhasData['tanggal_mulai_pendaftaran']) &&
                                            now() <= \Carbon\Carbon::parse($semhasData['tanggal_akhir_pendaftaran']))
                                        @if (!$dataSemhasDaftar == null)
                                            <tr>
                                                <th class="col-2">Keterangan</th>
                                                <th>:</th>
                                                <td class="col-10 bg-success-opacity">
                                                    <div>{{ $success }}</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <td class="col-10 bg-success-opacity">
                                                    <div>{{ $successDaftar1 }}</div>
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <th class="col-2">Keterangan</th>
                                                <th>:</th>
                                                <td class="col-10 bg-success-opacity">
                                                    <div>{{ $success }}</div>
                                                </td>
                                            </tr>
                                        @endif
                                    @elseif(now() > \Carbon\Carbon::parse($semhasData['tanggal_akhir_pendaftaran']))
                                        @if (!$dataSemhasDaftar == null)
                                            <tr>
                                                <th class="col-2">Keterangan</th>
                                                <th>:</th>
                                                <td class="col-10 bg-success-opacity">
                                                    <div>{{ $success }}</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <td class="col-10 bg-success-opacity">
                                                    <div>{{ $successDaftar1 }}</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th>:</th>
                                                <td class="col-10 bg-danger-opacity">
                                                    <div>Mohon Maaf Pendaftaran ditutup </div>
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <th class="col-2">Keterangan</th>
                                                <th>:</th>
                                                <td class="col-10 bg-success-opacity">
                                                    <div>{{ $success }}</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <td class="col-10 bg-danger-opacity">
                                                    <div>Mohon Maaf Pendaftaran ditutup mohon hubungi admin Jurusan
                                                        {{ $jurusanName }} atau koordinator
                                                        prodi {{ $prodi_name }} </div>

                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                </tbody>
                            </table>
                            {{-- Form Anda bisa ditambahkan di sini --}}
                        </div>
                    </div>
                </section>
                @if (now() >= \Carbon\Carbon::parse($semhasData['tanggal_mulai_pendaftaran']) &&
                        now() <= \Carbon\Carbon::parse($semhasData['tanggal_akhir_pendaftaran']))
                    @if (!$dataSemhasDaftar == null)
                        <section class="col-lg-12">
                            <div class="card card-outline card-{{ $theme->card_outline }}">
                                <div class="card-header">
                                    <h3 class="card-title mt-1">
                                        <i
                                            class="fas fa-angle-double-right text-md text-{{ $theme->card_outline }} mr-1"></i>
                                        {!! $page->title !!}
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <div class="form-group  row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Jenis Magang</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $magang->mitra->kegiatan->kegiatan_nama }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group  row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Mitra Kegiatan</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $magang->mitra->mitra_nama }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group  row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Dosen Pembimbing
                                            Institusi</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $nama_dosen }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group  row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Instruktur Lapangan</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $nama_instruktur }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group  row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Judul Seminar Hasil</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $dataSemhasDaftar->Judul }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group  row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Link github/project</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $dataSemhasDaftar->link_github }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group  row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Repo Dokumen</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $dataSemhasDaftar->link_laporan }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    @else
                        <section class="col-lg-12">
                            <div class="card card-outline card-{{ $theme->card_outline }}">
                                <div class="card-header">
                                    <h3 class="card-title mt-1">
                                        <i
                                            class="fas fa-angle-double-right text-md text-{{ $theme->card_outline }} mr-1"></i>
                                        {!! $page->title !!}
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <div class="form-group  row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Jenis Magang</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $magang->mitra->kegiatan->kegiatan_nama }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group  row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Mitra Kegiatan</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $magang->mitra->mitra_nama }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group  row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Dosen Pembimbing
                                            Institusi</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $nama_dosen }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group  row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Instruktur Lapangan</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $nama_instruktur }}" readonly>
                                        </div>
                                    </div>
                                    <form action="{{ route('daftar.semhas') }}" method="POST" id="form-daftar">
                                        @csrf
                                        @if ($dataSemhasDaftar1->isNotEmpty())
                                            <div class="form-group required row mb-2">
                                                <label class="col-sm-3 control-label col-form-label">Pilih Judul
                                                    Seminar</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control form-control-sm" id="Pilih-judul">
                                                        <option value="" selected disabled>Pilih Judul Seminar Hasil
                                                        </option>
                                                        <option value="existing">Pilih dari Judul yang Sudah Ada</option>
                                                        <option value="manual">Masukkan Manual</option>
                                                    </select>
                                                    <small id="judul" class="form-text text-muted">Pilih Judul Seminar
                                                        Hasil dari yang teman anda inputkan atau masukkan manual jika tidak
                                                        ada dalam daftar.</small>
                                                </div>
                                            </div>

                                            <div class="form-group required row mb-2" id="existingJudulInput"
                                                style="display: none;">
                                                <label class="col-sm-3 control-label col-form-label">Pilih Judul yang Sudah
                                                    Ada</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control form-control-sm" id="existingJudul"
                                                        name="existingJudul">
                                                        <option value="" selected disabled>Pilih Judul yang Sudah Ada
                                                        </option>
                                                        @foreach ($dataSemhasDaftar1 as $item)
                                                            <option value="{{ $item->Judul }}">{{ $item->Judul }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group required row mb-2" id="manualJudulInput"
                                                style="display: none;">
                                                <label class="col-sm-3 control-label col-form-label">Judul Seminar
                                                    Hasil</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="manualJudul" name="manualJudul">
                                                    <small id="manualJudulText" class="form-text text-muted">Masukkan
                                                        Judul Seminar Hasil</small>
                                                </div>
                                            </div>
                                        @else
                                            <div class="form-group required row mb-2">
                                                <label class="col-sm-3 control-label col-form-label">Judul Seminar
                                                    Hasil</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="Judul" name="Judul">
                                                    <small id="judul" class="form-text text-muted">Masukkan Judul
                                                        Magang.</small>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="form-group required row mb-2">
                                            <label class="col-sm-3 control-label col-form-label">Link
                                                github/project</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control form-control-sm"
                                                    id="link_github" name="link_github">
                                                <small id="judul" class="form-text text-muted">Buat link repository
                                                    untuk
                                                    aplikasi yang sudah dikembangkan ditempat magang. Bisa link repository
                                                    pada
                                                    Github </small>
                                            </div>
                                        </div>
                                        <div class="form-group required row mb-2">
                                            <label class="col-sm-3 control-label col-form-label">Repo Dokumen</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control form-control-sm"
                                                    id="link_laporan" name="link_laporan">
                                                <small id="judul" class="form-text text-muted">Buat link repository
                                                    untuk
                                                    dokumen Proposal, Log Bimbingan. Bisa link share folder pada Google
                                                    Drive</small>
                                            </div>
                                        </div>
                                        <input type="hidden" name="semhas" value="{{ $semhas_id }}">
                                        <input type="hidden" name="instrukturLapangan"
                                            value="{{ $instrukturLapangan }}">
                                        <input type="hidden" name="pembimbingdosen" value="{{ $pembimbingdosen }}">
                                        <input type="hidden" name="magang_id" value="{{ $magang_id }}">

                                        <div class="form-group row text-right mr-2 mb-3">
                                            <div class="col-sm-12">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                    {{-- Form Anda bisa ditambahkan di sini --}}
                                </div>
                            </div>
                        </section>
                    @endif
                    {{-- <section class="col-lg-12">
                        <div class="card card-outline card-{{ $theme->card_outline }}">
                            <div class="card-header">
                                <h3 class="card-title mt-1">
                                    <i class="fas fa-angle-double-right text-md text-{{ $theme->card_outline }} mr-1"></i>
                                    {!! $page->title !!}
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="form-group  row mb-2">
                                    <label class="col-sm-3 control-label col-form-label">Jenis Magang</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $magang->mitra->kegiatan->kegiatan_nama }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group  row mb-2">
                                    <label class="col-sm-3 control-label col-form-label">Mitra Kegiatan</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $magang->mitra->mitra_nama }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group  row mb-2">
                                    <label class="col-sm-3 control-label col-form-label">Dosen Pembimbing
                                        Institusi</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $nama_dosen }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group  row mb-2">
                                    <label class="col-sm-3 control-label col-form-label">Instruktur Lapangan</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $nama_instruktur }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group  row mb-2">
                                    <label class="col-sm-3 control-label col-form-label">Judul Seminar Hasil</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $dataSemhasDaftar->Judul }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group  row mb-2">
                                    <label class="col-sm-3 control-label col-form-label">Link github/project</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $dataSemhasDaftar->link_github }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group  row mb-2">
                                    <label class="col-sm-3 control-label col-form-label">Repo Dokumen</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $dataSemhasDaftar->link_laporan }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section> --}}
                @elseif(now() > \Carbon\Carbon::parse($semhasData['tanggal_akhir_pendaftaran']))
                    @if (!$dataSemhasDaftar == null)
                        <section class="col-lg-12">
                            <div class="card card-outline card-{{ $theme->card_outline }}">
                                <div class="card-header">
                                    <h3 class="card-title mt-1">
                                        <i
                                            class="fas fa-angle-double-right text-md text-{{ $theme->card_outline }} mr-1"></i>
                                        {!! $page->title !!}
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <div class="form-group  row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Jenis Magang</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $magang->mitra->kegiatan->kegiatan_nama }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group  row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Mitra Kegiatan</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $magang->mitra->mitra_nama }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group  row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Dosen Pembimbing
                                            Institusi</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $nama_dosen }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group  row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Instruktur Lapangan</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $nama_instruktur }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group  row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Judul Seminar Hasil</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $dataSemhasDaftar->Judul }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group  row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Link github/project</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $dataSemhasDaftar->link_github }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group  row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Repo Dokumen</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $dataSemhasDaftar->link_laporan }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    @else
                    @endif
                @endif
            @endif
        </div>
    </div>
@endsection
@push('content-js')
    <script>
        $(document).ready(function() {
            unblockUI();
            $('#Pilih-judul').change(function() {
                if ($(this).val() == 'manual') {
                    $('#manualJudulInput').show();
                    $('#existingJudulInput').hide();
                } else if ($(this).val() == 'existing') {
                    $('#existingJudulInput').show();
                    $('#manualJudulInput').hide();
                } else {
                    $('#existingJudulInput').hide();
                    $('#manualJudulInput').hide();
                }
            });
            $("#form-daftar").validate({
                rules: {
                    link_github: {
                        required: true,
                    },
                    link_laporan: {
                        required: true,
                    },
                    Judul: {
                        required: true,
                    },
                    manualJudul: { // Menambahkan aturan validasi untuk input manual
                        required: true,
                    }
                },
                submitHandler: function(form) {
                    $('.form-message').html('');
                    blockUI(form);
                    var formData = {};
                    if ($('#Pilih-judul').val() == 'manual') {
                        formData = {
                            'link_github': $('input[name="link_github"]').val(),
                            'link_laporan': $('input[name="link_laporan"]').val(),
                            'Judul': $('input[name="manualJudul"]').val(),
                            // Sisipkan sisa data yang diperlukan
                        };
                    } else if ($('#Pilih-judul').val() == 'existing') {
                        formData = {
                            'link_github': $('input[name="link_github"]').val(),
                            'link_laporan': $('input[name="link_laporan"]').val(),
                            'Judul': $('#existingJudul').val(),
                            // Sisipkan sisa data yang diperlukan
                        };
                    }
                    console.log(formData);
                    $.ajax({
                        type: 'POST',
                        url: $(form).attr('action'),
                        data: formData,
                        dataType: 'json',
                        success: function(data) {
                            console.log("Submit berhasil!");
                            unblockUI(form);
                            setFormMessage('.form-message', data);
                            if (data.success) {
                                window.location.href = '/transaksi/seminarhasil-daftar';
                            }
                        },
                        error: function(xhr, status, error) {
                            unblockUI(form);
                            console.error(xhr.responseText);
                        }
                    });
                },
                validClass: "valid-feedback",
                errorElement: "div",
                errorClass: 'invalid-feedback',
                errorPlacement: erp,
                highlight: hl,
                unhighlight: uhl,
                success: sc
            });
        });
    </script>
@endpush

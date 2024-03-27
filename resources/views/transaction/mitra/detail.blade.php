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
                        <div class="form-message text-center"></div>
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th class="w-15 text-right">Nama Kegiatan</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">{{ $mitra->kegiatan->kegiatan_nama }}</td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Nama Mitra</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">
                                        <i class="far fa-building text-md text-primary"></i>
                                        {{ $mitra->mitra_nama }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Alamat Mitra</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">
                                        <i
                                            class="fas fa-map-marker-alt
                                        text-md text-primary"></i>
                                        {{ $mitra->mitra_alamat }}, {{ $mitra->kota->nama_kab_kota }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Periode</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">{{ $mitra->periode->periode_nama }}</td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Deskripsi</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">{!! $mitra->mitra_deskripsi !!}</td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Status</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">
                                        @if ($mitra->status == 0)
                                            <span class="badge badge-info">Menunggu</span>
                                        @elseif ($mitra->status == 1)
                                            <span class="badge badge-success">Diterima</span>
                                        @else
                                            <span class="badge badge-danger">Ditolak</span>
                                        @endif
                                    </td>
                                </tr>
                                @if ($mitra->status == 2)
                                    <tr>
                                        <th class="w-15 text-right">Keterangan Ditolak</th>
                                        <th class="w-1">:</th>
                                        <td class="w-84">{{ $mitra->mitra_keterangan_ditolak }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th class="w-15 text-right">Durasi</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">
                                        <i class="far fa-clock text-md text-primary"></i>
                                        {{ $mitra->mitra_durasi }}
                                        bulan
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Skema</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">{{ $mitra->mitra_skema }}</td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Batas Pendaftaran</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">
                                        <i class="far fa-calendar-alt text-md text-primary"></i>
                                        {{ \Carbon\Carbon::parse($mitra->mitra_batas_pendaftaran)->format('d M Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Flyer</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">
                                        @if ($mitra->mitra_flyer)
                                            <a href="{{ url('assets/flyer/' . $mitra->mitra_flyer) }}" target="_blank"
                                                class="">[Lihat Flyer]</a>
                                        @else
                                            <span class="badge badge-danger">Tidak ada flyer</span>
                                        @endif
                                    </td>
                                </tr>
                                @if ($mitra->status == 0)
                                    <form method="post" id="form-update-status"
                                        action="{{ route('mitra.update.status', $data->mitra_id) }}" role="form"
                                        class="form-horizontal">
                                        @csrf
                                        @method('put')
                                        <tr>
                                            <th class="w-15 text-right">Status</th>
                                            <th class="w-1">:</th>
                                            <td class="w-84">
                                                <div class="form-group required row mb-2">
                                                    <div class="col-sm-10">
                                                        <div class="icheck-success d-inline mr-3">
                                                            <input type="radio" id="radioActive" name="status"
                                                                value="1">
                                                            <label for="radioActive">Diterima </label>
                                                        </div>
                                                        <div class="icheck-danger d-inline mr-3">
                                                            <input type="radio" id="radioFailed" name="status"
                                                                value="2">
                                                            <label for="radioFailed">Ditolak</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group required row mb-2">
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="mitra_keterangan_ditolak" name="mitra_keterangan_ditolak"
                                                            placeholder="Keterangan">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            {{-- simpan buttn --}}
                                            <th class="border-0"></th>
                                            <th class="border-0"></th>
                                            <td colspan="3" class="border-0">
                                                <button type="submit" class="btn btn-primary"
                                                    id="simpan">Simpan</button>
                                            </td>

                                        </tr>
                                    </form>
                                @endif
                                @if ($mitra->status == 1)
                                    <tr>
                                        <th class="w-15 text-right">Kuota</th>
                                        <th class="w-1">:</th>
                                        <td class="w-84">
                                            @foreach ($prodis as $prodi)
                                                Set kuota untuk prodi {{ $prodi->prodi_name }}
                                                <form method="post" action="{{ $url }}" role="form"
                                                    class="form-horizontal" id="form-kuota-{{ $prodi->prodi_id }}">
                                                    @csrf
                                                    {!! method_field($action) !!}
                                                    <div class="d-flex justify-content-start align-items-center">
                                                        <input type="number" class="w-50 form-control form-control-sm"
                                                            id="kuota" name="kuota"
                                                            value="{{ isset($prodi->kuota->kuota) ? $prodi->kuota->kuota : '' }}" />
                                                        <input type="hidden" name="prodi_id"
                                                            value="{{ $prodi->prodi_id }}" />
                                                        <button type="submit"
                                                            class="btn btn-primary btn-sm">Simpan</button>
                                                    </div>
                                                </form>
                                            @endforeach
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
@endsection

@push('content-js')
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
            $('#btn-tambah-mhs').attr('disabled', true)

            $('#mitra_deskripsi').summernote("disable");

            loadFile()

            $('.select2_combobox').select2();

            $("#form-update-status").submit(function(e) {
                $('.form-message').html('');
                let blc = '#modal-confirm';
                blockUI(blc);
                $(this).ajaxSubmit({
                    dataType: 'json',
                    success: function(data) {
                        refreshToken(data);
                        unblockUI(blc);
                        setFormMessage('.form-message', data);
                        // if (data.stat) {
                        // }
                        window.location.reload();
                        // closeModal($modal, data);
                    }
                });
                return false;
            });

            @foreach ($prodis as $prodi)
                $("#form-kuota-{{ $prodi->prodi_id }}").submit(function(e) {
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
                                if (typeof tableAssignment != 'undefined') tableAssignment
                                    .draw();
                            }
                            // closeModal($modal, data);
                        }
                    });
                    return false;
                });
            @endforeach
        });
    </script>
@endpush

@extends('layouts.template')

@section('content')
    <div class="container-fluid">
        @if($quota)
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box card-outline card-{{ $theme->card_outline }}">
                    <span class="info-box-icon bg-success"><i class="fas fa-user-graduate"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">Kuota Pembimbing Utama</span>
                        <span class="info-box-number mt-2"><span class="text-lg">{{ $quota->jumlah_proposal }}</span> <span class="font-weight-normal"> topik &nbsp; / &nbsp;</span> <span class="text-lg">{{ $quota->quota }}</span> <span class="font-weight-normal"> (maks. topik)</span></span>
                    </div>
                </div>
            </div>

            @foreach($quota_prodi as $q)
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box card-outline card-{{ $theme->card_outline }} p-0">
                    <div class="info-box-content p-0">
                        <table class="table table-striped table-sm table-dashboard-info">
                            <thead><tr><th colspan="4" class="text-center"><span class="font-weight-normal"> kuota :</span> {{ $q->prodi_code }}</th></tr></thead>
                            <tbody><tr><td class="col-7">Kuota Minimum</td><td>:</td><td class="col-4"><b>{{ $q->quota_min }}</b>&nbsp; topik</td><td class="col-1"></td></tr></tbody>
                            <tbody><tr><td>Jumlah Bimbingan</td><td>:</td><td><b>{{ $q->jumlah_proposal }}</b>&nbsp; topik</td><td>
                                    @if($q->is_ok_topik)
                                        <span class="badge badge-success">OK</span>
                                    @else
                                        <span class="badge badge-danger blink">Warning</span>
                                    @endif
                                </td></tr></tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-{{ $theme->card_outline }}">
                    <div class="card-header">
                        <h3 class="card-title mt-1">
                            <i class="fas fa-angle-double-right text-md text-{{ $theme->card_outline }} mr-1"></i>
                            Daftar <span class="badge badge-success">Berita Terkini</span>
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-full-width" id="table_berita">
                                <thead>
                                <tr><th>No</th>
                                    <th>Judul</th>
                                    <th>Tanggal</th>
                                    <th>Oleh</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-{{ $theme->card_outline }}">
                    <div class="card-header">
                        <h3 class="card-title mt-1">
                            <i class="fas fa-angle-double-right text-md text-{{ $theme->card_outline }} mr-1"></i>
                            Daftar Mahasiswa Mengajukan Proposal Tugas Akhir
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="w-2">No</th>
                                <th class="w-8">Prodi</th>
                                <th class="w-25">Mahasiswa</th>
                                <th class="w-58">Judul</th>
                                <th class="w-7">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(!$proposal->isEmpty())
                            @foreach($proposal as $i => $p)
                                @php
                                    $mhs = explode(',', $p->mahasiswa);
                                @endphp

                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td class="pl-1">{{ $p->prodi_code }}</td>
                                    <td class="pl-1"><span class="badge badge-secondary">{!! implode('</span><span class="badge badge-secondary">', $mhs) !!}</span></td>
                                    <td class="pl-1">{{ $p->judul }}</td>
                                    <td class="pl-1">
                                        @switch($p->is_approval)
                                            @case('1')
                                                <span class="badge badge-success">Diterima</span>
                                            @break
                                            @case('0')
                                                <span class="badge badge-danger">Ditolak</span>
                                            @break
                                            @default
                                                -
                                            @break
                                        @endswitch
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="5" class="text-center">Tidak ada data proposal yang diajukan.</td></tr>
                        @endif
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('content-js')
<script src="{{ asset('assets/plugins/chart.js/Chart.min.js') }}"></script>
<script>
    var dataBerita;
    $(document).ready(function(){
        dataBerita = $('#table_berita').DataTable({
            "bServerSide": true,
            "bAutoWidth": false,
            "bFilter": false,
            "bLengthChange": false,
            "bPageLength": 5,
            "lengthMenu": [5],
            "ajax": {
                "url": "{{ url('berita') }}",
                "dataType": "json",
                "type": "POST",
            },
            "aoColumns": [{
                "mData": "no",
                "sClass": "text-center",
                "sWidth": "5%",
                "bSortable": false,
                "bSearchable": false
            },
                {
                    "mData": "berita_judul",
                    "sWidth": "65%",
                    "bSortable": true,
                    "bSearchable": true,
                    "mRender": function(data, type, row, meta) {
                        return '<a href="#" data-block="body" data-url="{{ url('berita') }}/'+row.berita_uid+'" class="ajax_modal" data-toggle="tooltip" data-placement="top" title="Lihat Detail Berita">'+data+'</a>';
                    }
                },
                {
                    "mData": "tanggal",
                    "sClass": "",
                    "sWidth": "15%",
                    "bSortable": true,
                    "bSearchable": true,
                },
                {
                    "mData": "created_by",
                    "sClass": "",
                    "sWidth": "15%",
                    "bSortable": true,
                    "bSearchable": false
                }
            ],
            "fnDrawCallback": function ( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $( 'a', this.fnGetNodes() ).tooltip();
            }
        });
    });
</script>
@endpush

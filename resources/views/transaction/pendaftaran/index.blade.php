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
                        {{-- <div class="card-tools">
                            @if ($allowAccess->create)
                                <button type="button" data-block="body"
                                    class="btn btn-sm btn-{{ $theme->button }} mt-1 ajax_modal"
                                    data-url="{{ $page->url }}/create"><i class="fas fa-plus"></i> Tambah</button>
                            @endif
                        </div> --}}
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-full-width" id="table_master">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Magang</th>
                                        <th>Nama Mahasiswa</th>
                                        @if (auth()->user()->group_id == 1)
                                            <th>Prodi</th>
                                        @endif
                                        <th>Nama Mitra</th>
                                        <th>Jenis Kegiatan</th>
                                        <th>Durasi</th>
                                        <th>Tipe Pendaftar</th>
                                        @if (auth()->user()->group_id != 4)
                                            <th>Persetujuan Anggota </th>
                                        @endif
                                        <th>Status</th>
                                        @if (auth()->user()->group_id != 4)
                                            <th>#</th>
                                        @endif
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@push('content-js')
    <script>
        $(document).ready(function() {

            $('.filter_combobox').select2();

            var v = 0;
            dataMaster = $('#table_master').DataTable({
                "bServerSide": true,
                "bAutoWidth": false,
                "ajax": {
                    "url": "{{ $page->url }}/list",
                    "dataType": "json",
                    "type": "POST"
                },
                "aoColumns": [{
                        "mData": "no",
                        "sClass": "text-center",
                        "sWidth": "5%",
                        "bSortable": false,
                        "bSearchable": false
                    },
                    {
                        "mData": "magang_kode",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "mahasiswa.nama_mahasiswa",
                        "sClass": "",
                        "sWidth": "5%",
                        "bSortable": true,
                        "bSearchable": true,
                    },
                    @if (auth()->user()->group_id == 1)
                        {
                            "mData": "prodi.prodi_name",
                            "sClass": "",
                            "sWidth": "10%",
                            "bSortable": true,
                            "bSearchable": true
                        },
                    @endif {
                        "mData": "mitra.mitra_nama",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "mitra.kegiatan.kegiatan_nama",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "mitra.mitra_durasi",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true,
                        "mRender": function(data, type, row, meta) {
                            return data + ' Bulan';
                        }
                    },
                    {
                        "mData": "magang_tipe",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true,
                        "mRender": function(data, type, row, meta) {
                            if (data == 2) {
                                return 'Individu';
                            } else {
                                return data == 0 ? 'Kelompok (Ketua)' : 'Kelompok (Anggota)'
                            }
                        }
                    },
                    @if (auth()->user()->group_id != 4)
                        {
                            "mData": "is_accept",
                            "sClass": "",
                            "sWidth": "10%",
                            "bSortable": true,
                            "bSearchable": true,
                            "mRender": function(data, type, row, meta) {
                                if (row.magang_tipe == "2") {
                                    return '';
                                } else {
                                    switch (data) {
                                        case 0:
                                            return '<span class="badge badge-warning">Menunggu</span>';
                                            break;
                                        case 1:
                                            return '<span class="badge badge-success">Menerima</span>';
                                            break;
                                        case 2:
                                            return '<span class="badge badge-danger">Menolak</span>';
                                            break;
                                        default:
                                            return '';
                                            break;
                                    }
                                }
                            }
                        },
                    @endif {
                        "mData": "status",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true,
                        "mRender": function(data, type, row, meta) {
                            if (row.magang_tipe == 1 && row.is_accept == 2) {
                                return '<span class="badge badge-danger">Menolak Undangan</span>';
                            } else {
                                switch (data) {
                                    case 2:
                                        return '<span class="badge badge-danger">Ditolak</span>';
                                        break;
                                    case 1:
                                        return '<span class="badge badge-success">Diterima</span>';
                                        break;
                                    case 0:
                                        return '<span class="badge badge-warning">Menunggu</span>';
                                        break;
                                }
                            }
                        }
                    },
                    @if (auth()->user()->group_id != 4)
                        {
                            "mData": "magang_id",
                            "sClass": "pr-2",
                            "sWidth": "8%",
                            "bSortable": false,
                            "bSearchable": false,
                            "mRender": function(data, type, row, meta) {
                                var buttons = '';
                                @if ($allowAccess->update)
                                    if (row.status == 0) {
                                        if (row.magang_tipe == 0 || row.magang_tipe == 1 && row
                                            .is_accept == 1 || row.magang_tipe == 2) {
                                            buttons +=
                                                // `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/confirm_approve" class="ajax_modal btn btn-xs btn-success tooltips text-white" data-placement="left" data-original-title="Approve" ><i class="fa fa-check"></i></a> ` +
                                                // `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/confirm_approve" class="ajax_modal btn btn-xs btn-success tooltips text-white" data-placement="left" data-original-title="Approve" ><i class="fa fa-check"></i></a> ` +
                                                `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/confirm" class="ajax_modal btn btn-xs btn-primary tooltips text-white" data-placement="left" data-original-title="Acc/Reject" ><i class="fa fa-vote-yea"></i></a> `;
                                        }
                                    }
                                @endif
                                return buttons;
                            }
                        },
                    @endif
                ],
                "fnDrawCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    $('a', this.fnGetNodes()).tooltip();
                }
            });

            $('.dataTables_filter input').unbind().bind('keyup', function(e) {
                if (e.keyCode == 13) {
                    dataMaster.search($(this).val()).draw();
                }
            });
        });
    </script>
@endpush

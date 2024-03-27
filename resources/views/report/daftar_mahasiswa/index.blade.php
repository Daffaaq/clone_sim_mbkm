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
                        <div id="filter" class="form-horizontal filter-date p-2 border-bottom">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm row text-sm mb-0">
                                        <label class="col-md-1 col-form-label">Filter</label>
                                        <div class="col-md-4">
                                            <select
                                                class="form-control form-control-sm w-100 filter_combobox filter_status">
                                                <option value="">- Semua -</option>
                                                @foreach ($statuses as $stat)
                                                    <option value="{{ $stat->id }}">{{ $stat->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">Status Mahasiswa</small>
                                        </div>
                                        @if (Auth::user()->group_id == 1)
                                            <div class="col-md-4">
                                                <select
                                                    class="form-control form-control-sm w-100 filter_combobox filter_prodi">
                                                    <option value="">- Semua -</option>
                                                    @foreach ($prodis as $prodi)
                                                        <option value="{{ $prodi->prodi_id }}">{{ $prodi->prodi_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="form-text text-muted">Prodi</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    {{-- button export excel with icon fontawesome --}}
                                    <button type="button" class="btn btn-primary float-right" id="export">
                                        <i class="fa fa-download"></i> Download
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-full-width" id="table_master">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIM</th>
                                        <th>Nama Mahasiswa</th>
                                        <th>No Telp</th>
                                        <th>Kelas</th>
                                        <th>Prodi</th>
                                        <th>Nama Mitra</th>
                                        <th>Posisi/Skema</th>
                                        <th>Status</th>
                                        <th>Jenis Kegiatan</th>
                                        <th>Tanggal Awal Pelaksanaan</th>
                                        <th>Tanggal Akhir Pelaksanaan</th>
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
                    "type": "POST",
                    "data": function(d) {
                        d.status = $('.filter_status').val();
                        d.prodi_id = $('.filter_prodi').val();
                    },
                },
                "aoColumns": [{
                        "mData": "no",
                        "sClass": "text-center",
                        "sWidth": "5%",
                        "bSortable": false,
                        "bSearchable": false
                    },
                    {
                        "mData": "nim",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "nama_mahasiswa",
                        "sClass": "",
                        "sWidth": "5%",
                        "bSortable": true,
                        "bSearchable": true,
                    },
                    {
                        "mData": "no_hp",
                        "sClass": "",
                        "sWidth": "5%",
                        "bSortable": true,
                        "bSearchable": true,
                    },
                    {
                        "mData": "kelas",
                        "sClass": "",
                        "sWidth": "5%",
                        "bSortable": true,
                        "bSearchable": true,
                    },
                    {
                        "mData": "prodi.prodi_name",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "magang.mitra.mitra_nama",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true,
                        "mRender": function(data, type, row, meta) {
                            if (!data) {
                                return "-";
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        "mData": "magang.magang_skema",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true,
                        "mRender": function(data, type, row, meta) {
                            if (!data) {
                                return "-";
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        "mData": "status_magang",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true,
                        "mRender": function(data, type, row, meta) {

                            switch (data) {
                                case "1":
                                    return '<span class="badge badge-success">Diterima</span>';
                                    break;
                                case "2":
                                    return '<span class="badge badge-primary">Terdaftar</span>';
                                    break;
                                case "3":
                                    return '<span class="badge badge-danger">Belum Terdaftar</span>';
                                    break;
                            }

                        }
                    },
                    {
                        "mData": "magang.mitra.kegiatan.kegiatan_nama",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true,
                        "mRender": function(data, type, row, meta) {
                            if (!data) {
                                return "-";
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        "mData": "magang.mitra.mitra_durasi",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true,
                        "mRender": function(data, type, row, meta) {
                            return "-"
                        }
                    },
                    {
                        "mData": "magang.magang_tipe",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true,
                        "mRender": function(data, type, row, meta) {
                            return "-"
                        }
                    },
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

            $('.filter_prodi, .filter_status').change(function() {
                dataMaster.draw();
            });

            $('#export').click(function() {
                const status = $('.filter_status').val();
                const prodi_id = $('.filter_prodi').val() ?? ''
                window.location.href = "{{ url('laporan/daftar-mahasiswa/export?status=') }}" + status +
                    "&prodi_id=" + prodi_id;
            })
        });
    </script>
@endpush

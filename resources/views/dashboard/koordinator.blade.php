@extends('layouts.template')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Jumlah Pendaftar</span>
                        <span class="info-box-number">
                            {{ $count_pendaftar }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-user-graduate"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Jumlah Mahasiswa</span>
                        <span class="info-box-number">
                            {{ $count_mahasiswa }}
                        </span>
                    </div>

                </div>

            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-building text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Jumlah Mitra</span>
                        <span class="info-box-number">
                            {{ $count_mitra }}
                        </span>
                    </div>

                </div>

            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Jumlah Mahasiswa Diterima</span>
                        <span class="info-box-number">
                            {{ $count_diterima }}
                        </span>
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
                            Daftar <span class="badge badge-success">Berita Terkini</span>
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-full-width" id="table_berita">
                                <thead>
                                    <tr>
                                        <th>No</th>
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

    </div>
@endsection

@push('content-js')
    <script src="{{ asset('assets/plugins/summernote/summernote.min.js') }}"></script>
    <script>
        var dataBerita;
        $(document).ready(function() {

            $('.filter_combobox').select2();

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
                            return '<a href="#" data-block="body" data-url="{{ url('berita') }}/' +
                                row.berita_uid +
                                '" class="ajax_modal" data-toggle="tooltip" data-placement="top" title="Lihat Detail Berita">' +
                                data + '</a>';
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

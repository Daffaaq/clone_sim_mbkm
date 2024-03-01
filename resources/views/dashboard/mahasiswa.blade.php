@extends('layouts.template')

@section('content')
    <div class="container-fluid">
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

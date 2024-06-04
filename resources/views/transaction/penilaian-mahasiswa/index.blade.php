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
                        <table class="table table-striped table-hover table-full-width" id="table_menu">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Nama Pembimbing Dosen</th>
                                    <th>Nama Instruktur Lapangan</th>
                                    <th>Komentar Dosen Pembimbing</th>
                                    <th>Komentar Instruktur Lapangan</th>
                                    <th>Nilai Dosen Pembimbing</th>
                                    <th>Nilai Instruktur Lapangan</th>
                                </tr>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('content-js')
    <script>
        $(document).ready(function() {
            dataMaster = $('#table_menu').DataTable({
                "bServerSide": true,
                "bAutoWidth": false,
                "ajax": {
                    "url": "{{ $page->url }}/list",
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
                        "mData": "mahasiswa.nama_mahasiswa",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "pembimbing_dosen.dosen.dosen_name",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "instruktur_lapangan.instruktur.nama_instruktur",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "nilai_dosen_pembimbing",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "komentar_dosen_pembimbing",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true,
                    }
                    {
                        "mData": "nilai_instruktur_lapangan",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "komentar_instruktur_lapangan",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true,
                    }
                ],
                "fnDrawCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    $('a', this.fnGetNodes()).tooltip();
                }
            });
        });
    </script>
@endpush

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
                        <div class="card-tools">
                            @if ($allowAccess->create)
                                <button type="button" data-block="body"
                                    class="btn btn-sm btn-{{ $theme->button }} mt-1 ajax_modal"
                                    data-url="{{ $page->url }}/create"><i class="fas fa-plus"></i> Tambah</button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-full-width" id="table_master">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Mahasiswa</th>
                                        <th>No Telephone Mahasiswa</th>
                                        <th>Judul</th>
                                        <th>Berita Acara</th>
                                        <th>Nilai Instruktur Lapangan</th>
                                        <th>Nilai Dosen Pembimbing</th>
                                        <th>Nilai Dosen Pembahas</th>
                                        <th>Status Magang</th>
                                        <th>Detail</th>
                                        {{-- <th>#</th> --}}
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
                        "mData": "nama_mahasiswa", // Menggunakan properti prodi_name dari relasi prodi
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "no_hp", // Menggunakan properti prodi_name dari relasi prodi
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "Judul",
                        "sClass": "",
                        "sWidth": "20%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "Berita_acara",
                        "sClass": "",
                        "sWidth": "20%",
                        "bSortable": true,
                        "bSearchable": true,
                        "mRender": function(data, type, row, meta) {
                            if (data) {
                                var url = '{{ asset('storage/assets/berita-acara') }}/' + data;
                                return '<span class="badge badge-success"><a href="' + url +
                                    '" target="_blank" class="text-white text-decoration-underline">Sudah Upload Berita Acara</a></span>';
                            } else {
                                return '<span class="badge badge-danger">Belum Upload Berita Acara</span>';
                            }
                        }
                    },
                    {
                        "mData": "nilai_exist_instruktur",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": false,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            if (data) {
                                return '<span class="badge badge-success">Sudah Mengisi Nilai</span>';
                            } else {
                                return '<span class="badge badge-danger">Belum Mengisi Nilai</span>';
                            }
                        }
                    },
                    {
                        "mData": "nilai_exist_pembimbing",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": false,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            if (data) {
                                return '<span class="badge badge-success">Sudah Mengisi Nilai</span>';
                            } else {
                                return '<span class="badge badge-danger">Belum Mengisi Nilai</span>';
                            }
                        }
                    },
                    {
                        "mData": "nilai_exist_pembahas",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": false,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            if (data) {
                                return '<span class="badge badge-success">Sudah Mengisi Nilai</span>';
                            } else {
                                return '<span class="badge badge-danger">Belum Mengisi Nilai</span>';
                            }
                        }
                    },
                    {
                        "mData": "semhas_daftar_id",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": false,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            let magangStatus = row.magang_status === 'Sudah Selesai Magang' ?
                                '<span class="badge badge-success">' + row.magang_status +
                                '</span>' :
                                '<span class="badge badge-danger">' + row.magang_status + '</span>';
                            return magangStatus;

                        }
                    },
                    {
                        "mData": "semhas_daftar_id",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": false,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            if (!row.nilai_exist_instruktur && !row.nilai_exist_pembimbing && !row
                                .nilai_exist_pembahas) {
                                // Tampilkan kosong jika semua nilai-nilai tersebut tidak ada
                                return '';
                            } else {
                                let buttons = '';
                                if (!row.nilai_exist_instruktur) {
                                    buttons +=
                                        `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/nilai-pembimbing" class="ajax_modal btn btn-xs btn-success tooltips text-secondary" data-placement="left" data-original-title="Nilai Pembimbing" ><i class="fas fa-chalkboard-teacher text-white" style="color: #ffffff;"></i></a>
                                        <a href="#" data-block="body" data-url="{{ $page->url }}/${data}/nilai-pembahas" class="ajax_modal btn btn-xs btn-primary tooltips text-secondary" data-placement="left" data-original-title="Nilai Pembahas" ><i class="fas fa-user-tie text-white" style="color: #ffffff;"></i></a>`;
                                }
                                if (!row.nilai_exist_pembimbing) {
                                    buttons +=
                                        `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/nilai-pembahas" class="ajax_modal btn btn-xs btn-primary tooltips text-secondary" data-placement="left" data-original-title="Nilai Pembahas" ><i class="fas fa-user-tie text-white" style="color: #ffffff;"></i></a>
                            <a href="#" data-block="body" data-url="{{ $page->url }}/${data}/nilai-instruktur" class="ajax_modal btn btn-xs btn-info tooltips text-secondary" data-placement="left" data-original-title="Nilai Instruktur" ><i class="fas fa-hard-hat text-white" style="color: #ffffff;"></i></a>`;
                                }
                                if (!row.nilai_exist_pembahas) {
                                    buttons +=
                                        `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/nilai-pembimbing" class="ajax_modal btn btn-xs btn-success tooltips text-secondary" data-placement="left" data-original-title="Nilai Pembimbing" ><i class="fas fa-chalkboard-teacher text-white" style="color: #ffffff;"></i></a>
                                        <a href="#" data-block="body" data-url="{{ $page->url }}/${data}/nilai-instruktur" class="ajax_modal btn btn-xs btn-info tooltips text-secondary" data-placement="left" data-original-title="Nilai Instruktur" ><i class="fas fa-hard-hat text-white" style="color: #ffffff;"></i></a>`;
                                }
                                if (row.nilai_exist_instruktur && row.nilai_exist_pembimbing && row
                                    .nilai_exist_pembahas) {
                                    buttons +=
                                        `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/nilai-akhir" class="ajax_modal btn btn-xs btn-danger tooltips text-secondary" data-placement="left" data-original-title="Nilai Akhir" ><i class="fas fa-medal text-white" style="color: #ffffff;"></i></a>`;
                                    buttons +=
                                        `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/nilai-pembimbing" class="ajax_modal btn btn-xs btn-success tooltips text-secondary" data-placement="left" data-original-title="Nilai Pembimbing" ><i class="fas fa-chalkboard-teacher text-white" style="color: #ffffff;"></i></a>
                            <a href="#" data-block="body" data-url="{{ $page->url }}/${data}/nilai-pembahas" class="ajax_modal btn btn-xs btn-primary tooltips text-secondary" data-placement="left" data-original-title="Nilai Pembahas" ><i class="fas fa-user-tie text-white" style="color: #ffffff;"></i></a>
                            <a href="#" data-block="body" data-url="{{ $page->url }}/${data}/nilai-instruktur" class="ajax_modal btn btn-xs btn-info tooltips text-secondary" data-placement="left" data-original-title="Nilai Instruktur" ><i class="fas fa-hard-hat text-white" style="color: #ffffff;"></i></a>`;
                                }
                                return buttons;
                            }
                        }
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

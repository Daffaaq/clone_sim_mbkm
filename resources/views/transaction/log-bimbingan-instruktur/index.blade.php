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
                        <div id="filter" class="form-horizontal filter-date p-2 border-bottom">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-sm row text-sm mb-0">
                                        <div class="col-md-3">
                                            <select name="filter_mahasiswa"
                                                class="form-control form-control-sm w-100 filter_combobox filter_mahasiswa">
                                                <option value="">- Semua Mahasiswa -</option>
                                                @foreach ($mahasiswaDropdown as $userId => $namaMahasiswa)
                                                    <option value="{{ $userId }}">{{ $namaMahasiswa }}</option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">Filter Mahasiswa</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped table-hover table-full-width" id="table_menu">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jam Mulai</th>
                                    <th>Jam Selesai</th>
                                    <th>Penjelasan Kegiatan</th>
                                    <th>Status Dosen Pembimbing</th>
                                    <th>Status Pembimbing Lapangan</th>
                                    <th></th>
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

            $('.filter_combobox').on('change', function() {
                // Simpan nilai filter mahasiswa yang dipilih
                var selectedMahasiswa = $(this).val();

                // Kemudian buat permintaan AJAX untuk memperbarui tabel dengan filter yang dipilih
                dataMaster.ajax.reload(null, false);
            });


            dataMaster = $('#table_menu').DataTable({
                "bServerSide": true,
                "bAutoWidth": false,
                "ajax": {
                    "url": "{{ $page->url }}/list",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d.filter_mahasiswa = $('.filter_mahasiswa').val();
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
                        "mData": "tanggal",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "jam_mulai",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "jam_selesai",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "topik_bimbingan",
                        "sClass": "",
                        "sWidth": "40%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "status1",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true,
                        "mRender": function(data, type, row, meta) {
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
                    },
                    {
                        "mData": "status2",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true,
                        "mRender": function(data, type, row, meta) {
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
                    },
                    {
                        "mData": "log_bimbingan_id",
                        "sClass": "text-center",
                        "sWidth": "8%",
                        "bSortable": false,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            return ''
                            @if ($allowAccess->update)
                                +
                                `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/edit" class="ajax_modal btn btn-xs btn-warning tooltips text-light" data-placement="left" data-original-title="Edit Data" ><i class="fa fa-edit"></i></a> `
                            @endif

                            @if ($allowAccess->delete)
                                +
                                `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/delete" class="ajax_modal btn btn-xs btn-danger tooltips text-light" data-placement="left" data-original-title="Hapus Data" ><i class="fa fa-trash"></i></a> `
                            @endif ;
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

            $('.filter_level, .filter_parent').change(function() {
                dataMaster.draw();
            });
        });
    </script>
@endpush

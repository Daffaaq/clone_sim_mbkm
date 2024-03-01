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
                                        <th>Nama Perusahaan</th>
                                        <th>Posisi</th>
                                        <th>Tipe Kegiatan</th>
                                        <th>Nama Mahasiswa</th>
                                        <th>Periode</th>
                                        <th>Status</th>
                                        <th>#</th>
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
                        "mData": "kegiatan_perusahaan.perusahaan.nama_perusahaan",
                        "sClass": "",
                        "sWidth": "30%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "kegiatan_perusahaan.posisi_lowongan",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "kegiatan_perusahaan.tipe_kegiatan.nama_kegiatan",
                        "sClass": "",
                        "sWidth": "20%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "mahasiswa.nama_mahasiswa",
                        "sClass": "",
                        "sWidth": "20%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "periode.semester",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true,
                        "mRender": function(data, type, row, meta) {
                            return data + ' - ' + row.periode.tahun_ajar;
                        }
                    },
                    {
                        "mData": "status",
                        "sClass": "",
                        "sWidth": "5%",
                        "bSortable": true,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            var status = '';
                            if (data == 0) {
                                status = '<span class="badge badge-info">Menunggu</span>';
                            } else if (data == 1) {
                                status =
                                    '<span class="badge badge-success">Diterima Koordinator</span>';
                            } else if (data == 2) {
                                status = '<span class="badge badge-danger">Menolak Undangan</span>';
                            } else if (data == 3) {
                                status =
                                    '<span class="badge badge-warning">Menerima Undangan</span>';
                            } else if (data == 4) {
                                status =
                                    '<span class="badge badge-danger">Ditolak Koordinator</span>';
                            }
                            return status;
                        }
                    },
                    {
                        "mData": "periode_id",
                        "sClass": "text-center pr-2",
                        "sWidth": "10%",
                        "bSortable": false,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            console.log(row);
                            var buttons = '';
                            @if ($allowAccess->update)
                                if (row.is_current != 1 && row.is_active == 1) {
                                    buttons +=
                                        `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/confirm_active" class="ajax_modal btn btn-xs btn-info tooltips text-white" data-placement="left" data-original-title="Set Active" ><i class="fa fa-check"></i></a> `;
                                }
                                buttons +=
                                    `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/edit" class="ajax_modal btn btn-xs btn-warning tooltips text-secondary" data-placement="left" data-original-title="Edit Data" ><i class="fa fa-edit"></i></a> `;
                            @endif
                            @if ($allowAccess->delete)
                                buttons +=
                                    `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/delete" class="ajax_modal btn btn-xs btn-danger tooltips text-light" data-placement="left" data-original-title="Hapus Data" ><i class="fa fa-trash"></i></a> `;
                            @endif

                            return buttons;
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

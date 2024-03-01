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
                            <button type="button" data-block="body"
                                class="btn btn-sm btn-{{ $theme->button }} mt-1 ajax_modal"
                                data-url="{{ $page->url }}/create"><i class="fas fa-plus"></i> Ajukan</button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-full-width" id="table_master">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Perusahaan</th>
                                        <th>Tipe Kegiatan</th>
                                        <th>Posisi</th>
                                        <th>Kuota</th>
                                        <th>Durasi</th>
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
                        "mData": "perusahaan.nama_perusahaan",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "tipe_kegiatan.nama_kegiatan",
                        "sClass": "",
                        "sWidth": "5%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "posisi_lowongan",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "kuota",
                        "sClass": "",
                        "sWidth": "25%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "periode_kegiatan",
                        "sClass": "",
                        "sWidth": "25%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "kegiatan_perusahaan_id",
                        "sClass": "text-center pr-2",
                        "sWidth": "10%",
                        "bSortable": false,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            console.log(row);
                            var buttons = '';
                            @if ($allowAccess->update)
                                // if (row.status == 0) {
                                //     buttons +=
                                //         `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/confirm_approve" class="ajax_modal btn btn-xs btn-success tooltips text-white" data-placement="left" data-original-title="Approve" ><i class="fa fa-check"></i></a> ` +
                                //         `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/confirm_reject" class="ajax_modal btn btn-xs btn-danger tooltips text-white" data-placement="left" data-original-title="Reject" ><i class="fa fa-times"></i></a> `;
                                // }
                                buttons +=
                                    `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/edit" class="ajax_modal btn btn-xs btn-warning tooltips text-secondary" data-placement="left" data-original-title="Edit Data" ><i class="fa fa-edit"></i></a> `;
                            @endif
                            @if ($allowAccess->delete)
                                buttons +=
                                    `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/delete" class="ajax_modal btn btn-xs btn-danger tooltips text-light" data-placement="left" data-original-title="Hapus Data" ><i class="fa fa-trash"></i></a> `;
                            @endif
                            buttons +=
                                `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}" class="ajax_modal btn btn-xs btn-info tooltips text-light text-xs" data-placement="left" data-original-title="Detail Kegiatan" ><i class="fa fa-eye"></i></a> `

                            if (row.is_undang) {
                                buttons +=
                                    `<a href="#" data-block="body" data-url="{{ $page->url }}/daftar/${data}" class="ajax_modal btn btn-xs btn-success tooltips text-light text-xs" data-placement="left" data-original-title="Daftar Kegiatan" >Daftar</a> `
                            } else {
                                if (!row.is_daftar) {

                                    buttons +=
                                        `<a href="#" data-block="body" data-url="{{ $page->url }}/daftar/${data}" class="ajax_modal btn btn-xs btn-success tooltips text-light text-xs" data-placement="left" data-original-title="Daftar Kegiatan" >Daftar</a> `
                                }
                            }
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

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
                            @if($allowAccess->create)
                                <button type="button" data-block="body" class="btn btn-sm btn-{{ $theme->button }} mt-1 ajax_modal" data-url="{{ $page->url }}/create"><i class="fas fa-plus"></i> Tambah</button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-0">

                        <!-- untuk Filter data -->
                        <div id="filter" class="form-horizontal filter-date p-2 border-bottom">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-sm row text-sm mb-0">
                                        <label class="col-md-1 col-form-label">Filter</label>
                                        <div class="col-md-4">
                                            <select class="form-control form-control-sm w-100 filter_combobox filter_prodi">
                                                <option value="">- Semua -</option>
                                                @foreach($prodi as $d)
                                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">Program Studi</small>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-control form-control-sm w-100 filter_combobox filter_kategori">
                                                <option value="">- Semua -</option>
                                                @foreach($kategori as $d)
                                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">Kategori</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-full-width" id="table_master">
                                <thead>
                                <tr><th>No</th>
                                    <th>Prodi</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Tanggal</th>
                                    <th>status</th>
                                    <th>Aksi</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div class="modal fade" id="preview_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body text-center" id="preview_doc"></div>
            </div>
        </div>
    </div>
@endsection

@push('content-css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/summernote/summernote.min.css') }}">
@endpush

@push('content-js')
    <script src="{{ asset('assets/plugins/summernote/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            $('.filter_combobox').select2();

            dataMaster = $('#table_master').DataTable({
                "bServerSide": true,
                "bAutoWidth": false,
                "ajax": {
                    "url": "{{ $page->url }}/list",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d.prodi = $('.filter_prodi').val();
                        d.kategori = $('.filter_kategori').val();
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
                        "mData": "prodi_code",
                        "sWidth": "8%",
                        "bSortable": true,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            return (data)? data : '~ ALL ~';
                        }
                    },
                    {
                        "mData": "berita_judul",
                        "sClass": "w-40 datatable_overflow",
                        "sWidth": "40%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "kategori_name",
                        "sClass": "w-20 datatable_overflow",
                        "sWidth": "24%",
                        "bSortable": true,
                        "bSearchable": false,
                    },
                    {
                        "mData": "tanggal",
                        "sClass": "",
                        "sWidth": "11%",
                        "bSortable": true,
                        "bSearchable": false,
                    },
                    {
                        "mData": "berita_status",
                        "sClass": "",
                        "sWidth": "7%",
                        "bSortable": true,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            return (data)? '<span class="badge badge-success">Publish</span>' : '<span class="badge badge-danger">Closed</span>';
                        }
                    },
                    {
                        "mData": "berita_id",
                        "sClass": "text-center pr-2",
                        "sWidth": "8%",
                        "bSortable": false,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            return  ''
                                @if($allowAccess->read) + `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}" class="ajax_modal btn btn-xs btn-info tooltips text-light" data-placement="left" data-original-title="Detail Data" ><i class="fa fa-list"></i></a> ` @endif
                                @if($allowAccess->update) + `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/edit" class="ajax_modal btn btn-xs btn-warning tooltips text-secondary" data-placement="left" data-original-title="Edit Data" ><i class="fa fa-edit"></i></a> ` @endif
                                @if($allowAccess->delete) + `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/delete" class="ajax_modal btn btn-xs btn-danger tooltips text-light" data-placement="left" data-original-title="Hapus Data" ><i class="fa fa-trash"></i></a> ` @endif
                                ;
                        }
                    }
                ],
                "fnDrawCallback": function ( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    $( 'a', this.fnGetNodes() ).tooltip();
                }
            });

            $('.dataTables_filter input').unbind().bind('keyup', function(e) {
                if (e.keyCode == 13) {
                    dataMaster.search($(this).val()).draw();
                }
            });

            $('.filter_kategori, .filter_prodi').change(function (){
                dataMaster.draw();
            });
        });

    </script>

@endpush

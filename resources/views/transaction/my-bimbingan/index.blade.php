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
                        {{-- @if (Auth::user()->group_id == 1)
                            <div id="filter" class="form-horizontal filter-date p-2 border-bottom">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-group-sm row text-sm mb-0">
                                            <label class="col-md-1 col-form-label">Filter</label>
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif --}}
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-full-width" id="table_master">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Prodi</th>
                                        <th>Nim</th>
                                        <th>Nama Mahasiswa</th>
                                        <th>Nama Mitra</th>
                                        <th>Skema</th>
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
                    "type": "POST",
                    "data": function(d) {
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
                        "mData": "prodi_name",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "nim",
                        "sClass": "",
                        "sWidth": "20%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "nama_mahasiswa",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "mitra_nama",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "magang_skema",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "pembimbing_dosen_id",
                        "sClass": "text-center pr-2",
                        "sWidth": "10%",
                        "bSortable": false,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            // var buttons = '';
                            // @if ($allowAccess->update)
                            //     // if (row.status == 0) {
                            //     //     buttons +=
                            //     //         `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/confirm_approve" class="ajax_modal btn btn-xs btn-success tooltips text-white" data-placement="left" data-original-title="Approve" ><i class="fa fa-check"></i></a> ` +
                            //     //         `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/confirm_reject" class="ajax_modal btn btn-xs btn-danger tooltips text-white" data-placement="left" data-original-title="Reject" ><i class="fa fa-times"></i></a> `;
                            //     // }
                            //     buttons +=
                            //         `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/edit" class="ajax_modal btn btn-xs btn-warning tooltips text-secondary" data-placement="left" data-original-title="Edit Data" ><i class="fa fa-edit"></i></a> `;
                            // @endif
                            // @if ($allowAccess->delete)
                            //     buttons +=
                            //         `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/delete" class="ajax_modal btn btn-xs btn-danger tooltips text-light" data-placement="left" data-original-title="Hapus Data" ><i class="fa fa-trash"></i></a> `;
                            // @endif

                            // return buttons;
                            return '<a href="#" data-block="body" data-url="{{ $page->url }}/' +
                                data +
                                '" class="ajax_modal btn btn-xs btn-info tooltips text-secondary" data-placement="left" data-original-title="show Data" ><i class="fa fa-eye text-white"></i></a>';
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

            $('.filter_prodi').change(function() {
                dataMaster.draw();
            });
        });
    </script>
@endpush

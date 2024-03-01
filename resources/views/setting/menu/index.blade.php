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
                                    <label for="filter_date" class="col-md-1 col-form-label">Filter</label>
                                    <div class="col-md-3">
                                        <select name="filter_level"
                                            class="form-control form-control-sm w-100 filter_combobox filter_level">
                                            <option value="">- Semua -</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                        </select>
                                        <small class="form-text text-muted">Level Menu</small>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="filter_parent"
                                            class="form-control form-control-sm w-100 filter_combobox filter_parent">
                                            <option value="">- All Parent -</option>
                                            @foreach($parent as $p)
                                            <option value="{{ $p->id }}">{{ $p->code .' - '. $p->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Parent Menu</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-hover table-full-width" id="table_menu">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Url</th>
                                <th>Lvl</th>
                                <th>Urut</th>
                                <th>Parent</th>
                                <th>Status</th>
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

        $('.filter_combobox').select2();

        dataMaster = $('#table_menu').DataTable({
            "bServerSide": true,
            "bAutoWidth": false,
            "ajax": {
                "url": "{{ $page->url }}/list",
                "dataType": "json",
                "type": "POST",
                "data": function(d) {
                    d.level  = $('.filter_level').val();
                    d.parent = $('.filter_parent').val();
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
                    "mData": "menu_code",
                    "sClass": "",
                    "sWidth": "12%",
                    "bSortable": true,
                    "bSearchable": true
                },
                {
                    "mData": "menu_name",
                    "sClass": "",
                    "sWidth": "25%",
                    "bSortable": true,
                    "bSearchable": true
                },
                {
                    "mData": "menu_url",
                    "sClass": "",
                    "sWidth": "15%",
                    "bSortable": true,
                    "bSearchable": false
                },
                {
                    "mData": "menu_level",
                    "sClass": "",
                    "sWidth": "5%",
                    "bSortable": true,
                    "bSearchable": false
                },
                {
                    "mData": "order_no",
                    "sClass": "",
                    "sWidth": "5%",
                    "bSortable": false,
                    "bSearchable": true
                },
                {
                    "mData": "parent",
                    "sClass": "",
                    "sWidth": "12%",
                    "bSortable": true,
                    "bSearchable": false,
                    "mRender": function(data, type, row, meta) {
                        return row.menu_code == null ? '-' : row.menu_code + `<small class="d-none">${row.parent_id}</small>`;
                    }
                },
                {
                    "mData": "is_active",
                    "sClass": "",
                    "sWidth": "8%",
                    "bSortable": true,
                    "bSearchable": false,
                    "mRender": function(data, type, row, meta) {
                        return data == 1 ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak Aktif</span>';
                    }
                },
                {
                    "mData": "menu_id",
                    "sClass": "text-center",
                    "sWidth": "8%",
                    "bSortable": false,
                    "bSearchable": false,
                    "mRender": function(data, type, row, meta) {
                        return ''
                            @if($allowAccess->update) + `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/edit" class="ajax_modal btn btn-xs btn-warning tooltips text-light" data-placement="left" data-original-title="Edit Data" ><i class="fa fa-edit"></i></a> ` @endif
                            
                            @if($allowAccess->delete) + `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/delete" class="ajax_modal btn btn-xs btn-danger tooltips text-light" data-placement="left" data-original-title="Hapus Data" ><i class="fa fa-trash"></i></a> ` @endif ;
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

        $('.filter_level, .filter_parent').change(function (){
            dataMaster.draw();
        });
    });

</script>
@endpush
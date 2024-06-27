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
                    <div id="filter" class="form-horizontal filter-date p-2 border-bottom">
                        <div class="row">
                            <label class="col-md-1 col-form-label">Filter</label>
                            <div class="col-md-4">
                                <select id="filter-action" class="form-control">
                                    <option value="">Semua</option>
                                    @foreach ($actions as $action)
                                        <option value="{{ $action->action }}">{{ $action->action }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Action</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-full-width" id="table_master">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama user</th>
                                        <th>aksi</th>
                                        <th>url</th>
                                        <th>Data</th>
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
                        "mData": "name",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "action",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "url",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "data",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true,
                        "render": function(data, type, full, meta) {
                            if (data !== null) {
                                var members = data.split('|');
                                var members_name = data.split('|');

                                var text = "";

                                $.each(members, function(i, val) {
                                    text = text +
                                        "<span class='badge badge-primary' style='font-size:15px'>" +
                                        members_name[i] +
                                        " (" + members[i] + ")</span> ";
                                });
                            }
                            return data !== null ? data : 'Tidak Ada Data';
                        }
                    }
                ],
                "fnDrawCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    $('a', this.fnGetNodes()).tooltip();
                }
            });
            $('#filter-action').on('change', function() {
                var action = $(this).val();
                dataMaster.column(2).search(action).draw();
            });

            $('.dataTables_filter input').unbind().bind('keyup', function(e) {
                if (e.keyCode == 13) {
                    dataMaster.search($(this).val()).draw();
                }
            });
        });
    </script>
@endpush

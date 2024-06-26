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
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-full-width" id="table_master"
                                style="text-align: center;">
                                <thead style="text-align: center;">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Mahasiswa</th>
                                        <th>Instruktur Lapangan</th>
                                        <th>Judul</th>
                                        <th>Nilai</th>
                                        <th>Status Nilai</th>
                                        <th>Jadwal Penilaian</th>
                                        <th>Jadwal</th>
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
                        "mData": "nama_instruktur",
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
                        "mData": "semhas_daftar_id",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": false,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            return '' +
                                `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/nilai" class="ajax_modal btn btn-xs btn-success tooltips text-secondary" data-placement="left" data-original-title="Nilai" ><i class="fas fa-tasks" style="color: #ffffff;"></i></a> `

                        }
                    },
                    {
                        "mData": "nilai_exist",
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
                        "mData": "jadwal",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": false,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            var todaydate = moment(); // todaydate sebagai objek moment
                            console.log("Today Date: " + todaydate.format('DD-MM-YYYY'));

                            var deadline = moment(data,
                                'YYYY-MM-DD'); // Ambil tanggal deadline dari data

                            if (!data || data === '-') {
                                return '<span class="badge badge-warning">Belum ada Jadwal Penilaian</span>';
                            } else {
                                if (deadline.isSameOrBefore(todaydate,
                                        'day'
                                        )) { // Periksa apakah deadline lebih awal dari atau sama dengan hari ini
                                    return '<span class="badge badge-danger">' + deadline.format(
                                        'DD-MM-YYYY') + '</span>';
                                } else {
                                    return '<span class="badge badge-success">' + deadline.format(
                                        'DD-MM-YYYY') + '</span>';
                                }
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
                            return '' +
                                `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}" class="ajax_modal btn btn-xs btn-info tooltips text-secondary" data-placement="left" data-original-title="show Data" ><i class="fa fa-eye" style="color: #ffffff;"></i></a> `

                        }
                    }
                    // {
                    //     "mData": "t_semhas_daftar.semhas_daftar_id",
                    //     "sClass": "text-center pr-2",
                    //     "sWidth": "10%",
                    //     "bSortable": false,
                    //     "bSearchable": false,
                    //     "mRender": function(data, type, row, meta) {
                    //         return ''
                    //         @if ($allowAccess->update)
                    //             +
                    //             `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/edit" class="ajax_modal btn btn-xs btn-warning tooltips text-secondary" data-placement="left" data-original-title="Edit Data" ><i class="fa fa-edit"></i></a> `
                    //         @endif
                    //         @if ($allowAccess->delete)
                    //             +
                    //             `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/delete" class="ajax_modal btn btn-xs btn-danger tooltips text-light" data-placement="left" data-original-title="Hapus Data" ><i class="fa fa-trash"></i></a> `
                    //         @endif ;
                    //     }
                    // }
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

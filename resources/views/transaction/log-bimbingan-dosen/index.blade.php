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
                        <div id="filter" class="form-horizontal filter-date p-2 border-bottom">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-sm row text-sm mb-0">
                                        <div class="col-md-3">
                                            <select name="filter_mahasiswa"
                                                class="form-control form-control-sm w-100 filter_combobox filter_mahasiswa">
                                                @foreach ($mahasiswaDropdown as $userId => $namaMahasiswa)
                                                    <option value="{{ $userId }}">{{ $namaMahasiswa }}</option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">Pilih Mahasiswa</small>
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
                                    <th>Nilai Pembimbing Dosen</th>
                                    <th>Action</th>
                                    <th>#</th>
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
            var logBimbinganId, statusDosen, nilaiPembimbing;
            $('body').on('change', '.toggle_status_dosen, .nilai_pembimbing_dosen', function(e) {
                e.preventDefault();
                logBimbinganId = $(this).data('id');
                statusDosen = $('.toggle_status_dosen[data-id="' + logBimbinganId + '"]').prop('checked') ?
                    1 : 2;
                nilaiPembimbing = $('.nilai_pembimbing_dosen[data-id="' + logBimbinganId + '"]').val();
                if (statusDosen == 2) {
                    nilaiPembimbing = 0.00; // Atau bisa juga diatur ke null
                }
                var isChecked = $(this).prop('checked');
                console.log("logBimbinganId:", logBimbinganId);
                console.log("statusDosen:", statusDosen);
                console.log("nilaiPembimbing:", nilaiPembimbing);
                console.log("isChecked:", isChecked);
                var label = $(this).siblings('.custom-control-label');
                if (statusDosen == 1) {
                    label.removeClass('text-danger').addClass('text-success').text('Menerima');
                } else if (statusDosen == 2) {
                    label.removeClass('text-success').addClass('text-danger').text('Menolak');
                } else {
                    label.removeClass('text-success text-danger').text('Menunggu');
                }
            });

            // Fungsi yang dipanggil saat tombol submit manual ditekan
            $('body').on('click', '.manual_submit_button', function() {
                logBimbinganId = $(this).data('id');
                statusDosen = $('.toggle_status_dosen[data-id="' + logBimbinganId + '"]').prop(
                    'checked') ? 1 : 2;
                nilaiPembimbing = $('.nilai_pembimbing_dosen[data-id="' + logBimbinganId + '"]').val();
                if (statusDosen == 2) {
                    nilaiPembimbing = 0.00; // Atau bisa juga diatur ke null
                }

                // Lakukan permintaan AJAX untuk menyimpan perubahan
                $.ajax({
                    url: "{{ route('update.logbimbingan.dosen') }}",
                    type: "POST",
                    data: {
                        log_bimbingan_id: logBimbinganId,
                        status1: statusDosen,
                        nilai_pembimbing_dosen: nilaiPembimbing
                    },
                    success: function(response) {
                        if (response.success) {
                            dataMaster.ajax.reload(null, false);
                            // location.reload();
                        } else {
                            // Tampilkan pesan validasi di dalam form
                            var errorDiv = $('.nilai_pembimbing_dosen[data-id="' +
                                logBimbinganId + '"]').siblings('.error-message');
                            errorDiv.text(response.message);
                            errorDiv.show();
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });

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
                            console.log("Nilai data: ", data);
                            var toggleSwitch = '<div class="custom-control custom-switch">';
                            toggleSwitch +=
                                '<input type="checkbox" class="custom-control-input toggle_status_dosen" id="toggle_' +
                                row.log_bimbingan_id + '"';
                            toggleSwitch += ' data-id="' + row.log_bimbingan_id +
                                '" data-status="' + data + '"';
                            if (data == 1) {
                                toggleSwitch += ' checked>';
                                toggleSwitch +=
                                    '<label class="custom-control-label text-success" for="toggle_' +
                                    row.log_bimbingan_id + '">Menerima</label>';
                            } else if (data == 2) {
                                toggleSwitch += '>';
                                toggleSwitch +=
                                    '<label class="custom-control-label text-danger" for="toggle_' +
                                    row.log_bimbingan_id + '">Menolak</label>';
                            } else {
                                toggleSwitch += '>';
                                toggleSwitch += '<label class="custom-control-label" for="toggle_' +
                                    row.log_bimbingan_id + '">Menunggu</label>';
                            }
                            toggleSwitch += '</div>';
                            return toggleSwitch;
                        }
                    },
                    {
                        "mData": "nilai_pembimbing_dosen",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true,
                        "mRender": function(data, type, row, meta) {
                            var value = data ? data : "0.00";
                            var inputHtml =
                                '<input type="text" class="form-control nilai_pembimbing_dosen" data-id="' +
                                row.log_bimbingan_id + '" value="' + value + '">';
                            inputHtml +=
                                '<div class="error-message text-danger" style="display:none;"></div>'; // tambahkan ini

                            setTimeout(function() {
                                $(".error-message").fadeOut(
                                    1000); // Hilangkan error message setelah 5 detik
                            }, 5000);

                            return inputHtml;
                        }
                    },
                    {
                        "mData": "log_bimbingan_id",
                        "sClass": "text-center",
                        "sWidth": "8%",
                        "bSortable": false,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            var buttons = '';
                            // @if ($allowAccess->update)
                            //     +
                            //     `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/edit" class="ajax_modal btn btn-xs btn-warning tooltips text-light" data-placement="left" data-original-title="Edit Data" ><i class="fa fa-edit"></i></a> `
                            // @endif

                            // @if ($allowAccess->delete)
                            //     +
                            //     `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/delete" class="ajax_modal btn btn-xs btn-danger tooltips text-light" data-placement="left" data-original-title="Hapus Data" ><i class="fa fa-trash"></i></a> `
                            // @endif ;
                            buttons += '<button id="manual_submit_button_' + data +
                                '" class="manual_submit_button btn btn-xs btn-success py-0 px-1 tooltips text-secondary" data-id="' +
                                data +
                                '"><i class="fa fa-check text-white" style="font-size: smaller;"></i></button>';

                            return buttons;
                        }
                    },
                    {
                        "mData": "log_bimbingan_id",
                        "sClass": "text-center",
                        "sWidth": "8%",
                        "bSortable": false,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            return '<a href="#" data-block="body" data-url="{{ $page->url }}/' +
                                data +
                                '" class="ajax_modal btn btn-xs btn-info tooltips text-secondary" data-placement="left" data-original-title="show Data" ><i class="fa fa-eye text-white"></i></a>';
                        }
                    }
                ],
                "columnDefs": [{
                    "targets": 4,
                    "render": function(data, type, row, meta) {
                        return '<div style="overflow-wrap: break-word; max-width: 300px;">' +
                            data + '</div>';
                    }
                }],
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

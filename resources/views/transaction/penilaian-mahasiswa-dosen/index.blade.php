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
                        <table class="table table-striped table-hover table-full-width" id="table_menu">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Mahasiswa</th>
                                    <th>nilai_dosen_instruktur_lapangan</th>
                                    <th>komentar_instruktur_lapangan</th>
                                    <th>Action</th>
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

            $('body').on('click', '.manual_submit_button', function() {
                var penilaianId = $(this).data('id');
                var dosenId = $(this).data('dosen_id');
                var mahasiswaId = $(this).data('mahasiswa-id');
                var nilai = $(this).closest('tr').find('.form-control')
                    .val(); // Ambil nilai dari dropdown di baris yang sesuai
                var komentar = $(this).closest('tr').find('textarea')
                    .val(); // Ambil komentar dari textarea di baris yang sesuai
                // Saat validasi gagal
                // Jika validasi gagal
                if (komentar.trim() === '') {
                    $(this).closest('tr').find('textarea').css('border-color',
                        '#dc3545'); // Set border color to red
                    $(this).closest('tr').find('.error-message').show(); // Tampilkan pesan kesalahan
                    return; // Stop proses jika validasi gagal
                } else {
                    $(this).closest('tr').find('textarea').css('border-color', ''); // Reset border color
                    $(this).closest('tr').find('.error-message')
                        .hide(); // Sembunyikan pesan kesalahan jika sudah valid
                }



                // Kirim data melalui AJAX
                $.ajax({
                    url: "{{ route('update.penilaian.mahasiswa.dosen') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        mahasiswa_id: mahasiswaId,
                        pembimbing_dosen_id: dosenId,
                        nilai_dosen_pembimbing: nilai,
                        komentar_dosen_pembimbing: komentar
                    },
                    success: function(response) {
                        // Tambahkan logika untuk menangani respons
                        console.log(response);
                        location.reload();
                        // Tambahkan logika untuk memberi tahu pengguna bahwa data telah disimpan
                    },
                    error: function(xhr, status, error) {
                        // Tambahkan logika untuk menangani kesalahan jika terjadi
                        console.error(xhr.responseText);
                    }
                });
            });



            dataMaster = $('#table_menu').DataTable({
                "bServerSide": true,
                "bAutoWidth": false,
                "ajax": {
                    "url": "{{ $page->url }}/list",
                    "dataType": "json",
                    "type": "POST",
                },
                "aoColumns": [{
                        "mData": "no",
                        "sClass": "text-center",
                        "sWidth": "5%",
                        "bSortable": false,
                        "bSearchable": false
                    },
                    {
                        "mData": "mahasiswa.nama_mahasiswa",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true,
                        "mRender": function(data, type, row) {
                            var mahasiswaId = row.mahasiswa
                                .mahasiswa_id; // Ambil mahasiswa_id dari objek row
                            return '<span data-mahasiswa-id="' + mahasiswaId + '">' + data +
                                '</span>';
                        }
                    },
                    {
                        "mData": "nilai_dosen_pembimbing",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true,
                        "mRender": function(data, type, row) {
                            var dropdown = '';
                            // Jika sudah disubmit, tampilkan nilai menggunakan dropdown
                            dropdown += '<select class="form-control">';
                            dropdown += '<option value="Baik Sekali"' + (data === 'Baik Sekali' ?
                                ' selected' : '') + '>Baik Sekali</option>';
                            dropdown += '<option value="Baik"' + (data === 'Baik' ? ' selected' :
                                '') + '>Baik</option>';
                            dropdown += '<option value="Cukup"' + (data === 'Cukup' ? ' selected' :
                                '') + '>Cukup</option>';
                            dropdown += '<option value="Kurang"' + (data === 'Kurang' ?
                                ' selected' : '') + '>Kurang</option>';
                            dropdown += '<option value="Kurang Sekali"' + (data ===
                                'Kurang Sekali' ? ' selected' : '') + '>Kurang Sekali</option>';
                            dropdown += '</select>';
                            return dropdown;
                        }
                    },
                    {
                        "mData": "komentar_dosen_pembimbing",
                        "sClass": "",
                        "sWidth": "15%",
                        "bSortable": true,
                        "bSearchable": true,
                        // "mRender": function(data, type, row) {
                        //     var textarea = '';
                        //     // Jika belum disubmit, tampilkan textarea untuk mengubah komentar
                        //     textarea = '<textarea class="form-control">' + (data ? data : '') +
                        //         '</textarea>';
                        //     return textarea;
                        // }
                        "mRender": function(data, type, row) {
                            var textarea = '';
                            // Jika belum disubmit, tampilkan textarea untuk mengubah komentar
                            textarea = '<textarea class="form-control">' + (data ? data : '') +
                                '</textarea>';
                            textarea +=
                                '<span class="text-danger error-message" style="display: none;">Komentar harus diisi.</span>';
                            return textarea;
                        }
                    },


                    {
                        "mData": "penilaian_mahasiswa_id",
                        "sClass": "text-center",
                        "sWidth": "8%",
                        "bSortable": false,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            var buttons = '';
                            buttons += '<button id="manual_submit_button_' + row
                                .penilaian_mahasiswa_id +
                                '" class="manual_submit_button btn btn-xs btn-primary tooltips text-light" data-id="' +
                                row.penilaian_mahasiswa_id + '" data-mahasiswa-id="' + row.mahasiswa
                                .mahasiswa_id +
                                '" data-dosen_id="' + row.pembimbing_dosen_id +
                                '"><i class="fa fa-check"></i> Submit</button>';

                            return buttons;
                        }
                    }

                ],
                "fnDrawCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    $('a', this.fnGetNodes()).tooltip();
                }
            });
        });
    </script>
@endpush

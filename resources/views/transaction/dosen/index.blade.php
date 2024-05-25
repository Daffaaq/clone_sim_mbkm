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
                            <button type="button" class="btn btn-sm btn-success mt-1" id="btnImport"><i
                                    class="fas fa-file-import"></i> Import</button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="importSuccessMessage" class="alert alert-success" style="display: none;">
                            Data Dosen berhasil diimpor
                        </div>
                        <div id="importerrorMessage" class="alert alert-danger" style="display: none;">
                            Data Dosen gagal diimpor
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-full-width" id="table_master">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Email Dosen</th>
                                        <th>Nama Dosen</th>
                                        <th>Kuota Dosen</th>
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
    <div class="modal fade" id="importFileModal" tabindex="-1" role="dialog" aria-labelledby="importFileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importFileModalLabel">Import Data Dosen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="importFileForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            {{-- <label for="file">Import Data Dosen:</label> --}}
                            <div class="custom-file">
                                <input type="file" class="form-control-sm custom-file-input" id="file"
                                    name="file" data-rule-filesize="1"data-rule-accept=".xls,.xlsx" accept=".xls,.xlsx">
                                <label class="form-control-sm custom-file-label" for="file">Choose file</label>
                                <small id="excel" class="form-text" style="margin-left: 0px; color: red;">File type:
                                    .xls/.xlsx</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('content-js')
    <script>
        var loadFile = function(event) {
            $('input.custom-file-input').on('change', function() {
                // Get the file name
                var fileName = $(this).val().split('\\').pop();

                // Set the label text to the file name
                $(this).next('.custom-file-label').html(fileName);
            });

        };
        $(document).ready(function() {
            loadFile()
            $('#btnImport').click(function() {
                // Tampilkan modal untuk memilih file
                $('#importFileModal').modal('show');
            });
            $('#importFileForm').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: '{{ route('dosen.import') }}', // Menggunakan route untuk mengikat ke URL dengan nama yang benar
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.stat) {
                            // Tutup modal setelah berhasil mengimpor
                            $('#importFileModal').modal('hide');
                            // Reset form
                            $('#importFileForm')[0].reset();
                            // Clear file input label
                            $('#importFileForm .custom-file-label').html('Choose file');
                            // Show success message
                            $('#importSuccessMessage').show().delay(5000).fadeOut();
                            // Refresh tabel data
                            dataMaster.ajax.reload();
                        } else {
                            $('#importFileModal').modal('hide');
                            $('#importFileForm')[0].reset();
                            // Clear file input label
                            $('#importFileForm .custom-file-label').html('Choose file');
                            $('#importerrorMessage').show().delay(5000).fadeOut();
                        }
                    },
                    error: function(xhr, status, error) {
                        // Tangani kesalahan
                        $('#importFileModal').modal('hide');
                        $('#importFileForm')[0].reset();
                        // Clear file input label
                        $('#importFileForm .custom-file-label').html('Choose file');
                        $('#importerrorMessage').show().delay(5000).fadeOut();
                        console.log('Full error response:', xhr);
                    }
                });
            });
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
                        "mData": "dosen_email",
                        "sClass": "",
                        "sWidth": "20%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "dosen_name",
                        "sClass": "",
                        "sWidth": "65%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "kuota",
                        "sClass": "",
                        "sWidth": "10%",
                        "bSortable": true,
                        "bSearchable": true,
                        "mRender": function(data, type, row, meta) {
                            const jumlah = row.pembimbing_dosen_count + '/' + data;
                            if (data == 0 || data == row.pembimbing_dosen_count) {
                                return '<span class="badge badge-danger">' + jumlah + '</span>'
                            } else {
                                return jumlah
                            }
                        }
                    },
                    {
                        "mData": "dosen_id",
                        "sClass": "text-center pr-2",
                        "sWidth": "10%",
                        "bSortable": false,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            return ''
                            @if ($allowAccess->update)
                                +
                                `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/edit" class="ajax_modal btn btn-xs btn-warning tooltips text-secondary" data-placement="left" data-original-title="Edit Data" ><i class="fa fa-edit"></i></a> `
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
        });
    </script>
@endpush

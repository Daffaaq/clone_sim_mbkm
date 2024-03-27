<?php
// jika $data ada ISI-nya maka actionnya adalah edit, jika KOSONG : insert
$is_edit = isset($data);
?>

<form method="post" action="{{ $page->url }}" role="form" class="form-horizontal" id="form-master">
    @csrf
    @method('POST')
    <div id="modal-master" class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-message text-center"></div>
                <div class="form-group row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Cari
                        Mahasiswa</label>
                    <div class="col-sm-9">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control form-control-sm" id="search"
                                autocomplete="off" />
                            <span class="input-group-append">
                                <button type="button" class="btn btn-info btn-flat" id="btn-cari-mhs">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="mahasiswa_id" id="mahasiswa_id">
                <table class="table table-sm mb-0" id="mhs-table">
                    <tr>
                        <th class="w-20 text-right">NIM</th>
                        <th class="w-1">:</th>
                        <td class="w-77">
                            <span id="nim"></span>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-20 text-right">Nama</th>
                        <th class="w-1">:</th>
                        <td class="w-77">
                            <span id="nama_mahasiswa"></span>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-20 text-right">Kelas</th>
                        <th class="w-1">:</th>
                        <td class="w-77">
                            <span id="kelas"></span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Tambahkan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        unblockUI();

        $("#btn-cari-mhs").click(function() {
            $('.form-message').html('');
            const nim = $('#search').val()

            $.ajax({
                url: "{{ url('mahasiswa') }}" + "/" + nim + "/cari",
                type: "GET",
                success: function(response) {
                    // Handle the success response here
                    console.log(response);
                    if (response.stat) {
                        $('#mahasiswa_id').val(response.data.mahasiswa_id)
                        $('#nim').text(response.data.nim)
                        $('#nama_mahasiswa').text(response.data.nama_mahasiswa)
                        $('#kelas').text(response.data.kelas)
                    } else {
                        setFormMessage('.form-message', response);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle errors here
                    console.error(xhr.responseText);
                }
            });
        })

        $("#form-master").validate({
            rules: {
                // nama_kegiatan: {
                //     required: true,
                //     maxlength: 100
                // }
            },
            submitHandler: function(form) {
                $('.form-message').html('');
                blockUI(form);
                $(form).ajaxSubmit({
                    dataType: 'json',
                    success: function(data) {
                        unblockUI(form);
                        setFormMessage('.form-message', data);
                        if (data.stat) {
                            window.location.reload();
                        }
                        closeModal($modal, data);
                    }
                });
            },
            validClass: "valid-feedback",
            errorElement: "div",
            errorClass: 'invalid-feedback',
            errorPlacement: erp,
            highlight: hl,
            unhighlight: uhl,
            success: sc
        });
    });
</script>

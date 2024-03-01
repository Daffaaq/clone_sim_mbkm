<?php
// jika $data ada ISI-nya maka actionnya adalah edit, jika KOSONG : insert
$is_edit = isset($data);
?>

<form method="post" action="{{ $page->url }}" role="form" class="form-horizontal" id="form-master">
    @csrf
    {!! $is_edit ? method_field('PUT') : '' !!}
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
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Prodi</label>
                    <div class="col-sm-9">
                        <select data-testid="partner-category" class="form-control form-control-sm" id="prodi_id"
                            name="prodi_id">
                            <option disabled selected value="">Pilih Prodi</option>
                            @foreach ($prodis as $prodi)
                                <option value="{{ $prodi->prodi_id }}"
                                    {{ isset($data->prodi_id) && $data->prodi_id == $prodi->prodi_id ? 'selected' : '' }}>
                                    {{ $prodi->prodi_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Kuota</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="kuota" name="kuota"
                            value="{{ isset($data->kuota) ? $data->kuota : '' }}" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        unblockUI();

        $('#provinsi_id').change(function() {
            var provinsi_id = $(this).val();
            if (provinsi_id) {
                $.ajax({
                    url: "{{ url('kota') }}?provinsi_id=" + provinsi_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#kota_id').empty();
                        $('#kota_id').append(
                            '<option disabled selected value="">Pilih kabupaten/kota</option>'
                        );
                        $.each(data, function(key, value) {
                            $('#kota_id').append('<option value="' + value.id +
                                '">' + value.nama_kab_kota +
                                '</option>');
                        });
                    }
                });
            } else {
                $('#kota_id').empty();
                $('#kota_id').append(
                    '<option disabled selected value="">Pilih kabupaten/kota</option>');
            }
        });

        $("#form-master").validate({
            rules: {
                nama_perusahaan: {
                    required: true,
                },
                kategori: {
                    required: true
                },
                tipe_industri: {
                    required: true
                },
                alamat: {
                    required: true
                },
                provinsi_id: {
                    required: true
                },
                kota_id: {
                    required: true
                },
                profil_perusahaan: {
                    required: true
                },
                website: {
                    required: true
                }
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
                            resetForm('#form-master');
                            dataMaster.draw(false);
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

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
                    <label class="col-sm-3 control-label col-form-label">Mahasiswa</label>
                    <div class="col-sm-9">
                        <select id="mahasiswa_id" name="mahasiswa_id[]"
                            class="form-control form-control-sm select2_combobox" multiple>
                            <option value="">- Pilih -</option>
                            @if (count($mahasiswa) > 0)
                                @foreach ($mahasiswa as $mahasiswa_id => $r)
                                    <option value="{{ $mahasiswa_id }}" data-magang-id="{{ $r['magang_id'] }}">
                                        {{ $r['nama_mahasiswa'] }}
                                    </option>
                                @endforeach
                            @else
                                <option disabled selected>Tidak ada mahasiswa yang diterima magang</option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="form-message text-center"></div>
                    <div class="form-group required row mb-2">
                        <label class="col-sm-3 control-label col-form-label">Dosen Pembimbing</label>
                        <div class="col-sm-9">
                            <select id="dosen_id" name="dosen_id"
                                class="form-control form-control-sm select2_combobox">
                                <option value="">- Pilih -</option>
                                @foreach ($dosen as $r)
                                    <option value="{{ $r->dosen_id }}">{{ $r->dosen_name }}</option>
                                @endforeach
                            </select>
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
{{-- <script src="/assets/js/select2.min.js"></script> --}}
<script>
    $(document).ready(function() {

        $('#mahasiswa_id').select2({
            placeholder: "Pilih satu atau lebih Mahasiswa",
            allowClear: true
        });
    });
</script>
<script>
    $(document).ready(function() {
        unblockUI();

        @if ($is_edit)
            $('#mahasiswa_id').val('{{ $data->mahasiswa_id }}').trigger('change');
            $('#dosen_id').val('{{ $data->dosen_id }}').trigger('change');
        @endif

        $("#form-master").validate({
            rules: {
                mahasiswa_id: {
                    required: true
                },
                dosen_id: {
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

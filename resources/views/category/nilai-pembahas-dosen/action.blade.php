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
                    <label class="col-sm-3 control-label col-form-label">Judul</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="name_kriteria_pembahas_dosen"
                            name="name_kriteria_pembahas_dosen"
                            value="{{ isset($data->name_kriteria_pembahas_dosen) ? $data->name_kriteria_pembahas_dosen : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Bobot Kriteria</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control form-control-sm" id="bobot" name="bobot"
                            value="{{ isset($data->bobot) ? $data->bobot : '' }}" />
                    </div>
                </div>
                @if ($is_edit && $data->subKriteria->isNotEmpty())
                    <div class="form-group required row mb-2">
                        <label class="col-sm-3 control-label col-form-label">Sub Kriteria</label>
                        <div class="col-sm-9">
                            @foreach ($data->subKriteria as $subKriteria)
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" name="sub_kriteria[]"
                                        id="sub_kriteria[]" value="{{ $subKriteria->name_kriteria_pembahas_dosen }}">
                                </div>
                                <input type="hidden" name="sub_kriteria_ids[]"
                                    value="{{ $subKriteria->nilai_pembahas_dosen_id }}">
                            @endforeach
                        </div>
                    </div>
                @endif
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

        $("#form-master").validate({
            rules: {
                name_kriteria_pembahas_dosen: {
                    required: true
                },
                bobot: {
                    required: true,
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

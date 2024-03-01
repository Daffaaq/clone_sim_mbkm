<?php
// jika $data ada ISI-nya maka actionnya adalah edit, jika KOSONG : insert
$is_edit = isset($data);
?>

<form method="post" action="{{ $page->url }}" role="form" class="form-horizontal" id="form-master">
    @csrf
    {!! ($is_edit)? method_field('PUT') : '' !!}
    <div id="modal-master" class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-message text-center"></div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-2 control-label col-form-label">Prodi & Kategori</label>
                    <div class="col-sm-5">
                        <select id="prodi_id" name="prodi_id" class="form-control form-control-sm select2_combobox">
                            <option value="">~ ALL ~</option>
                            @foreach ($prodi as $r)
                                <option value="{{ $r->prodi_id }}">{{ $r->prodi_code.' - '.$r->prodi_name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Program Studi</small>
                    </div>
                    <div class="col-sm-5">
                        <select id="kategori_id" name="kategori_id" class="form-control form-control-sm select2_combobox">
                            <option value="">- Pilih -</option>
                            @foreach ($kategori as $r)
                                <option value="{{ $r->kategori_id }}">{{ $r->kategori_name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Kategori</small>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-2 control-label col-form-label">Topik</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control form-control-sm" id="berita_judul" name="berita_judul" value="{{ isset($data->berita_judul) ? $data->berita_judul : '' }}"/>
                        <small class="form-text text-muted">Judul</small>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-2 control-label col-form-label">Deskripsi</label>
                    <div class="col-sm-10">
                        <textarea class="form-control form-control-sm summernote" id="berita_isi" name="berita_isi">{!! isset($data->berita_isi) ? $data->berita_isi : '' !!}</textarea>
                        <small class="form-text text-muted">Deskripsi Topik</small>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-2 control-label col-form-label">Status</label>
                    <div class="col-sm-10 mt-2">
                        <div class="icheck-success d-inline mr-3">
                            <input type="radio" id="radioActive" name="berita_status" value="1" <?php echo isset($data->berita_status)? (($data->berita_status == 1)? 'checked' : '') : 'checked' ?>>
                            <label for="radioActive">Publish </label>
                        </div>
                        <div class="icheck-danger d-inline mr-3">
                            <input type="radio" id="radioFailed" name="berita_status" value="0" <?php echo isset($data->berita_status)? (($data->berita_status == 0)? 'checked' : '') : '' ?>>
                            <label for="radioFailed">Closed</label>
                        </div>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label class="col-sm-2 control-label col-form-label">Dokumen</label>
                    <div class="col-sm-10 mt-2">
                        <table class="table table-striped table-sm text-sm berita_dokumen">
                            @if($is_edit)
                                @foreach($data->getDokumen as $r)
                                    <tr class="pld_{{$r->berita_dokumen_id}}">
                                        <td>{{ $r->file_name }}</td>
                                        <td class="align-top">
                                            <a class="pop_preview" href="#" data-src="{{ asset($r->file_url) }}" data-type="{{ $r->file_ext }}"><em>preview</em> {!! ($r->file_ext == 'pdf')? '<span class="badge bg-fuchsia">pdf</span>' : '<span class="badge bg-purple">image</span>' !!} </a>
                                        </td>
                                        <td>
                                            <a href="#" data-block="body" data-url="{{ url($page->url.'/'.$r->berita_dokumen_id.'/delete') }}" class="ajax_modal_confirm btn btn-sm btn-danger tooltips text-light" data-placement="left" data-original-title="Hapus Data" ><i class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr>
                                <td class="align-top">
                                    <div class="form-control-sm custom-file">
                                        <input  type="file" class="form-control-sm custom-file-input" data-target="0" id="berita_doc_0" name="berita_dokumen[0]" data-rule-filesize="1" data-rule-accept="application/pdf,image/png,image/jpg" accept="application/pdf,image/png,image/jpg"/>
                                        <label class="form-control-sm custom-file-label file_label_0" for="berita_doc_0">Choose file</label>
                                    </div>
                                </td>
                                <td class="align-top"><input type="text" class="form-control form-control-sm file_name file_name_0" name="file_name[0]" value=""></td>
                                <td class="align-top"><button type="button" class="btn btn-sm btn-success" onclick="addRow(this)"><i class="fa fa-plus"></i></button></td>
                            </tr>
                        </table>
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
    function addRow(th){
        it++;
        let n = $(th).closest('tr').clone(true).html(function(i, t){
            return t.replace('btn-success', 'btn-warning').replace('fa-plus', 'fa-trash').replace('addRow(this)','delRow(this)').replaceAll('0', it);
        });
        n.find('span').remove();
        n.find('label.custom-file-label').html('');

        $('.berita_dokumen').append(n);
        bsCustomFileInput.init();

        loadFile();
    }

    function delRow(th) {
        $(th).closest('tr').remove();
    }

    var it = 1;
    var loadFile = function(event) {
        $('input.custom-file-input').on('change', function(){
            $('.file_name_'+$(this).data('target')).val($('.file_label_'+$(this).data('target')).text());
        });
    };

    $(document).ready(function () {
        unblockUI();
        bsCustomFileInput.init();

        $('.select2_combobox').select2();

        @if($is_edit)
            $('#prodi_id').val('{{ $data->prodi_id }}').trigger('change');
            $('#kategori_id').val('{{ $data->kategori_id }}').trigger('change');
        @endif

        loadFile();

        $('#berita_isi').summernote({
            tabsize: 2,
            height: 200,
            dialogsInBody: true,
            codeviewFilter: true,
            codeviewIframeFilter: true,
            popover: {
                air: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']],
                ]
            }
        });

        $("#form-master").validate({
            rules: {
                kategori_id: {required: true},
                berita_judul: {required: true, maxlength: 255},
                berita_isi: {required: true},
                berita_status: {required: true},
            },
            submitHandler: function (form) {
                if ($('#berita_isi').summernote('isEmpty')) {
                    setFormMessage('.form-message', {stat: false, msg: 'Deskripsi harus diisi', msgField:{'berita_isi': 'Deskripsi harus diisi'}});
                    return false;
                }

                $('.form-message').html('');
                blockUI(form);
                $(form).ajaxSubmit({
                    dataType: 'json',
                    success: function (data) {
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

        $('.pop_preview').on('click', function() {
            if($(this).data('type') == 'pdf') {
                $('#preview_doc').html('<object data="'+$(this).data('src')+'" type="application/pdf" width="100%" height="580px" /><p><a href="'+$(this).data('src')+'">Download</a> berkas pdf.</p> </object>');
            }else{
                $('#preview_doc').html('<img src="'+$(this).data('src')+'" class="img-fluid" />');
            }
            $('#preview_modal').modal('show');
        });
    });
</script>

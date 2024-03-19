<form method="post" action="{{ $url }}" role="form" class="form-horizontal" id="form-confirm">
    @csrf
    {!! method_field($action) !!}

    <div id="modal-confirm" class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ $title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="mb-0 form-message text-center"></div>
                <div class="alert alert-warning mb-0 rounded-0">
                    {{ $desc }}
                    <section class="landing">
                        <div class="container">
                            <dl class="row mb-0">
                                @foreach ($info as $k => $v)
                                    <dt class="col-sm-3 text-right"><strong>{{ $k }}:</strong></dt>
                                    <dd class="col-sm-9 mb-0">{{ $v }}</dd>
                                @endforeach
                                <dt class="col-sm-3 text-right"><strong>Anggota:</strong></dt>
                                <dd class="col-sm-9 mb-0">
                                    <table>
                                        @foreach ($otherData->anggotas as $key => $a)
                                            <tr>
                                                <td>{{ $key + 1 }}.</td>
                                                <td>{{ $a->mahasiswa->nim }} - </td>
                                                <td>{{ $a->mahasiswa->nama_mahasiswa }}@if ($a->magang_tipe == 0)
                                                        <span class="badge badge-pill badge-primary">Ketua</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </dd>
                            </dl>
                        </div>
                    </section>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Keluar</button>
                <button type="submit" class="btn btn-primary">{{ $btnAction }}</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {

        $("#form-confirm").submit(function() {
            $('.form-message').html('');
            let blc = '#modal-confirm';
            blockUI(blc);
            $(this).ajaxSubmit({
                dataType: 'json',
                success: function(data) {
                    refreshToken(data);
                    unblockUI(blc);
                    setFormMessage('.form-message', data);
                    if (data.stat) {
                        if (typeof dataDetail != 'undefined') dataDetail.draw();
                        if (typeof dataMaster != 'undefined') dataMaster.draw(false);
                        if (typeof tableFile != 'undefined') tableFile.draw();
                        if (typeof tableQuiz != 'undefined') tableQuiz.draw();
                        if (typeof tableAssignment != 'undefined') tableAssignment.draw();
                    }
                    closeModal($modal, data);
                }
            });
            return false;
        });
    });
</script>

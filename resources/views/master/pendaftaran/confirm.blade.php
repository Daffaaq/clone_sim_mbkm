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
                        </dl>
                    </div>
                </section>
            </div>
        </div>
        <div class="modal-footer">
            <form method="post" action="{{ $url }}" role="form" class="form-horizontal" id="form-tolak">
                @csrf
                {!! method_field($action) !!}
                <input type="hidden" name="act" value="0" />
                <button type="submit" class="btn btn-danger">Tolak</button>
            </form>
            <form method="post" action="{{ $url }}" role="form" class="form-horizontal" id="form-terima">
                @csrf
                {!! method_field($action) !!}
                <input type="hidden" name="act" value="1" />
                <button type="submit" class="btn btn-success">Terima</button>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        $("#form-tolak").submit(function() {
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

        $("#form-terima").submit(function() {
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

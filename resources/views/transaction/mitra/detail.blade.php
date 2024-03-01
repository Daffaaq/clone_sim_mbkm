<div id="modal-kuota" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body p-0">
            <div class="form-message text-center"></div>
            <table class="table table-sm mb-0">
                @foreach ($datas as $data)
                    <tr>
                        <th class="w-25 text-right">{{ $data->title }}</th>
                        <th class="w-1">:</th>
                        <td class="w-74 @if ($data->bold) font-weight-bold @endif">
                            {{-- //if $data->value has a href --}}
                            @if (strpos($data->value, 'href') !== false)
                                {!! $data->value !!}
                            @elseif (strpos($data->value, 'img') !== false)
                                {!! $data->value !!}
                                {{-- else if includes <br /> --}}
                            @elseif (strpos($data->value, '<br />') !== false)
                                {!! $data->value !!}
                            @else
                                @if ($data->color)
                                    <span class="badge badge-{{ $data->color }}">{{ $data->value }}</span>
                                @else
                                    {{ $data->value }}
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
                @if ($mitra->status == 1)
                    <tr>
                        <th class="w-25 text-right">Kuota</th>
                        <th class="w-1">:</th>
                        <td class="w-74">
                            @foreach ($prodis as $prodi)
                                <form method="post" action="{{ $url }}" role="form" class="form-horizontal"
                                    id="form-kuota-{{ $prodi->prodi_id }}">
                                    @csrf
                                    {!! method_field($action) !!}
                                    Set kuota untuk prodi {{ $prodi->prodi_name }}
                                    <div class="d-flex justify-content-start align-items-center">
                                        <input type="number" class="w-50 form-control form-control-sm" id="kuota"
                                            name="kuota"
                                            value="{{ isset($prodi->kuota->kuota) ? $prodi->kuota->kuota : '' }}" />
                                        <input type="hidden" name="prodi_id" value="{{ $prodi->prodi_id }}" />
                                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                    </div>
                                </form>
                            @endforeach
                        </td>
                    </tr>
                @endif
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-warning">Keluar</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // unblockUI();

        @foreach ($prodis as $prodi)
            $("#form-kuota-{{ $prodi->prodi_id }}").submit(function(e) {
                $('.form-message').html('');
                let blc = '#modal-kuota';
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
                            if (typeof tableAssignment != 'undefined') tableAssignment
                                .draw();
                        }
                        // closeModal($modal, data);
                    }
                });
                return false;
            });
        @endforeach
    });
</script>

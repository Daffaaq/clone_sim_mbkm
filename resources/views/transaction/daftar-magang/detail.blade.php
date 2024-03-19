<form method="post" action="{{ $url }}" role="form" class="form-horizontal" id="form-kuota"
    enctype="multipart/form-data">
    @csrf
    {!! method_field($action) !!}
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
                    <tr>
                        <th class="w-25 text-right">Peran</th>
                        <th class="w-1">:</th>
                        <td class="w-74">
                            <select data-testid="partner-category" class="form-control form-control-sm"
                                id="tipe_pendaftar" name="tipe_pendaftar">
                                <option value="2">Individu</option>
                                <option value="0">Kelompok</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <table class="table table-sm mb-0" id="mahasiswa_select">
                    <tr>
                        <th class="w-25 text-right">Mahasiswa 1</th>
                        <th class="w-1">:</th>
                        <td class="w-74">
                            <select data-testid="partner-category"
                                class="form-control select2_combobox form-control-sm mahasiswa-1" id="mahasiswa_1"
                                name="mahasiswa[]" readonly>
                                <option value="" selected>Pilih Mahasiswa 1</option>
                                @foreach ($mahasiswas as $item)
                                    <option value="{{ $item->mahasiswa_id }}"
                                        {{ $item->mahasiswa_id == $mahasiswa_id ? 'selected' : '' }}>
                                        {{ $item->nim }} -
                                        {{ $item->nama_mahasiswa }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25 text-right">Mahasiswa 2</th>
                        <th class="w-1">:</th>
                        <td class="w-74">
                            <select data-testid="partner-category2"
                                class="form-control select2_combobox form-control-sm mahasiswa-2" id="mahasiswa_2"
                                name="mahasiswa[]">
                                <option value="" selected>Pilih Mahasiswa 2</option>
                                @foreach ($mahasiswas as $item)
                                    <option value="{{ $item->mahasiswa_id }}">
                                        {{ $item->nim }} -
                                        {{ $item->nama_mahasiswa }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25 text-right">Mahasiswa 3</th>
                        <th class="w-1">:</th>
                        <td class="w-74">
                            <select data-testid="partner-category"
                                class="form-control select2_combobox form-control-sm mahasiswa-3" id="mahasiswa_3"
                                name="mahasiswa[]">
                                <option value="" selected>Pilih Mahasiswa 3</option>
                                @foreach ($mahasiswas as $item)
                                    <option value="{{ $item->mahasiswa_id }}">
                                        {{ $item->nim }} -
                                        {{ $item->nama_mahasiswa }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                </table>
                <table class="table table-sm mb-0">
                    <tr>
                        <th class="w-25 text-right">Skema</th>
                        <th class="w-1">:</th>
                        <td class="w-74">
                            <select data-testid="partner-category" class="form-control form-control-sm"
                                id="magang_skema" name="magang_skema">
                                <option value="" disabled selected>Pilih Skema</option>
                                @foreach ($mitra->skema as $s)
                                    <option value="{{ $s }}">{{ $s }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    @if ($mitra->kegiatan->is_submit_proposal == 1)
                        <tr>
                            <th class="w-25 text-right">Proposal</th>
                            <th class="w-1">:</th>
                            <td class="w-74">
                                <div class="form-control-sm custom-file">
                                    <input type="file" class="form-control-sm custom-file-input" data-target="0"
                                        id="berita_doc_0" name="proposal" data-rule-filesize="1"
                                        data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                        accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" />
                                    <label class="form-control-sm custom-file-label file_label_0"
                                        for="berita_doc_0">Choose
                                        file</label>
                                </div>
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"
                    @if ($disabled) disabled @endif>Daftar</button>
                <button type="button" data-dismiss="modal" class="btn btn-warning">Keluar</button>
            </div>
        </div>
    </div>
</form>

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
        // unblockUI();
        // bsCustomFileInput.init();

        loadFile()

        $('.select2_combobox').select2();

        $('#mahasiswa_select').hide();

        $('#tipe_pendaftar').on('change', function() {
            var value = $(this).val()
            console.log(value);
            if (value == 0) {
                $('#mahasiswa_select').show();
            } else {
                $('#mahasiswa_select').hide();
            }
        })

        $("#form-kuota").submit(function() {
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
                        if (typeof tableAssignment != 'undefined') tableAssignment.draw();
                    }
                    closeModal($modal, data);
                }
            });
            return false;
        });
    });
</script>

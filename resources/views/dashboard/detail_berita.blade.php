<div id="modal-master" class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Detail Berita</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body p-0">
            <table class="table table-striped table-sm">
                <tr>
                    <th class="text-right">Prodi : </th>
                    <td>{{ $data->prodi ?? '~ Semua Prodi dalam Jurusan ~' }}</td>
                    <th class="text-right">Oleh : </th>
                    <td>{{ $data->created_by }}</td>
                </tr>
                <tr>
                    <th class="text-right">Kategori : </th>
                    <td>{{ $data->kategori_name ?? '-' }}</td>
                    <th class="text-right">Tanggal : </th>
                    <td>{{ $data->tanggal }}</td>
                </tr>
                <tr>
                    <th colspan="4" class="text-center">
                        <h4>{{ $data->berita_judul }}</h4>
                    </th>
                </tr>
                <tr>
                    <td colspan="4" class="text-justify p-3 p-md-4">{!! $data->berita_isi !!}</td>
                </tr>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-warning">Keluar</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        unblockUI();
    });
</script>

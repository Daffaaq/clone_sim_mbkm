<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body p-0">
            <table class="table table-sm mb-0">
                <tr>
                    <th class="w-25 text-right">Tanggal</th>
                    <th class="w-1">:</th>
                    <td class="w-74">{{ $data->tanggal }}</td>
                </tr>
                <tr>
                    <th class="w-25 text-right">Jam Mulai</th>
                    <th class="w-1">:</th>
                    <td class="w-74">{{ $data->jam_mulai }}</td>
                </tr>
                <tr>
                    <th class="w-25 text-right">Jam Selesai</th>
                    <th class="w-1">:</th>
                    <td class="w-74">{{ $data->jam_selesai }}</td>
                </tr>
                <tr>
                    <th class="w-25 text-right">Status Dosen Pembimbing</th>
                    <th class="w-1">:</th>
                    <td class="w-74">
                        @if ($data->status1 == 0)
                            <span class="badge badge-warning">Menunggu</span>
                        @elseif ($data->status1 == 1)
                            <span class="badge badge-success">Menerima</span>
                        @elseif ($data->status1 == 2)
                            <span class="badge badge-danger">Menolak</span>
                        @else
                            {{ $data->status1 }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="w-25 text-right">Status Pembimbing Lapangan</th>
                    <th class="w-1">:</th>
                    <td class="w-74">
                        @if ($data->status2 == 0)
                            <span class="badge badge-warning">Menunggu</span>
                        @elseif ($data->status2 == 1)
                            <span class="badge badge-success">Menerima</span>
                        @elseif ($data->status2 == 2)
                            <span class="badge badge-danger">Menolak</span>
                        @else
                            {{ $data->status2 }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="w-25 text-right">Topik Bimbingan</th>
                    <th class="w-1">:</th>
                    <td class="w-74" style="word-wrap: break-word;">{!! $data->topik_bimbingan !!}</td>
                </tr>
                <tr>
                    <th class="w-25 text-right">Foto</th>
                    <th class="w-1">:</th>
                    <td class="w-74">
                        @if ($data->foto)
                            <img src="{{ asset('storage/assets/logbimbingan/' . $data->foto) }}" alt="Foto Bimbingan"
                                style="max-width: 450px;">
                        @else
                            -
                        @endif

                    </td>
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

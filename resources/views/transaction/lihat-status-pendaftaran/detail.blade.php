<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body p-0">
            <table class="table table-sm mb-0">
                @foreach ($datas as $data)
                    <tr>
                        <th class="w-25 text-right">{{ $data->title }}</th>
                        <th class="w-1">:</th>
                        <td class="w-74 @if ($data->bold) font-weight-bold @endif">
                            @if (!$data->link)
                                {{ $data->value }}
                            @else
                                <a target="_blank"
                                    href="{{ asset('assets/proposal/' . $data->value) }}">{{ $data->value }}</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                @if ($anggota)
                    <tr>
                        <th class="w-25 text-right">Anggota</th>
                        <th class="w-1">:</th>
                        <td class="w-74 @if ($data->bold) font-weight-bold @endif">
                            <ul class="list-group">
                                @foreach ($anggota as $d)
                                    @php
                                        switch ($d->is_accept) {
                                            case 0:
                                                $status = '<span class="ml-2 badge badge-warning">Menunggu</span>';
                                                break;
                                            case 1:
                                                $status = '<span class="ml-2 badge badge-success">Menerima</span>';
                                                break;
                                            case 2:
                                                $status =
                                                    '<span class="ml-2 accent-bluebadge badge-danger">Menolak</span>';
                                                break;
                                            default:
                                                $status = '-';
                                                break;
                                        }
                                    @endphp
                                    <li class="list-group-item border-0 p-0">
                                        {{ $d->mahasiswa->nama_mahasiswa }} ({{ $d->mahasiswa->nim }})
                                        {!! $status !!}
                                    </li>
                                @endforeach
                            </ul>
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
        unblockUI();
    });
</script>

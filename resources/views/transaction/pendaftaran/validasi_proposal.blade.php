<div id="modal-master" class="modal-dialog modal-xl" role="document" style="max-width: 100%">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body p-0">
            <div class="form-message text-center"></div>
            <div class="row">
                <div class="col-7">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th class="w-15 text-right">Magang ID</th>
                                <th class="w-1">:</th>
                                <td class="w-84">{{ $magang->magang_kode }}</td>
                            </tr>
                            <tr>
                                <th class="w-15 text-right">Nama Kegiatan</th>
                                <th class="w-1">:</th>
                                <td class="w-84">{{ $magang->mitra->kegiatan->kegiatan_nama }}</td>
                            </tr>
                            <tr>
                                <th class="w-15 text-right">Nama Mitra</th>
                                <th class="w-1">:</th>
                                <td class="w-84">
                                    <i class="far fa-building text-md text-primary"></i>
                                    {{ $magang->mitra->mitra_nama }}
                                </td>
                            </tr>
                            <tr>
                                <th class="w-15 text-right">Periode</th>
                                <th class="w-1">:</th>
                                <td class="w-84">{{ $magang->periode->periode_nama }}</td>
                            </tr>
                            <tr>
                                <th class="w-15 text-right">Durasi</th>
                                <th class="w-1">:</th>
                                <td class="w-84">
                                    <i class="far fa-clock text-md text-primary"></i>
                                    {{ $magang->mitra->mitra_durasi }}
                                    bulan
                                </td>
                            </tr>
                            <tr>
                                <th class="w-15 text-right">Skema</th>
                                <th class="w-1">:</th>
                                <td class="w-84">{{ $magang->magang_skema }}</td>
                            </tr>
                            <tr>
                                <th class="w-15 text-right">Tanggal Pendaftaran</th>
                                <th class="w-1">:</th>
                                <td class="w-84">
                                    <i class="far fa-calendar-alt text-md text-primary"></i>
                                    {{ \Carbon\Carbon::parse($magang->mitra->mitra_batas_pendaftaran)->format('d M Y') }}
                                </td>
                            </tr>
                            <tr>
                                <th class="w-15 text-right">Anggota</th>
                                <th class="w-1">:</th>
                                <td class="w-84">
                                    <table class="table table-sm text-sm table-bordered"
                                        style="table-layout:fixed;width:100%;" id="table-mhs">
                                        <thead>
                                            <tr>
                                                <th style="width: 14%">No</th>
                                                <th style="width: 22%">NIM</th>
                                                <th style="width: 45%">Nama Mahasiswa</th>
                                                <th style="width: 14%">Kelas</th>
                                                <th style="width: 14%">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($anggotas as $key => $a)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $a->mahasiswa->nim }}</td>
                                                    <td>{{ $a->mahasiswa->nama_mahasiswa }}@if ($a->magang_tipe == 0)
                                                            <span class="badge badge-pill badge-primary">Ketua</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $a->mahasiswa->kelas }}</td>
                                                    <td>
                                                        @if ($a->magang_tipe == 1)
                                                            @if ($a->is_accept == 0)
                                                                <span class="badge badge badge-warning">Menunggu</span>
                                                            @elseif ($a->is_accept == 1)
                                                                <span class="badge badge badge-success">Menerima</span>
                                                            @elseif ($a->is_accept == 2)
                                                                <span class="badge badge badge-danger">Menolak</span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <th class="w-15 text-right">Status Proposal</th>
                                <th class="w-1">:</th>
                                <td class="w-84">
                                    <div class="form-group required row mb-2">
                                        <div class="col-sm-10">
                                            <div class="icheck-success d-inline mr-3">
                                                <input type="radio" id="radioActive" name="status" value="3">
                                                <label for="radioActive">Diterima </label>
                                            </div>
                                            <div class="icheck-danger d-inline mr-3">
                                                <input type="radio" id="radioFailed" name="status" value="2">
                                                <label for="radioFailed">Ditolak</label>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- input --}}
                                    <div class="form-group required row mb-2">
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control form-control-sm"
                                                id="dokumen_magang_keterangan" name="dokumen_magang_keterangan"
                                                placeholder="Keterangan">
                                            <small class="text-muted">Catatan untuk Laporan Proposal</small><br />
                                            <small class="text-muted">** Mohon Bapak/Ibu koordinator agar melakukan
                                                <b>validasi(pengecekan)</b> terhadap proposal yang disubmit oleh
                                                mahasiswa yang telah ditandatangani secara lengkap dan berstempel
                                                jurusan</small>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                {{-- simpan buttn --}}
                                <th class="border-0"></th>
                                <th class="border-0"></th>
                                <td colspan="3" class="border-0">
                                    <button type="button" class="btn btn-primary" id="simpan"
                                        data-idproposal="{{ $magang->proposal->dokumen_magang_id }}">Simpan</button>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-5">
                    <embed src="{{ asset('assets/proposal/' . $magang->proposal->dokumen_magang_file) }}"
                        type="application/pdf" width="100%" height="600px" />
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-warning">Keluar</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        unblockUI();

        $("#simpan").click(function(e) {
            $('.form-message').html('');
            let blc = '#modal-master';
            blockUI(blc);
            $.ajax({
                url: "{{ url('transaksi/pendaftaran/confirm_proposal') }}",
                dataType: 'json',
                type: 'post',
                data: {
                    id: $(this).data('idproposal'),
                    status: $('input[name="status"]:checked').val(),
                    keterangan: $('#dokumen_magang_keterangan').val()
                },
                success: function(data) {
                    unblockUI(blc);
                    setFormMessage('.form-message', data);
                    if (data.stat) {
                        dataMaster.draw(false);
                    } else {
                        dataMaster.draw(false);
                    }
                    closeModal($modal, data);
                }
            });
            return false;
        });

        $("#acc-dok-sb").click(function(e) {
            $('.form-message').html('');
            let blc = '#modal-master';
            blockUI(blc);
            $.ajax({
                url: "{{ url('transaksi/pendaftaran/confirm_sb') }}",
                dataType: 'json',
                type: 'post',
                data: {
                    id: $(this).data('idproposal'),
                    status: 1
                },
                success: function(data) {
                    unblockUI(blc);
                    setFormMessage('.form-message', data);
                    if (data.stat) {
                        dataMaster.draw(false);
                    }
                    closeModal($modal, data);
                }
            });
            return false;
        });

        $("#tolak-dok-p").click(function(e) {
            $('.form-message').html('');
            let blc = '#modal-master';
            blockUI(blc);
            $.ajax({
                url: "{{ url('transaksi/pendaftaran/confirm_proposal') }}",
                dataType: 'json',
                type: 'post',
                data: {
                    id: $(this).data('idproposal'),
                    status: 2
                },
                success: function(data) {
                    unblockUI(blc);
                    setFormMessage('.form-message', data);
                    dataMaster.draw(false);
                    closeModal($modal, data);
                }
            });
            return false;
        });

        $("#tolak-dok-sb").click(function(e) {
            $('.form-message').html('');
            let blc = '#modal-master';
            blockUI(blc);
            $.ajax({
                url: "{{ url('transaksi/pendaftaran/confirm_sb') }}",
                dataType: 'json',
                type: 'post',
                data: {
                    id: $(this).data('idproposal'),
                    status: 2
                },
                success: function(data) {
                    unblockUI(blc);
                    setFormMessage('.form-message', data);
                    if (data.stat) {
                        dataMaster.draw(false);
                    }
                    closeModal($modal, data);
                }
            });
            return false;
        });
    });
</script>

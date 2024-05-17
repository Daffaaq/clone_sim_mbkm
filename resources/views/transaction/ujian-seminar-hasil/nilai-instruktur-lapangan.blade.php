<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body p-0">
            <div class="header">
                <div class="header-container" style="display: flex;">
                    <h5 style="margin-left: 5px;">EVALUASI AKHIR SEMESTER Instruktur Lapangan</h5>
                    <a href="{{ url('transaksi/ujian-seminar-hasil/' . $encryption . '/cetak-nilai-instruktur') }}"
                        class="print-button" style="margin-left: 280px;" target="_blank">
                        <i class="fas fa-print"></i>
                    </a>
                </div>
                <br>
                <h5 style="text-align: center">FORM PENILAIAN DISEMINASI HASIL KEGIATAN MBKM</h5>
                <h5 style="text-align: center">Instruktur Lapangan</h5>
            </div>
            <div class="upper" style="margin-left: 5px">
                <table border="0" cellpadding="1" class="tbl-no">
                    <tbody>
                        <tr>
                            <td width="200"><span style="font-size: 16px;">Nama Mahasiswa</span></td>
                            <td width="8"style="padding-left: 5px;><span style="font-size: 16px;">:</span></td>
                                <td width="200"><span style="font-size: 16px;">{{ $data->magang->mahasiswa->nama_mahasiswa }}</span> </td>
                            </tr>
                            <tr>
                                <td><span style="font-size: 16px;">NIM</span></td>
                                <td width="8"style=" padding-left: 5px;><span style="font-size: 16px;">:</span>
                            </td>
                            <td><span style="font-size: 16px;">{{ $data->magang->mahasiswa->nim }}</span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size: 16px;">Program Studi</span></td>
                            <td width="8"style="padding-left: 5px;><span style="font-size: 16px;">:</span></td>
                            <td><span style="font-size: 16px; ">{{ $data->magang->mahasiswa->prodi->prodi_name }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><span style="font-size: 16px;">Jenis</span></td>
                            <td width="8"style=" padding-left: 5px;><span style="font-size: 16px;">:</span></td>
                            <td><span
                                    style="font-size: 16px; ">{{ $data->magang->mitra->kegiatan->kegiatan_nama }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><span style="font-size: 16px;">Mitra Kegiatan</span></td>
                            <td width="8"style=" padding-left: 5px;><span style="font-size: 16px;">:</span>
                            </td>
                            <td><span style="font-size: 16px; ">{{ $data->magang->mitra->mitra_nama }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><span style="font-size: 16px;">Durasi Kegiatan</span></td>
                            <td width="8"style=" padding-left: 5px;><span style="font-size: 16px;">:</span>
                            </td>
                            <td><span style="font-size: 16px; ">{{ $data->magang->mitra->mitra_durasi }}</span>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
            <input type="hidden" name="periode_id" value="{{ $activePeriods }}">
            <input type="hidden" name="semhas_daftar_id" value="{{ $semhas_daftar_id }}">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Kriteria Penilaian</th>
                        <th scope="col">Nilai (1-100)</th>
                        <th scope="col">Bobot Nilai</th>
                        <th scope="col">Nilai x Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kriteriaNilai as $nilai)
                        @php
                            // Cari nilai yang sesuai dari koleksi $datanilai
                            $nilaiModel = $datanilai->firstWhere(
                                'nilai_instruktur_lapangan_id',
                                $nilai->nilai_instruktur_lapangan_id,
                            );
                            // dd($nilaiModel);
                            $nilaiValue = $nilaiModel ? $nilaiModel->nilai : '';
                        @endphp
                        <tr>
                            <td>{{ $nilai->name_kriteria_instruktur_lapangan }}</td>
                            <td>
                                <input type="number" id="nilai_{{ $nilai->id }}" name="nilai[{{ $nilai->id }}]"
                                    class="form-control form-control-sm nilai-subkriteria" value="{{ $nilaiValue }}"
                                    {{ $nilai->subKriteria->isEmpty() ? 'readonly' : 'readonly' }} required>
                                <input type="hidden" name="nilai_instruktur_lapangan_id[{{ $nilai->id }}]"
                                    value="{{ $nilai->nilai_instruktur_lapangan_id }}">
                            </td>
                            <td>{{ $nilai->bobot }}</td>
                            <td id="nilai_x_bobot_{{ $nilai->id }}"></td>
                        </tr>
                    @endforeach
                    <tr class="total-nilai-row">
                        <td colspan="3" class="total-nilai" style="text-align: center;">Total Nilai</td>
                        <td class="total-nilai1"></td>
                    </tr>
                </tbody>
            </table>
            <table>
                <tr>
                    <td>
                        <div class="form-group" style="display: flex;align-items: center;margin-bottom: 10px;">
                            <label for="saran_instruktur_lapangan"
                                style="flex: 0 0 200px; margin-right: 10px; text-align: right;">Saran
                                Pembimbing Dosen</label>
                            <textarea class="form-control" id="saran_instruktur_lapangan" name="saran_instruktur_lapangan" rows="3"
                                style="flex: 1; width: 590px;" value="" readonly>{{ isset($existingNilai->saran_instruktur_lapangan) ? $existingNilai->saran_instruktur_lapangan : '' }}</textarea>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-group" style="display: flex;align-items: center;margin-bottom: 10px;">
                            <label for="catatan_instruktur_lapangan"
                                style="flex: 0 0 200px; margin-right: 10px; text-align: right;">Catatan
                                Pembimbing Dosen</label>
                            <textarea class="form-control" id="catatan_instruktur_lapangan" name="catatan_instruktur_lapangan" rows="3"
                                style=" flex: 1; width: 590px" value="" readonly>{{ isset($existingNilai->catatan_instruktur_lapangan) ? $existingNilai->catatan_instruktur_lapangan : '' }}</textarea>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-warning">Keluar</button>
            <a href="{{ url('transaksi/ujian-seminar-hasil/' . $encryption . '/cetak-nilai-nilai-instruktur') }}"
                class="btn btn-primary" target="_blank">Cetak</a>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var nilaiInputs = document.querySelectorAll('.nilai-subkriteria');
        var average = 0;
        var form = document.getElementById('form-nilai');
        var inputsValid = true;

        function hitungNilai() {
            var totalNilai = 0;

            nilaiInputs.forEach(function(input) {
                var nilai = parseFloat(input.value);
                var bobotCell = input.closest('tr').querySelector('td:nth-child(3)');
                var bobot = parseFloat(bobotCell.textContent);
                var nilaiXBobotCell = input.closest('tr').querySelector('td:nth-child(4)');

                if (!isNaN(nilai) && !isNaN(bobot) && nilaiXBobotCell) {
                    var nilaiXBobot = nilai * bobot;
                    nilaiXBobotCell.textContent = nilaiXBobot;
                }

                // Hitung total nilai
                var nilaiXBobot = parseFloat(nilaiXBobotCell.textContent);
                if (!isNaN(nilaiXBobot)) {
                    totalNilai += nilaiXBobot;
                }
            });

            // Tampilkan total nilai
            document.querySelector('.total-nilai1').textContent = totalNilai;
        }

        // Hitung nilai saat halaman dimuat
        hitungNilai();
    });
</script>

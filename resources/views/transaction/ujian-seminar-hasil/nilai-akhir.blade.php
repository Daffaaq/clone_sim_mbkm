<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body p-0">
            <table class="table">
                <thead>
                    <th scope="col">Nilai Pembimbing</th>
                    <th scope="col">Nilai Pembahas</th>
                    <th scope="col">Nilai dari Mitra</th>
                    <th scope="col">Nilai Akhir</th>
                </thead>
                <tbody>
                    <td class="total-nilai1-pembimbing"></td>
                    <td class="total-nilai1-pembahas"></td>
                    <td class="total-nilai1-intruktur"></td>
                    <td class="total-nilai-akhir"></td>
                </tbody>
            </table>
            {{-- <div class="header">
                <h5 style="align-content: flex-start">EVALUASI AKHIR SEMESTER DOSEN PEMBIMBING</h5>
                <br>
                <h5 style="text-align: center">FORM PENILAIAN DISEMINASI HASIL KEGIATAN MBKM</h5>
                <h5 style="text-align: center">DOSEN PEMBIMBING</h5>
            </div>
            <div class="upper">
                <table border="0" cellpadding="1" class="tbl-no">
                    <tbody>
                        <tr>
                            <td width="93"><span style="font-size: 16px;">Nama Mahasiswa</span></td>
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
            </div> --}}
            <input type="hidden" name="periode_id" value="{{ $activePeriods }}">
            <input type="hidden" name="semhas_daftar_id" value="{{ $semhas_daftar_id }}">
            <table class="table-instruktur" style="display: none;">
                <thead>
                    <tr>
                        <th scope="col">Kriteria Penilaian</th>
                        <th scope="col">Nilai (1-100)</th>
                        <th scope="col">Bobot Nilai</th>
                        <th scope="col">Nilai x Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kriteriaNilaiintruktur as $nilai)
                        @php
                            // Cari nilai yang sesuai dari koleksi $datanilai
                            $nilaiModel = $datanilaiinstruktur->firstWhere(
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
            <table class="table-pembahas" style="display: none;">
                <thead>
                    <tr>
                        <th scope="col">Kriteria Penilaian</th>
                        <th scope="col">Nilai (1-100)</th>
                        <th scope="col">Bobot Nilai</th>
                        <th scope="col">Nilai x Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kriteriaNilaiPembahas as $nilai)
                        @php
                            // Cari nilai yang sesuai dari koleksi $datanilai
                            $nilaiModel = $datanilaiPembahas->firstWhere(
                                'nilai_pembahas_dosen_id',
                                $nilai->nilai_pembahas_dosen_id,
                            );
                            // dd($nilaiModel);
                            $nilaiValue = $nilaiModel ? $nilaiModel->nilai : '';
                        @endphp
                        <tr>
                            <td>{{ $nilai->name_kriteria_pembahas_dosen }}</td>
                            <td>
                                <input type="number" id="nilai_{{ $nilai->id }}" name="nilai[{{ $nilai->id }}]"
                                    class="form-control form-control-sm nilai-subkriteria" value="{{ $nilaiValue }}"
                                    {{ $nilai->subKriteria->isEmpty() ? 'readonly' : 'readonly' }} required>
                                <input type="hidden" name="nilai_pembahas_dosen_id[{{ $nilai->id }}]"
                                    value="{{ $nilai->nilai_pembahas_dosen_id }}">
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
            <table class="table-pembimbing" style="display: none;">
                <thead>
                    <tr>
                        <th scope="col">Kriteria Penilaian</th>
                        <th scope="col">Nilai (1-100)</th>
                        <th scope="col">Bobot Nilai</th>
                        <th scope="col">Nilai x Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kriteriaNilaiPembimbing as $nilai)
                        @php
                            // Cari nilai yang sesuai dari koleksi $datanilai
                            $nilaiModel = $datanilaiPembimbing->firstWhere(
                                'nilai_pembimbing_dosen_id',
                                $nilai->nilai_pembimbing_dosen_id,
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
                                <input type="hidden" name="nilai_pembimbing_dosen_id[{{ $nilai->id }}]"
                                    value="{{ $nilai->nilai_pembimbing_dosen_id }}">
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
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-warning">Keluar</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {


        var instrukturInputs = document.querySelectorAll('.table-instruktur .nilai-subkriteria');
        var instrukturAverage = 0;

        function hitungNilaiInstruktur() {
            var instrukturTotalNilai = 0;

            instrukturInputs.forEach(function(input) {
                var nilai = parseFloat(input.value);
                var bobotCell = input.closest('tr').querySelector('td:nth-child(3)');
                var bobot = parseFloat(bobotCell.textContent);
                var nilaiXBobotCell = input.closest('tr').querySelector('td:nth-child(4)');

                if (!isNaN(nilai) && !isNaN(bobot) && nilaiXBobotCell) {
                    var nilaiXBobot = nilai * bobot;
                    nilaiXBobotCell.textContent = nilaiXBobot.toFixed(2);
                }

                // Hitung total nilai
                var nilaiXBobot = parseFloat(nilaiXBobotCell.textContent);
                if (!isNaN(nilaiXBobot)) {
                    instrukturTotalNilai += nilaiXBobot;
                }
            });

            // Tampilkan total nilai
            document.querySelector('.total-nilai1-intruktur').textContent = instrukturTotalNilai
                .toFixed(2);
        }

        // Menghitung nilai saat halaman dimuat untuk tabel instruktur lapangan
        hitungNilaiInstruktur();

        var instrukturInputs = document.querySelectorAll('.table-pembimbing .nilai-subkriteria');
        var instrukturAverage = 0;

        function hitungNilaiPembimbing() {
            var instrukturTotalNilai = 0;

            instrukturInputs.forEach(function(input) {
                var nilai = parseFloat(input.value);
                var bobotCell = input.closest('tr').querySelector('td:nth-child(3)');
                var bobot = parseFloat(bobotCell.textContent);
                var nilaiXBobotCell = input.closest('tr').querySelector('td:nth-child(4)');

                if (!isNaN(nilai) && !isNaN(bobot) && nilaiXBobotCell) {
                    var nilaiXBobot = nilai * bobot;
                    nilaiXBobotCell.textContent = nilaiXBobot.toFixed(2);
                }

                // Hitung total nilai
                var nilaiXBobot = parseFloat(nilaiXBobotCell.textContent);
                if (!isNaN(nilaiXBobot)) {
                    instrukturTotalNilai += nilaiXBobot;
                }
            });

            // Tampilkan total nilai
            document.querySelector('.total-nilai1-pembimbing').textContent = instrukturTotalNilai
                .toFixed(2);
        }

        // Menghitung nilai saat halaman dimuat untuk tabel instruktur lapangan
        hitungNilaiPembimbing();

        var pembahasInputs = document.querySelectorAll('.table-pembahas .nilai-subkriteria');
        var pembahasAverage = 0;

        function hitungNilaiPembahas() {
            var pembahasTotalNilai = 0;

            pembahasInputs.forEach(function(input) {
                var nilai = parseFloat(input.value);
                var bobotCell = input.closest('tr').querySelector('td:nth-child(3)');
                var bobot = parseFloat(bobotCell.textContent);
                var nilaiXBobotCell = input.closest('tr').querySelector('td:nth-child(4)');

                if (!isNaN(nilai) && !isNaN(bobot) && nilaiXBobotCell) {
                    var nilaiXBobot = nilai * bobot;
                    nilaiXBobotCell.textContent = nilaiXBobot.toFixed(2);
                }

                // Hitung total nilai
                var nilaiXBobot = parseFloat(nilaiXBobotCell.textContent);
                if (!isNaN(nilaiXBobot)) {
                    pembahasTotalNilai += nilaiXBobot;
                }
            });

            // Tampilkan total nilai
            document.querySelector('.total-nilai1-pembahas').textContent = pembahasTotalNilai.toFixed(2);
        }

        // Menghitung nilai saat halaman dimuat untuk tabel pembahas
        hitungNilaiPembahas();

        var nilaiPembimbing = parseFloat(document.querySelector('.total-nilai1-pembimbing').textContent);
        var nilaiPembahas = parseFloat(document.querySelector('.total-nilai1-pembahas').textContent);
        var nilaiInstruktur = parseFloat(document.querySelector('.total-nilai1-intruktur').textContent);

        // Menghitung nilai akhir berdasarkan persentase
        var nilaiAkhir = (nilaiPembimbing * 0.35) + (nilaiPembahas * 0.15) + (nilaiInstruktur * 0.50);

        // Menampilkan nilai akhir pada elemen yang ditarget
        document.querySelector('.total-nilai-akhir').textContent = nilaiAkhir.toFixed(2);
    });
</script>

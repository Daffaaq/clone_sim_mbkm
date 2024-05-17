<html>

<head>
    <title>Nilai Dosen Pembimbing</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        * {
            font-family: TimesNewRoman, Times New Roman, Times, Baskerville, Georgia, serif;
            line-height: 1.5;
        }

        .mhs th,
        .mhs td {
            padding: 5px;
        }

        .mhs td:first-child,
        .mhs td:last-child {
            text-align: center;
        }

        .main tr:first-child span {
            line-height: 1;
        }

        .tbl-no span {
            line-height: 1;
        }
    </style>
</head>

<body>
    @php
        $img = asset('assets/poltek.jpeg');
        $base_64 = base64_encode($img);
        $img = 'data:image/png;base64,' . $base_64;
    @endphp
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/poltek.jpeg'))) }}"
        style="height: 100px; position: absolute; top:10px; margin-left: 20px" />
    <table align="center" border="0" cellpadding="1" class="main">
        <tbody>
            <tr>
                <td colspan="3">
                    <div align="center">
                        <span style="font-size: 15px;">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,<br />RISET, DAN
                            TEKNOLOGI<br />POLITEKNIK NEGERI MALANG<br /></span>
                        <span style="font-size: 12px;">JL. Soekarno Hatta No.9 Malang 65141<br />Telp (0341) 404424 -
                            404425 Fax (0341) 404420<br />Laman://www.polinema.ac.id</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <hr style="border-top: 4px double black" />
    <div class="header">
        <h5 style="margin-top:0%; margin-left: 5px; font-size: 20px">EVALUASI AKHIR SEMESTER DOSEN PEMBIMBING</h5>
        <h5 style="margin-top: -10px; text-align: center; font-size: 20px">FORM PENILAIAN DISEMINASI HASIL KEGIATAN
            MBKM<br />DOSEN PEMBIMBING</h5>
    </div>
    <div class="upper" style="margin-left: 5px">
        <table border="0" cellpadding="1" class="tbl-no">
            <tbody>
                <tr>
                    <td width="200"><span style="font-size: 16px;">Nama Mahasiswa</span></td>
                    <td width="8" style="padding-left: 5px;"><span style="font-size: 16px;">:</span></td>
                    <td width="200"><span
                            style="font-size: 16px;">{{ $data->magang->mahasiswa->nama_mahasiswa }}</span></td>
                </tr>
                <tr>
                    <td><span style="font-size: 16px;">NIM</span></td>
                    <td width="8" style="padding-left: 5px;"><span style="font-size: 16px;">:</span></td>
                    <td><span style="font-size: 16px;">{{ $data->magang->mahasiswa->nim }}</span></td>
                </tr>
                <tr>
                    <td><span style="font-size: 16px;">Program Studi</span></td>
                    <td width="8" style="padding-left: 5px;"><span style="font-size: 16px;">:</span></td>
                    <td><span style="font-size: 16px;">{{ $data->magang->mahasiswa->prodi->prodi_name }}</span></td>
                </tr>
                <tr>
                    <td><span style="font-size: 16px;">Jenis</span></td>
                    <td width="8" style="padding-left: 5px;"><span style="font-size: 16px;">:</span></td>
                    <td><span style="font-size: 16px;">{{ $data->magang->mitra->kegiatan->kegiatan_nama }}</span></td>
                </tr>
                <tr>
                    <td><span style="font-size: 16px;">Mitra Kegiatan</span></td>
                    <td width="8" style="padding-left: 5px;"><span style="font-size: 16px;">:</span></td>
                    <td><span style="font-size: 16px;">{{ $data->magang->mitra->mitra_nama }}</span></td>
                </tr>
                <tr>
                    <td><span style="font-size: 16px;">Durasi Kegiatan</span></td>
                    <td width="8" style="padding-left: 5px;"><span style="font-size: 16px;">:</span></td>
                    <td><span style="font-size: 16px;">{{ $data->magang->mitra->mitra_durasi }} Bulan</span></td>
                </tr>
            </tbody>
        </table>
    </div>
    <table class="table" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th scope="col" style="width: 420px; border: 1px solid black;">Kriteria Penilaian</th>
                <th scope="col" style="border: 1px solid black;">Nilai (1-100)</th>
                <th scope="col" style="border: 1px solid black;">Bobot Nilai</th>
                <th scope="col" style="border: 1px solid black;">Nilai x Bobot</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($nilaiDetails as $detail)
                <tr>
                    <td style="border-right: 1px solid black; border-left: 1px solid black;">{{ $detail['name'] }}</td>
                    <td style="border-right: 1px solid black; text-align: center;">{{ $detail['nilai'] }}</td>
                    <td style="border-right: 1px solid black; text-align: center;">{{ $detail['bobot'] }}</td>
                    @if ($detail['bobot'] != null)
                        <td style="border-right: 1px solid black; text-align: center;">{{ $detail['nilaiXBobot'] }}
                        </td>
                    @else
                        <td style="border-right: 1px solid black; text-align: center;"></td>
                    @endif
                </tr>
            @endforeach
            <tr>
                <td style="border: 1px solid black;"></td>
            </tr>
            <tr class="total-nilai-row">
                <td colspan="3" class="total-nilai" style="text-align: center; border: 1px solid black;">TOTAL NILAI
                </td>
                <td class="total-nilai1" style="text-align: center; border: 1px solid black;">{{ $totalNilai }}</td>
            </tr>
        </tbody>
    </table>
    <table>
        <tr>
            <td>
                <div class="form-group" style="display: flex; align-items: center; margin-bottom: 10px;">
                    <label for="saran_pembimbing_dosen"
                        style="flex: 0 0 200px; margin-right: 10px; text-align: right;">Saran Pembimbing Dosen</label>
                    <textarea class="form-control" id="saran_pembimbing_dosen" name="saran_pembimbing_dosen" rows="3"
                        style="flex: 1; width: 590px;" readonly>{{ $existingNilai->saran_pembimbing_dosen ?? '' }}</textarea>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="form-group" style="display: flex; align-items: center; margin-bottom: 10px;">
                    <label for="catatan_pembimbing_dosen"
                        style="flex: 0 0 200px; margin-right: 10px; text-align: right;">Catatan Pembimbing Dosen</label>
                    <textarea class="form-control" id="catatan_pembimbing_dosen" name="catatan_pembimbing_dosen" rows="3"
                        style="flex: 1; width: 590px;" readonly>{{ $existingNilai->catatan_pembimbing_dosen ?? '' }}</textarea>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>

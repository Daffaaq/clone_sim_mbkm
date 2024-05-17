<html>

<head>
    <title>Nilai Akhir</title>
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
        <h5 style="margin-top:0%; margin-left: 5px; font-size: 20px">EVALUASI AKHIR SEMESTER DOSEN PEMBAHAS</h5>
        <h5 style="margin-top: -10px; text-align: center; font-size: 20px">FORM PENILAIAN DISEMINASI HASIL KEGIATAN
            MBKM<br />DOSEN PEMBAHAS</h5>
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
   <table class="table" style="border: 1px solid black;">
    <thead class="" style="text-align: center">
        <th scope="col" style="border: 1px solid black;">Nilai Pembimbing</th>
        <th scope="col" style="border: 1px solid black;">Nilai Pembahas</th>
        <th scope="col" style="border: 1px solid black;">Nilai dari Mitra</th>
        <th scope="col" style="border: 1px solid black;">Nilai Akhir</th>
    </thead>
    <tbody class="" style="text-align: center">
        <td class="total-nilai1-pembimbing" style="border: 1px solid black;">{{$totalNilaiPembimbing}}</td>
        <td class="total-nilai1-pembahas" style="border: 1px solid black;">{{$totalNilaiPembahas}}</td>
        <td class="total-nilai1-intruktur" style="border: 1px solid black;">{{$totalNilaiInstruktur}}</td>
        <td class="total-nilai-akhir" style="border: 1px solid black;">{{$nilaiAkhir}}</td>
    </tbody>
</table>
</body>

</html>

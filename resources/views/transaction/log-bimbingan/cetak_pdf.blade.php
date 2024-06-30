<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Log Bimbingan</title>

    <style>
        :root {
            --header-height: 180px;
        }

        * {
            font-family: TimesNewRoman, Times New Roman, Times, Baskerville, Georgia, serif;
            line-height: 1.5;
        }

        body {
            margin: 0;
            padding: var(--header-height) 0 0 0;
        }

        @page {
            margin-top: var(--header-height);
            margin-bottom: 20px;
        }

        .header {
            width: 100%;
            text-align: center;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            background-color: white;
            padding-top: 10px;

            /* Ensure the header is always on top */
        }

        .header img {
            height: 120px;
            position: fixed;
            top: 10px;
        }

        .header hr {
            border-top: 4px double black;
        }

        .main-content {
            margin-top: 165px;
            padding: 0 20px;
        }

        .main-content2 {
            margin-top: calc(var(--header-height) + 20px);
            padding: 0 20px;
            position: relative;
            page-break-after: always;
            break-after: always;
        }

        .tbl-no span,
        .mhs th,
        .mhs td {
            font-size: 16px;
            padding: 5px;
        }

        .mhs th,
        .mhs td {
            border: 1px solid black;
        }

        .mhs td:first-child,
        .mhs td:last-child {
            text-align: center;
        }

        .judul {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
        }

        thead {
            display: table-header-group;

        }


        tr.data {
            page-break-inside: avoid;
            /* Hindari pemisahan baris */
        }

        .datalog {
            page-break-after: auto;
            position: relative;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        @media print {
            @page {
                margin-top: 200px;
                margin-bottom: 20px;
            }

            .header {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1000;
            }

            body {
                margin: 0;
                padding-top: 180px;
                /* Pastikan cukup ruang untuk header */
            }

            .main-content {
                margin-top: 200px;
            }

            .main-content2 {
                margin-top: 20px;
                page-break-inside: avoid;
            }

            .main-content2 td {
                word-break: keep-all;
                /* Mencegah pemisahan kata */
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/poltek.jpeg'))) }}"
            class="app-image-style" />
        <div align="center">
            <span style="font-size: 18px;">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,<br />
                RISET, DAN TEKNOLOGI <br />
                POLITEKNIK NEGERI MALANG <br />
            </span>
            <span style="font-size: 16px;">
                JL. Soekarno Hatta No.9 Malang 65141<br />
                Telp (0341) 404424 - 404425 Fax (0341) 404420<br />
                Laman://www.polinema.ac.id</span>
            <hr />
        </div>
    </div>


    <div class="all">
        <div class="main-content">
            <div class="judul">LOG BOOK AKTIVITAS HARIAN</div>
            <table align="center" border="0" cellpadding="1" class="tbl-no">
                <tbody>
                    <tr>
                        <td width="93">Nama Mahasiswa</td>
                        <td width="8" style="padding-left: 5px;">:</td>
                        <td width="200">{{ $mahasiswa->nama_mahasiswa }}</td>
                    </tr>
                    <tr>
                        <td>NIM</td>
                        <td width="8" style="padding-left: 5px;">:</td>
                        <td>{{ $mahasiswa->nim }}</td>
                    </tr>
                    <tr>
                        <td>Jenis</td>
                        <td width="8" style="padding-left: 5px;">:</td>
                        <td>{{ $magang->mitra->kegiatan->kegiatan_nama }}</td>
                    </tr>
                    <tr>
                        <td>Mitra Kegiatan</td>
                        <td width="8" style="padding-left: 5px;">:</td>
                        <td>{{ $magang->mitra->mitra_nama }}</td>
                    </tr>
                    <tr>
                        <td>Dosen Pembimbing Lapangan</td>
                        <td width="8" style="padding-left: 5px;">:</td>
                        <td>{{ $nama_instruktur }}</td>
                    </tr>
                    <tr>
                        <td>Dosen Pembimbing</td>
                        <td width="8" style="padding-left: 5px;">:</td>
                        <td>{{ $nama_dosen }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="main-content2">
            <table align="center" border="1" style="margin-top: 5px; margin-bottom: 5px;" class="mhs">
                <thead>
                    <tr>
                        <th width="10" style="border: 1px solid black;"><span style="font-size: 16px;">No</span>
                        </th>
                        <th width="35" style="border: 1px solid black;"><span
                                style="font-size: 16px;">Tanggal</span>
                        </th>
                        <th width="20" style="border: 1px solid black;"><span style="font-size: 16px;">Jam
                                Mulai</span>
                        </th>
                        <th width="20" style="border: 1px solid black;"><span style="font-size: 16px;">Jam
                                Selesai</span></th>
                        <th width="90" style="border: 1px solid black;"><span style="font-size: 16px;">Penjelasan
                                Kegiatan</span></th>
                        <th width="30" style="border: 1px solid black;"><span style="font-size: 16px;">Paraf
                                Mahasiswa</span></th>
                        <th width="30" style="border: 1px solid black;"><span style="font-size: 16px;">Paraf
                                Pembimbing
                                Lapangan</span></th>
                        <th width="30" style="border: 1px solid black;"><span style="font-size: 16px;">Paraf Dosen
                                Pembimbing</span></th>
                    </tr>
                </thead>
                <tbody class="datalog">
                    @foreach ($data as $key => $item)
                        <tr class="data">
                            <td width="10" style="border: 1px solid black; text-align: left; vertical-align: top;">
                                <span style="font-size: 16px; font-weight: normal;">{{ $key + 1 }}</span>
                            </td>
                            <td width="35" style="border: 1px solid black; text-align: left; vertical-align: top;">
                                <span style="font-size: 16px; font-weight: normal;">{{ $item->tanggal }}</span>
                            </td>
                            <td width="20" style="border: 1px solid black; text-align: left; vertical-align: top;">
                                <span style="font-size: 16px; font-weight: normal;">{{ $item->jam_mulai }}</span>
                            </td>
                            <td width="20" style="border: 1px solid black; text-align: left; vertical-align: top;">
                                <span style="font-size: 16px; font-weight: normal;">{{ $item->jam_selesai }}</span>
                            </td>
                            <td width="90" style="border: 1px solid black; text-align: left; vertical-align: top;">
                                <span style="font-size: 16px; font-weight: normal;">{!! $item->topik_bimbingan !!}</span>
                            </td>
                            <td width="30" style="border: 1px solid black; text-align: left; vertical-align: top;">
                                <span style="font-size: 16px; font-weight: normal;"></span>
                            </td>
                            <td width="30" style="border: 1px solid black; text-align: left; vertical-align: top;">
                                <span style="font-size: 16px; font-weight: normal;"></span>
                            </td>
                            <td width="30" style="border: 1px solid black; text-align: left; vertical-align: top;">
                                <span style="font-size: 16px; font-weight: normal;"></span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>

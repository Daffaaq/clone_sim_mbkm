<html>

<head>
    <title>Log Bimbingan</title>
    <style>
        * {
            font-family: TimesNewRoman, Times New Roman, Times, Baskerville, Georgia, serif;
            line-height: 1.5;
        }

        body {
            /* height: 842px;
            width: 595px; */
            /* to centre page on screen*/
            /* margin-left: auto; */
            /* margin-right: auto; */
        }

        .mhs th,
        .mhs td {
            padding: 5px
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
    {{-- <img src="{{ $img }}" style="height: 80px;position: absolute;" /> --}}
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/poltek.jpeg'))) }}"
        class="app-image-style" style="height: 120px;position: absolute;top:10px" />
    <table align="center" border="0" cellpadding="1" class="main">
        <tbody>
            <tr>
                <td colspan="3">
                    <div align="center">
                        <span style="font-size: 18px;">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,<br />
                            RISET, DAN TEKNOLOGI <br />
                            POLITEKNIK NEGERI MALANG <br />
                        </span>
                        <span style="font-size: 16px;">
                            JL. Soekarno Hatta No.9 Malang 65141<br />
                            Telp (0341) 404424 - 404425 Fax (0341) 404420<br />
                            Laman://www.polinema.ac.id</span>
                        <hr style="border-top: 4px double black" />
                    </div>
                </td>
            </tr>
        </tbody>
        <div class="judul"
            style="width: 600px;font-size: 18px; text-align: center; margin-left: 80px; margin-right: auto;font-weight: bold">
            LOG BOOK AKTIVITAS HARIAN
        </div>


        <tbody>
            <tr>
                <td colspan="2">
                    <table border="0" cellpadding="1" class="tbl-no">
                        <tbody>
                            <tr>
                                <td width="93"><span style="font-size: 16px;">Nama Mahasiswa</span></td>
                                <td width="8"style="padding-left: 5px;><span style="font-size: 16px;">:</span></td>
                                <td width="200"><span style="font-size: 16px;">{{ $mahasiswa->nama_mahasiswa }}</span> </td>
                            </tr>
                            <tr>
                                <td><span style="font-size: 16px;">NIM</span></td>
                                <td width="8"style=" padding-left: 5px;><span style="font-size: 16px;">:</span>
                                </td>
                                <td><span style="font-size: 16px;">{{ $mahasiswa->nim }}</span></td>
                            </tr>
                            <tr>
                                <td><span style="font-size: 16px;">Jenis</span></td>
                                <td width="8"style="padding-left: 5px;><span style="font-size: 16px;">:</span></td>
                                <td><span style="font-size: 16px; ">{{ $magang->mitra->kegiatan->kegiatan_nama }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><span style="font-size: 16px;">Mitra Kegiatan</span></td>
                                <td width="8"style=" padding-left: 5px;><span style="font-size: 16px;">:</span>
                                </td>
                                <td><span style="font-size: 16px; ">{{ $magang->mitra->mitra_nama }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td width="100"><span style="font-size: 16px;">Dosen Pembimbing Lapangan</span></td>
                                <td width="8"style=" padding-left: 5px;><span style="font-size: 16px;">:</span>
                                </td>
                                <td><span style="font-size: 16px; ">{{ $nama_instruktur }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td width="150"><span style="font-size: 16px;">Dosen Pembimbing</span></td>
                                <td width="8"style=" padding-left: 5px;><span style="font-size: 16px;">:</span>
                                </td>
                                <td><span style="font-size: 16px; ">{{ $nama_dosen }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td valign="top">
                    {{-- <div align="right">
                        <span style="font-size: 16px;">Sumedang, 03 mei 2011</span>
                    </div> --}}
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="3" height="180" valign="top">
                    <div align="justify">
                        {{-- <span style="font-size: 16px;">Dengan ini kami mohon bantuan Bapak/Ibu agar dapat memberi
                            kesempatan kepada mahasiswa kami dari Jurusan Teknologi Informasi Program Studi
                            {{ $mitra->prodi->prodi_name }} untuk dapat melaksanakan magang industri di
                            Perusahaan/Instansi yang Bapak/Ibu
                            pimpin.
                            <br />Adapun nama-nama mahasiswa tersebut sebagai berikut:</span> --}}
                        <table border="1"
                            style="border-collapse:collapse;margin-top:5px;margin-bottom:5px;width:100%" class="mhs">
                            <thead>
                                <tr>
                                    <th width="10" style="border: 1px solid black;"><span
                                            style="font-size: 16px;">No</span></th>
                                    <th width="35" style="border: 1px solid black;"><span
                                            style="font-size: 16px;">Tanggal</span></th>
                                    <th width="20" style="border: 1px solid black;"><span
                                            style="font-size: 16px;">Jam Mulai</span></th>
                                    <th width="20" style="border: 1px solid black;"><span
                                            style="font-size: 16px;">Jam Selesai</span></th>
                                    <th width="90" style="border: 1px solid black;"><span
                                            style="font-size: 16px;">Penjelasan Kegiatan</span></th>
                                    <th width="30" style="border: 1px solid black;"><span
                                            style="font-size: 16px;">Paraf Mahasiswa</span></th>
                                    <th width="30" style="border: 1px solid black;"><span
                                            style="font-size: 16px;">Paraf Pembimbing Lapangan</span></th>
                                    <th width="30" style="border: 1px solid black;"><span
                                            style="font-size: 16px;">Paraf Dosen Pembimbing</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $item)
                                    <tr>
                                        <th width="10"
                                            style="border: 1px solid black; text-align: left; vertical-align: top;">
                                            <span
                                                style="font-size: 16px; font-weight: normal;">{{ $key + 1 }}</span>
                                        </th>
                                        <th width="35"
                                            style="border: 1px solid black; text-align: left; vertical-align: top;">
                                            <span
                                                style="font-size: 16px; font-weight: normal;">{{ $item->tanggal }}</span>
                                        </th>
                                        <th width="20"
                                            style="border: 1px solid black; text-align: left; vertical-align: top;">
                                            <span
                                                style="font-size: 16px; font-weight: normal;">{{ $item->jam_mulai }}</span>
                                        </th>
                                        <th width="20"
                                            style="border: 1px solid black; text-align: left; vertical-align: top;">
                                            <span
                                                style="font-size: 16px; font-weight: normal;">{{ $item->jam_selesai }}</span>
                                        </th>
                                        <th width="90"
                                            style="border: 1px solid black; text-align: left; vertical-align: top;">
                                            <span
                                                style="font-size: 16px; font-weight: normal;">{{ $item->topik_bimbingan }}</span>
                                        </th>
                                        <th width="30"
                                            style="border: 1px solid black; text-align: left; vertical-align: top;">
                                            <span style="font-size: 16px; font-weight: normal;"></span>
                                        </th>
                                        <th width="30"
                                            style="border: 1px solid black; text-align: left; vertical-align: top;">
                                            <span style="font-size: 16px; font-weight: normal;"></span>
                                        </th>
                                        <th width="30"
                                            style="border: 1px solid black; text-align: left; vertical-align: top;">
                                            <span style="font-size: 16px; font-weight: normal;"></span>
                                        </th>
                                    </tr>
                                @endforeach
                            </tbody>


                        </table>
                    </div>
                    {{-- <div align="center">
                        <span style="font-size: 16px;">Mengetahui</span>
                    </div> --}}
                </td>
            </tr>
            <tr>
            </tr>
        </tbody>
    </table>
</body>

</html>

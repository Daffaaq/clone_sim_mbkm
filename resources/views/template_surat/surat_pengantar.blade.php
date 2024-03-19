<html>

<head>
    <title>Surat Pengantar</title>
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
            <tr>
                <td colspan="2">
                    <table border="0" cellpadding="1" class="tbl-no">
                        <tbody>
                            <tr>
                                <td width="93"><span style="font-size: 16px;">Nomor</span></td>
                                <td width="8"><span style="font-size: 16px;">:</span></td>
                                <td width="200"><span
                                        style="font-size: 16px;margin-left:35px">{{ $sp->surat_pengantar_no }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><span style="font-size: 16px;">Lampiran</span></td>
                                <td><span style="font-size: 16px;">:</span></td>
                                <td><span style="font-size: 16px;">-</span></td>
                            </tr>
                            <tr>
                                <td><span style="font-size: 16px;">Perihal</span></td>
                                <td><span style="font-size: 16px;">:</span></td>
                                <td><span style="font-size: 16px; font-weight:bold">Permohonan Magang Industri</span>
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
                <td>
                    <table border="0" style="margin-top:20px;margin-bottom:10px">
                        <tbody>
                            <tr style="font-weight: bold">
                                <td width="74" style="vertical-align: bottom"><span
                                        style="font-size: 16px;line-height:1">Kepada
                                        Yth.</span></td>
                                <td width="140" style="vertical-align: top"><span
                                        style="font-size: 16px;line-height:1">{{ $mitra->mitra->mitra_nama }}</span>
                                </td>
                                <td width="11"></td>
                            </tr>
                            <tr style="font-weight: bold">
                                <td width="74" style="vertical-align: top"></td>
                                <td width="140" style="vertical-align: top">
                                    <span style="font-size: 16px;line-height:1">
                                        {{ $sp->surat_pengantar_alamat_mitra }}
                                    </span>
                                </td>
                                <td width="11"></td>
                            </tr>
                            {{-- <tr>
                                <td><span style="font-size: 16px;">ALAMAT</span></td>
                                <td></td>
                                <td>
                                </td>
                            </tr> --}}
                            {{-- <tr>
                                <td><span style="font-size: 16px;">di</span></td>
                                <td></td>
                                <td>
                                </td>
                            </tr>
                            <tr>
                                <td><span style="font-size: 16px;">tempat</span></td>
                                <td></td>
                                <td>
                                </td>
                            </tr> --}}
                        </tbody>
                    </table>
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="3" height="180" valign="top">
                    <div align="justify">
                        <span style="font-size: 16px;">Dengan ini kami mohon bantuan Bapak/Ibu agar dapat memberi
                            kesempatan kepada mahasiswa kami dari Jurusan Teknologi Informasi Program Studi
                            {{ $mitra->prodi->prodi_name }} untuk dapat melaksanakan magang industri di
                            Perusahaan/Instansi yang Bapak/Ibu
                            pimpin.
                            <br />Adapun nama-nama mahasiswa tersebut sebagai berikut:</span>
                        <table border="1"
                            style="border-collapse:collapse;margin-top:5px;margin-bottom:5px;width:100%" class="mhs">
                            <thead>
                                <tr>
                                    <th width="10" style="border: 1px solid black;"><span
                                            style="font-size: 16px;">No</span></th>
                                    <th width="248" style="border: 1px solid black;"><span
                                            style="font-size: 16px;">Nama</span></th>
                                    <th width="80" style="border: 1px solid black;"><span
                                            style="font-size: 16px;">NIM</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($anggotas as $key => $anggota)
                                    <tr>
                                        <td width="10" style="border: 1px solid black;line-height:0"><span
                                                style="font-size: 16px;">{{ $key + 1 }}.</span>
                                        </td>
                                        <td width="248" style="border: 1px solid black;line-height:0"><span
                                                style="font-size: 16px;">{{ $anggota->mahasiswa->nama_mahasiswa }}</span>
                                        </td>
                                        <td width="80" style="border: 1px solid black;line-height:0"><span
                                                style="font-size: 16px;">{{ $anggota->mahasiswa->nim }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div align="justify">
                            <span style="font-size: 16px;">
                                Permohonan magang industri tersebut rencana akan dilaksanakan pada bulan
                                {{ $sp->surat_pengantar_awal_pelaksanaan }} sampai
                                dengan {{ $sp->surat_pengantar_akhir_pelaksanaan }} <br />
                                Demikian atas perhatian dan kerjasamanya disampaikan terima kasih.
                            </span>
                        </div>
                    </div>
                    {{-- <div align="center">
                        <span style="font-size: 16px;">Mengetahui</span>
                    </div> --}}
                </td>
            </tr>
            <tr>
                <td valign="top">
                    <div align="center">
                        {{-- <span style="font-size: 16px;">Kepala Sekolah, </span> --}}
                    </div>
                    <div align="center">

                    </div>
                    <div align="center">
                        {{-- <span style="font-size: 16px;">E.Sulyati Dra,M.pd.</span> --}}
                    </div>
                </td>
                <td></td>
                <td>
                    <div align="left">
                        <span style="font-size: 16px;line-height:1">a.n Direktur</span><br />
                        <span style="font-size: 16px;line-height:1">Pembantu Direktur I,</span>
                    </div>
                    <div align="center" style="margin-bottom:90px">
                    </div>
                    <div align="left">
                        <span
                            style="font-size: 16px;text-decoration:underline;line-height:1">{{ $anggotas[0]->periode->periode_direktur }}
                        </span><br />
                        <span style="font-size: 16px;line-height:1">NIP.
                            {{ $anggotas[0]->periode->periode_nip }}</span>

                    </div>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 20px">
                    {{-- tembusan yth --}}
                    <span style="font-size: 16px;text-decoration:underline">Tembusan Yth : </span><br />
                    <span style="font-size: 16px;">1. Ketua Jurusan Teknologi Informasi</span><br />
                    <span style="font-size: 16px;">2. KPS Program Studi {{ $mitra->prodi->prodi_name }}</span>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>

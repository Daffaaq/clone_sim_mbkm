<table>
    <tr>
        <th>No</th>
        <th>NIM</th>
        <th>Nama Mahasiswa</th>
        <th>No Telp</th>
        <th>Kelas</th>
        <th>Prodi</th>
        <th>Nama Mitra</th>
        <th>Posisi/Skema</th>
        <th>Status</th>
        <th>Jenis Kegiatan</th>
        <th>Tanggal Awal Pelaksanaan</th>
        <th>Tanggal Akhir Pelaksanaan</th>
    </tr>
    @foreach ($mahasiswa as $mhs)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $mhs->nim }}</td>
            <td>{{ $mhs->nama_mahasiswa }}</td>
            <td>{{ $mhs->no_hp }}</td>
            <td>{{ $mhs->kelas }}</td>
            <td>{{ $mhs->prodi->prodi_name }}</td>
            <td>{!! $mhs->magang->mitra->mitra_nama ?? '-' !!}</td>
            <td>{!! $mhs->magang->magang_skema ?? '-' !!}</td>
            <td>{!! $mhs->status_magang ?? '-' !!}</td>
            <td>{!! $mhs->magang->mitra->kegiatan->kegiatan_nama ?? '-' !!}</td>
            <td>-</td>
            <td>-</td>
        </tr>
    @endforeach
</table>

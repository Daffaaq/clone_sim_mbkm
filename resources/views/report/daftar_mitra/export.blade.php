<table>
    <tr>
        <th>No</th>
        <th>Nama Mitra</th>
        <th>Alamat</th>
        <th>Jumlah Pendaftar</th>
    </tr>
    @foreach ($mitra as $m)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $m->mitra_nama }}</td>
            <td>{{ $m->mitra_alamat }}</td>
            <td>{{ $m->mitra_jumlah_pendaftar }}</td>
        </tr>
    @endforeach
</table>

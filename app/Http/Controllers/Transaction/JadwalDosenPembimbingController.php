<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Master\DosenModel;
use App\Models\Master\InstrukturModel;
use App\Models\Master\MahasiswaModel;
use App\Models\Master\NilaiPembimbingDosenModel;
use App\Models\Master\PeriodeModel;
use App\Models\Setting\UserModel;
use App\Models\Master\ProdiModel;
use App\Models\Master\TransaksiNilaiPembimbingDosenModel;
use App\Models\Transaction\DokumenBeritaAcaraModel;
use App\Models\Transaction\InstrukturLapanganModel;
use App\Models\Transaction\JadwalSidangMagangModel;
use App\Models\Transaction\KuotaDosenModel;
use App\Models\Transaction\LogBimbinganModel;
use App\Models\Transaction\LogModel;
use App\Models\Transaction\Magang;
use App\Models\Transaction\NilaiPembimbingDosenModel as TransactionNilaiPembimbingDosenModel;
use App\Models\Transaction\PembimbingDosenModel;
use App\Models\Transaction\RevisiPembimbingDosenModel;
use App\Models\Transaction\SemhasDaftarModel;
use App\Models\Transaction\TNilaiPembimbingDosenModel;
use App\Models\Transaction\TransaksiNilaiPembimbingDosenModel as TransactionTransaksiNilaiPembimbingDosenModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class JadwalDosenPembimbingController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'DOSEN.PEMBIMBING.JADWAL.SEMINAR.HASIL';
        $this->menuUrl   = url('dosen-pembimbing/jadwal-semhas-dospem');
        $this->menuTitle = 'Jadwal Semhas Dosen Pembimbing';
        $this->viewPath  = 'transaction.jadwal-semhas-dosen-pembimbing.';
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Dosen Pembimbing', 'Jadwal Semhas Dosen Pembimbing']
        ];

        $activeMenu = [
            'l1' => 'dosen-pembimbing',
            'l2' => 'dosenpem-jasem',
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl,
            'title' => 'Daftar ' . $this->menuTitle
        ];

        return view($this->viewPath . 'index')
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $activeMenu)
            ->with('page', (object) $page)
            ->with('allowAccess', $this->authAccessKey());
    }

    public function list(Request $request)
    {
        $this->authAction('read', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $user = auth()->user();
        $user_id = $user->user_id;
        $instruktur = DosenModel::where('user_id', $user_id)->first();
        $instruktur_id = $instruktur->dosen_id;

        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');

        $pembimbing_dosen = PembimbingDosenModel::where('dosen_id', $instruktur_id)
            ->where('periode_id', $activePeriods)
            ->pluck('pembimbing_dosen_id') // Ambil hanya kolom pembimbing_dosen_id
            ->unique() // Hanya nilai unik
            ->sort() // Urutkan nilai
            ->values() // Reset index array
            ->toArray(); // Konversi ke array

        $pembimbing_dosen_ids = $pembimbing_dosen;

        $data = SemhasDaftarModel::where('t_semhas_daftar.periode_id', $activePeriods)
            ->wherein('t_semhas_daftar.pembimbing_dosen_id', $pembimbing_dosen_ids)
            ->leftJoin('s_user', 't_semhas_daftar.created_by', '=', 's_user.user_id')
            ->leftJoin('m_mahasiswa', 's_user.user_id', '=', 'm_mahasiswa.user_id')
            ->leftJoin('t_pembimbing_dosen', 't_semhas_daftar.pembimbing_dosen_id', '=', 't_pembimbing_dosen.pembimbing_dosen_id')
            ->leftJoin('m_dosen', 't_pembimbing_dosen.dosen_id', '=', 'm_dosen.dosen_id')
            ->leftJoin('t_instruktur_lapangan', 't_semhas_daftar.instruktur_lapangan_id', '=', 't_instruktur_lapangan.instruktur_lapangan_id')
            ->leftJoin('m_instruktur', 't_instruktur_lapangan.instruktur_id', '=', 'm_instruktur.instruktur_id')
            ->select(
                't_semhas_daftar.semhas_daftar_id',
                'm_mahasiswa.nama_mahasiswa',
                't_semhas_daftar.Judul',
                'm_instruktur.nama_instruktur AS nama_instruktur'
            )
            ->get();

        $data->each(function ($item) use ($activePeriods) {
            $nilaiExist = TNilaiPembimbingDosenModel::where('semhas_daftar_id', $item->semhas_daftar_id)
                ->where('periode_id', $activePeriods)
                ->exists();
            $item->nilai_exist = $nilaiExist;
            $datajadwal = JadwalSidangMagangModel::where('semhas_daftar_id', $item->semhas_daftar_id)
                ->where('periode_id', $activePeriods)
                ->pluck('deadline_penilaian')
                ->first(); // Ambil nilai pertama atau null jika tidak ada

            $item->jadwal = $datajadwal ?? '-';
        });
        // dd($data);
        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }



    public function show($id)
    {
        $this->authAction('read', 'modal');
        // if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();
        $user = auth()->user();
        $user_id = $user->user_id;
        $instruktur = DosenModel::where('user_id', $user_id)->first();
        $instruktur_id = $instruktur->dosen_id;

        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');

        $pembimbing_dosen = PembimbingDosenModel::where('dosen_id', $instruktur_id)
            ->where('periode_id', $activePeriods)
            ->pluck('pembimbing_dosen_id') // Ambil hanya kolom pembimbing_dosen_id
            ->unique() // Hanya nilai unik
            ->sort() // Urutkan nilai
            ->values() // Reset index array
            ->toArray(); // Konversi ke array

        $pembimbing_dosen_ids = $pembimbing_dosen;

        $data = SemhasDaftarModel::where('t_semhas_daftar.periode_id', $activePeriods)
            ->wherein('t_semhas_daftar.pembimbing_dosen_id', $pembimbing_dosen_ids)
            ->leftJoin('s_user', 't_semhas_daftar.created_by', '=', 's_user.user_id')
            ->leftJoin('m_mahasiswa', 's_user.user_id', '=', 'm_mahasiswa.user_id')
            ->leftJoin('t_pembimbing_dosen', 't_semhas_daftar.pembimbing_dosen_id', '=', 't_pembimbing_dosen.pembimbing_dosen_id')
            ->leftJoin('m_dosen as d1', 't_semhas_daftar.dosen_pembahas_id', '=', 'd1.dosen_id') // Menggunakan alias yang sama seperti saat memilih kolom
            ->leftJoin('m_dosen as d2', 't_pembimbing_dosen.dosen_id', '=', 'd2.dosen_id')
            ->leftJoin('t_instruktur_lapangan', 't_semhas_daftar.instruktur_lapangan_id', '=', 't_instruktur_lapangan.instruktur_lapangan_id')
            ->leftJoin('m_instruktur', 't_instruktur_lapangan.instruktur_id', '=', 'm_instruktur.instruktur_id')
            ->select(
                't_semhas_daftar.semhas_daftar_id',
                'm_mahasiswa.nama_mahasiswa',
                't_semhas_daftar.Judul',
                't_semhas_daftar.magang_id',
                'd1.dosen_name AS nama_dosen_pembahas', // Menggunakan alias yang sama seperti dalam JOIN
                'm_instruktur.nama_instruktur AS nama_instruktur',
                'd2.dosen_name AS nama_dosen',
                't_semhas_daftar.tanggal_daftar',
                't_semhas_daftar.link_github',
                't_semhas_daftar.link_laporan'
            )
            ->find($id);
        // dd($data);


        $magang = Magang::where('magang_id', $data->magang_id)
            ->with('mitra')
            ->with('mitra.kegiatan')
            ->with('periode')
            ->where('periode_id', $activePeriods)
            ->first();
        // dd($magang);
        $datajadwal = JadwalSidangMagangModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->first();
        $page = [
            'title' => 'Detail ' . $this->menuTitle
        ];

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'detail')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('datajadwal', $datajadwal)
            ->with('magang', $magang)
            ->with('data', $data);
    }
    public function nilai($id)
    {
        $this->authAction('read', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $user = auth()->user();
        $user_id = $user->user_id;
        $instruktur = DosenModel::where('user_id', $user_id)->first();
        $pembimbing_dosen_id = $instruktur->dosen_id;

        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');

        $pembimbing_dosen = PembimbingDosenModel::where('dosen_id', $pembimbing_dosen_id)
            ->where('periode_id', $activePeriods)
            ->pluck('pembimbing_dosen_id') // Ambil hanya kolom pembimbing_dosen_id
            ->unique() // Hanya nilai unik
            ->sort() // Urutkan nilai
            ->values() // Reset index array
            ->toArray(); // Konversi ke array

        $pembimbing_dosen_ids = $pembimbing_dosen;

        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
        $data = SemhasDaftarModel::where('t_semhas_daftar.periode_id', $activePeriods)
            ->wherein('t_semhas_daftar.pembimbing_dosen_id', $pembimbing_dosen_ids)
            ->leftJoin('s_user', 't_semhas_daftar.created_by', '=', 's_user.user_id')
            ->leftJoin('m_mahasiswa', 's_user.user_id', '=', 'm_mahasiswa.user_id')
            ->leftJoin('t_pembimbing_dosen', 't_semhas_daftar.pembimbing_dosen_id', '=', 't_pembimbing_dosen.pembimbing_dosen_id')
            ->leftJoin('m_dosen as d1', 't_semhas_daftar.dosen_pembahas_id', '=', 'd1.dosen_id') // Menggunakan alias yang sama seperti saat memilih kolom
            ->leftJoin('m_dosen as d2', 't_pembimbing_dosen.dosen_id', '=', 'd2.dosen_id')
            ->leftJoin('t_instruktur_lapangan', 't_semhas_daftar.instruktur_lapangan_id', '=', 't_instruktur_lapangan.instruktur_lapangan_id')
            ->leftJoin('m_instruktur', 't_instruktur_lapangan.instruktur_id', '=', 'm_instruktur.instruktur_id')
            ->select(
                't_semhas_daftar.semhas_daftar_id',
                'm_mahasiswa.nama_mahasiswa',
                't_semhas_daftar.Judul',
                'd1.dosen_name AS nama_dosen_pembahas', // Menggunakan alias yang sama seperti dalam JOIN
                'm_instruktur.nama_instruktur AS nama_instruktur',
                'd2.dosen_name AS nama_dosen', // Menggunakan alias yang sama seperti dalam JOIN
                't_semhas_daftar.magang_id',
                't_semhas_daftar.tanggal_daftar',
                't_semhas_daftar.link_github',
                't_semhas_daftar.link_laporan'
            )
            ->find($id);
        // dd($data);
        $semhas_daftar_id = $id;
        // dd($semhas_daftar_id);

        $magang = Magang::where('magang_id', $data->magang_id)
            ->with('mitra')
            ->with('mitra.kegiatan')
            ->with('periode')
            ->where('periode_id', $activePeriods)
            ->first();
        // dd($magang);
        $datajadwal = JadwalSidangMagangModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->first();
        $datanilai = TNilaiPembimbingDosenModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->get();
        // dd($datanilai);

        $kriteriaNilai = NilaiPembimbingDosenModel::with('subKriteria')->where('periode_id', $activePeriods)->get();
        // if ($kriteriaNilai->isEmpty()) {
        //     // Tampilkan pesan error atau lakukan tindakan lain jika tidak ada data ditemukan
        //     return $this->showModalError('Kesalahan', 'Terjadi Kesalahan!!!', 'Belum ada Kriteria Nilai.');
        // }
        $subkriteria = NilaiPembimbingDosenModel::with('parent')
            ->whereNotNull('parent_id')
            ->where('periode_id', $activePeriods)
            ->count();

        // dd($subkriteria);

        // // Mengambil parent_id dari hasil query
        // $idSubKriteria = $subkriteria->pluck('parent_id');

        // // Menggunakan parent_id untuk mencari subkriteria
        // $subkriteria1 = NilaiPembimbingDosenModel::whereIn('parent_id', $idSubKriteria)->get();

        // dd($subkriteria1);
        $existingNilai = RevisiPembimbingDosenModel::where('semhas_daftar_id', $data->semhas_daftar_id)
            ->where('periode_id', $activePeriods)
            ->first();
        $dataJadwalSeminar = JadwalSidangMagangModel::where('semhas_daftar_id', $data->semhas_daftar_id)->first();
        // dd($dataJadwalSeminar);
        // dd($existingNilai);
        $page = [
            'title' => 'Nilai Dosen Pembimbing'
        ];

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'nilai')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('kriteriaNilai', $kriteriaNilai)
            ->with('activePeriods', $activePeriods)
            ->with('semhas_daftar_id', $semhas_daftar_id)
            ->with('dataJadwalSeminar', $dataJadwalSeminar)
            ->with('datanilai', $datanilai)
            ->with('existingNilai', $existingNilai)
            ->with('data', $data);
    }
    // public function simpanNilai(Request $request)
    // {
    //     $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
    //     // dd($request->all());
    //     $nilaiData = $request->input('nilai');
    //     $nilaiPembimbingDosenIds = $request->input('nilai_pembimbing_dosen_id');
    //     $periodeId = $request->input('periode_id');
    //     $semhasDaftarId = $request->input('semhas_daftar_id');

    //     $dataToStore = [];
    //     // $subkriteria = NilaiPembimbingDosenModel::with('parent')
    //     //     ->whereNotNull('parent_id')
    //     //     ->where('periode_id', $activePeriods)
    //     //     ->count();
    //     // $subkriteria1 = NilaiPembimbingDosenModel::with('parent')
    //     //     ->whereNotNull('parent_id')
    //     //     ->where('periode_id', $activePeriods)
    //     //     ->get();
    //     // // dd($subkriteria1);

    //     // // Inisialisasi total nilai
    //     // $kriteriaNilai1 = NilaiPembimbingDosenModel::has('subKriteria')->where('periode_id', $activePeriods)->pluck('nilai_pembimbing_dosen_id')->first();
    //     // $kriteriaNilai = NilaiPembimbingDosenModel::has('subKriteria')->where('periode_id', $activePeriods)->first();

    //     // $average = $kriteriaNilai / $subkriteria;
    //     // dd($average);

    //     // Loop through the indices of nilaiData
    //     foreach ($nilaiPembimbingDosenIds as $index => $id) {
    //         // Periksa apakah nilai kriteria tidak kosong
    //         if (!empty($nilaiData[$index])) {
    //             // Buat larik asosiatif untuk setiap pasangan nilai dan nilai_pembimbing_dosen_id
    //             $dataToStore[] = [
    //                 'nilai' => $nilaiData[$index],
    //                 'nilai_pembimbing_dosen_id' => $id,
    //                 'periode_id' => $periodeId,
    //                 'semhas_daftar_id' => $semhasDaftarId
    //             ];
    //         }
    //     }
    //     // dd($dataToStore);

    //     // Simpan data menggunakan batch insert (jika Anda ingin melakukan insert sekali untuk semua data)
    //     TNilaiPembimbingDosenModel::insert($dataToStore);

    //     // Jika berhasil disimpan, kembalikan respons sesuai kebutuhan
    //     return response()->json(['message' => 'Nilai berhasil disimpan'], 200);
    // }
    public function simpanNilai(Request $request)
    {
        // Ambil input dari request
        $nilaiData = $request->input('nilai');
        $nilaiPembimbingDosenIds = $request->input('nilai_pembimbing_dosen_id');
        $periodeId = $request->input('periode_id');
        $semhasDaftarId = $request->input('semhas_daftar_id');

        // Loop melalui nilai-nilai yang diberikan
        foreach ($nilaiPembimbingDosenIds as $index => $id) {
            // Cari data nilai yang sudah ada berdasarkan `nilai_pembimbing_dosen_id`
            $existingNilai = TNilaiPembimbingDosenModel::where('nilai_pembimbing_dosen_id', $id)
                ->where('periode_id', $periodeId)
                ->where('semhas_daftar_id', $semhasDaftarId)
                ->first();

            // Jika data sudah ada, perbarui nilai
            if ($existingNilai) {
                $existingNilai->nilai = $nilaiData[$index];
                $existingNilai->save();
            } else {
                // Jika tidak, buat data baru
                TNilaiPembimbingDosenModel::create([
                    'nilai' => $nilaiData[$index],
                    'nilai_pembimbing_dosen_id' => $id,
                    'periode_id' => $periodeId,
                    'semhas_daftar_id' => $semhasDaftarId
                ]);
            }
        }

        $existingNilai = RevisiPembimbingDosenModel::where('periode_id', $periodeId)
            ->where('semhas_daftar_id', $semhasDaftarId)
            ->first();

        if ($existingNilai) {
            $existingNilai->update([
                'saran_pembimbing_dosen' => $request->input('saran_pembimbing_dosen'),
                'catatan_pembimbing_dosen' => $request->input('catatan_pembimbing_dosen'),
                // update other fields as needed
            ]);
        } else {
            $saranKomentar = RevisiPembimbingDosenModel::create([
                'saran_pembimbing_dosen' => $request->input('saran_pembimbing_dosen'),
                'catatan_pembimbing_dosen' => $request->input('catatan_pembimbing_dosen'),
                'semhas_daftar_id' => $semhasDaftarId,
                'periode_id' => $periodeId,
                // fill other fields as needed
            ]);
        }



        // Berhasil disimpan, kembalikan respons
        return response()->json(['success' => true]);
    }

    public function validasi_berita_acara($id)
    {

        $page = [
            'url' => $this->menuUrl,
            'title' => 'Validasi berita_acara Dosen Pembimbing'
        ];

        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
        $data = SemhasDaftarModel::where('t_semhas_daftar.periode_id', $activePeriods)
            ->leftJoin('s_user', 't_semhas_daftar.created_by', '=', 's_user.user_id')
            ->leftJoin('m_mahasiswa', 's_user.user_id', '=', 'm_mahasiswa.user_id')
            ->leftJoin('t_pembimbing_dosen', 't_semhas_daftar.pembimbing_dosen_id', '=', 't_pembimbing_dosen.pembimbing_dosen_id')
            ->leftJoin('m_dosen as d1', 't_semhas_daftar.dosen_pembahas_id', '=', 'd1.dosen_id') // Menggunakan alias yang sama seperti saat memilih kolom
            ->leftJoin('m_dosen as d2', 't_pembimbing_dosen.dosen_id', '=', 'd2.dosen_id')
            ->leftJoin('t_instruktur_lapangan', 't_semhas_daftar.instruktur_lapangan_id', '=', 't_instruktur_lapangan.instruktur_lapangan_id')
            ->leftJoin('m_instruktur', 't_instruktur_lapangan.instruktur_id', '=', 'm_instruktur.instruktur_id')
            ->select(
                't_semhas_daftar.semhas_daftar_id',
                'm_mahasiswa.nama_mahasiswa',
                't_semhas_daftar.Judul',
                'd1.dosen_name AS nama_dosen_pembahas', // Menggunakan alias yang sama seperti dalam JOIN
                'm_instruktur.nama_instruktur AS nama_instruktur',
                'd2.dosen_name AS nama_dosen', // Menggunakan alias yang sama seperti dalam JOIN
                't_semhas_daftar.magang_id',
                't_semhas_daftar.tanggal_daftar',
                't_semhas_daftar.link_github',
                't_semhas_daftar.link_laporan'
            )
            ->find($id);
        $semhas_daftar_id = $id;
        // dd($semhas_daftar_id);

        $magang = Magang::where('magang_id', $data->magang_id)
            ->with('mitra')
            ->with('mitra.kegiatan')
            ->with('periode')
            ->where('periode_id', $activePeriods)
            ->first();
        $databeritaacara = DokumenBeritaAcaraModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->latest()->first();
        if ($databeritaacara == null) {
            // Tampilkan pesan error atau lakukan tindakan lain jika tidak ada data ditemukan
            return $this->showModalError('Kesalahan', 'Terjadi Kesalahan!!!', 'Belum Upload Berita Acara.');
        }
        $id_joined = $data->pluck('semhas_daftar_id');
        $databeritaacaraall = DokumenBeritaAcaraModel::wherein('semhas_daftar_id', $id_joined)->where('periode_id', $activePeriods)->get();

        return view($this->viewPath . 'verifikasi')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data)
            ->with('databeritaacara', $databeritaacara)
            ->with('databeritaacaraall', $databeritaacaraall)
            ->with('magang', $magang);
    }

    public function verifikasi_berita_acara(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            // Mendapatkan periode aktif
            $id = $request->id;
            $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');

            // Validasi input dari request
            $validatedData = $request->validate([
                'dokumen_berita_status' => 'required|in:1,2', // Validasi hanya menerima 1 atau 2
                'dokumen_berita_acara_keterangan' => 'nullable|string',
            ]);

            try {
                // Temukan dokumen yang akan diupdate berdasarkan periode aktif dan ID
                $dokumenBeritaAcara = DokumenBeritaAcaraModel::where('periode_id', $activePeriods)->findOrFail($id);

                // Update data dokumen
                $dokumenBeritaAcara->dokumen_berita_status = $request->dokumen_berita_status;
                $dokumenBeritaAcara->dokumen_berita_acara_keterangan = $request->dokumen_berita_acara_keterangan;
                $dokumenBeritaAcara->updated_by = auth()->id(); // Simpan user ID yang mengupdate
                $dokumenBeritaAcara->save();

                $status = ($dokumenBeritaAcara->dokumen_berita_status == 1) ? 'Diterima' : (($dokumenBeritaAcara->dokumen_berita_status == 2) ? 'Ditolak' : 'Pending');
                LogModel::create([
                    'user_id' => auth()->id(),
                    'action' => 'update',
                    'url' => $this->menuUrl,
                    'data' => 'Keterangan Berita Acara: ' . $dokumenBeritaAcara->dokumen_berita_acara_keterangan . ', Status: ' . $status,
                    'created_by' => auth()->id(),
                    'periode_id' => $activePeriods,
                ]);

                // Kembalikan respons sukses
                return response()->json(['status' => true, 'message' => 'Berita Acara berhasil diperbarui'], 200);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                // Kembalikan respons error jika dokumen tidak ditemukan
                return response()->json(['status' => false, 'error' => 'Dokumen Berita Acara tidak ditemukan'], 404);
            }
        }

        return redirect('/');
    }

    public function updateStatusDosenFromModal(Request $request, $id)
    {
        // Validasi data yang diterima dari permintaan
        $request->validate([
            'status1' => 'required|in:0,1,2',
            'nilai_pembimbing_dosen' => 'required|numeric'
        ]);

        // Ambil data yang dikirimkan melalui permintaan AJAX
        $statusDosen = $request->input('status1');
        $nilaiPembimbingDosen = $request->input('nilai_pembimbing_dosen');

        // Lakukan proses pembaruan status dosen pembimbing di sini
        $logBimbingan = LogBimbinganModel::find($id);

        if (!$logBimbingan) {
            // Jika log bimbingan tidak ditemukan, kembalikan respons dengan pesan error
            return response()->json(['success' => false, 'message' => 'Log bimbingan tidak ditemukan']);
        }

        // Proses update status dosen pembimbing
        $logBimbingan->status1 = $statusDosen;

        // Set tanggal_status_dosen berdasarkan status yang diubah
        if ($statusDosen == 1) {
            // Jika status diubah menjadi 'diterima', atur tanggal_status_dosen menjadi tanggal saat ini
            $logBimbingan->tanggal_status_dosen = now();
        } else if ($statusDosen == 2) {
            // Jika status diubah menjadi 'ditolak', atur nilai_pembimbing_dosen menjadi 0
            $logBimbingan->nilai_pembimbing_dosen = 0;
            // Atur tanggal_status_dosen menjadi null atau kosong
            $logBimbingan->tanggal_status_dosen = now(); // Sesuaikan dengan preferensi Anda
        } else {
            // Jika status diubah menjadi 'pending', atur tanggal_status_dosen menjadi null atau kosong
            $logBimbingan->tanggal_status_dosen = null; // Sesuaikan dengan preferensi Anda
        }

        // Set nilai_pembimbing_dosen berdasarkan input pengguna
        $logBimbingan->nilai_pembimbing_dosen = $nilaiPembimbingDosen; // Gunakan nilai yang diambil dari input pengguna

        // Simpan perubahan
        $logBimbingan->save();

        // Kemudian kembalikan respons
        return response()->json(['success' => true]);
    }


    public function confirm($id)
    {
        $this->authAction('delete', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = LogBimbinganModel::find($id);

        return (!$data) ? $this->showModalError() :
            $this->showModalConfirm($this->menuUrl . '/' . $id, [
                'NIP' => $data->dosen_nip,
                'Nama Dosen' => $data->dosen_name,
            ]);
    }

    public function destroy(Request $request, $id)
    {
        $this->authAction('delete', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $res = LogBimbinganModel::deleteData($id);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => LogBimbinganModel::getDeleteMessage()
            ]);
        }

        return redirect('/');
    }
}

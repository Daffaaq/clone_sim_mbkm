<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Master\DosenModel;
use App\Models\Master\InstrukturModel;
use App\Models\Master\MahasiswaModel;
use App\Models\Master\NilaiPembahasDosenModel;
use App\Models\Master\NilaiPembimbingDosenModel;
use App\Models\Master\NilaiInstrukturLapanganModel;
use App\Models\Master\PeriodeModel;
use App\Models\Setting\UserModel;
use App\Models\Master\ProdiModel;
use App\Models\Transaction\InstrukturLapanganModel;
use App\Models\Transaction\JadwalSidangMagangModel;
use App\Models\Transaction\KuotaDosenModel;
use App\Models\Transaction\LogBimbinganModel;
use App\Models\Transaction\Magang;
use Illuminate\Support\Facades\Crypt;
use App\Models\Transaction\PembimbingDosenModel;
use App\Models\Transaction\RevisiInstrukturLapanganModel;
use App\Models\Transaction\RevisiPembahasDosenModel;
use App\Models\Transaction\RevisiPembimbingDosenModel;
use App\Models\Transaction\SemhasDaftarModel;
use App\Models\Transaction\TNilaiInstrukturLapanganModel;
use App\Models\Transaction\TNilaiPembahasDosenModel;
use App\Models\Transaction\TNilaiPembimbingDosenModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\Dompdf\Options;
use Illuminate\Validation\Rule;

class UjianSeminarHasilController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'TRANSACTION.UJIAN.SEMHAS';
        $this->menuUrl   = url('transaksi/ujian-seminar-hasil');
        $this->menuTitle = 'Ujian Seminar Hasil';
        $this->viewPath  = 'transaction.ujian-seminar-hasil.';
    }

    public function index()
    {
        $user = auth()->user();
        // $user = auth()->user()->id;
        $user_id = $user->user_id;
        $mahasiswa = MahasiswaModel::where('user_id', $user_id)->first();
        $mahasiswa_id = $mahasiswa->mahasiswa_id;
        $activePeriods = PeriodeModel::where('is_current', 1)->pluck('periode_id');
        // Gunakan mahasiswa_id untuk mencari data magang
        $magang_data = Magang::where('mahasiswa_id', $mahasiswa_id)->get();

        $magang_status = Magang::where('mahasiswa_id', $mahasiswa_id)
            ->where('status', 1)
            ->where('periode_id', $activePeriods) // Status 1 menunjukkan 'Diterima'
            ->exists();
        if ($magang_status) {
            $this->authAction('read');
            $this->authCheckDetailAccess();

            $breadcrumb = [
                'title' => $this->menuTitle,
                'list'  => ['Transaksi', 'Ujian Seminar Hasil']
            ];

            $activeMenu = [
                'l1' => 'transaction',
                'l2' => 'transaksi-ujasem',
                'l3' => null
            ];

            $page = [
                'url' => $this->menuUrl,
                'title' => 'Jadwal ' . $this->menuTitle
            ];
            $user = auth()->user();
            // dd($user);
            // $user = auth()->user()->id;
            $user_id = $user->user_id;
            // $userId = Auth::id();
            $mahasiswa = MahasiswaModel::where('user_id', $user_id)->first();
            $mahasiswa_id = $mahasiswa->mahasiswa_id;
            $data = SemhasDaftarModel::where('created_by', $user_id)
                ->where('periode_id', $activePeriods)
                ->with('pembimbingDosen.dosen')
                ->with('magang.mitra')
                ->with('magang.mitra.kegiatan')
                ->with('magang.periode')
                ->with('magang.mahasiswa')
                ->first();
            if (!$data) {
                $message = "halaman belum bisa diakses. Silahkan untuk mendaftar seminar magang terlebih dahulu";
                return view($this->viewPath . 'index')
                    ->with('breadcrumb', (object) $breadcrumb)
                    ->with('activeMenu', (object) $activeMenu)
                    ->with('page', (object) $page)
                    ->with('data', $data)
                    ->with('user', $user)
                    ->with('message', $message)
                    ->with('allowAccess', $this->authAccessKey());
            }
            // dd($data);
            $dataJadwalSeminar = JadwalSidangMagangModel::where('semhas_daftar_id', $data->semhas_daftar_id)->first();
            // dd($dataJadwalSeminar);
            $datanilai = TNilaiPembimbingDosenModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->get();
            // dd($datanilai);
            $existingNilai = RevisiPembimbingDosenModel::where('semhas_daftar_id', $data->semhas_daftar_id)
                ->where('periode_id', $activePeriods)
                ->first();
            $datanilaiPembahas = TNilaiPembahasDosenModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->get();
            // dd($datanilai);
            $existingNilaiPembahas = RevisiPembahasDosenModel::where('semhas_daftar_id', $data->semhas_daftar_id)
                ->where('periode_id', $activePeriods)
                ->first();
            // 
            $datanilaiInstrukturLapangan = TNilaiInstrukturLapanganModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->get();
            // dd($datanilai);
            $existingNilaiInstrukturLapangan = RevisiInstrukturLapanganModel::where('semhas_daftar_id', $data->semhas_daftar_id)
                ->where('periode_id', $activePeriods)
                ->first();
            // 
            // dd($dataJadwalSeminar);
            // dd($hasilPerbandingan);
            // dd($datajamsidangselesai);
            // $datacomparasion = 
            // dd($jamsekarang);
            if (!$dataJadwalSeminar) {
                $message = "halaman belum bisa diakses. Silahkan menunggu untuk pembagian jadwal sidang magang";
                return view($this->viewPath . 'index')
                    ->with('breadcrumb', (object) $breadcrumb)
                    ->with('activeMenu', (object) $activeMenu)
                    ->with('page', (object) $page)
                    ->with('data', $data)
                    ->with('user', $user)
                    ->with('message', $message)
                    ->with('allowAccess', $this->authAccessKey());
            }
            $dataJadwalSeminar->jam_sidang_mulai = substr($dataJadwalSeminar->jam_sidang_mulai, 0, 5); // Mengambil karakter pertama hingga karakter ke-4
            $dataJadwalSeminar->jam_sidang_selesai = substr($dataJadwalSeminar->jam_sidang_selesai, 0, 5); // Mengambil karakter pertama hingga karakter ke-4

            $encryption = Crypt::encrypt($data->semhas_daftar_id);
            // dd($encryption);

            return view($this->viewPath . 'index')
                ->with('breadcrumb', (object) $breadcrumb)
                ->with('activeMenu', (object) $activeMenu)
                ->with('page', (object) $page)
                ->with('data', $data)
                ->with('user', $user)
                ->with('dataJadwalSeminar', $dataJadwalSeminar)
                ->with('datanilai', $datanilai)
                ->with('existingNilai', $existingNilai)
                ->with('dataJadwalSeminar', $dataJadwalSeminar)
                ->with('datanilaiPembahas', $datanilaiPembahas)
                ->with('existingNilaiPembahas', $existingNilaiPembahas)
                ->with('existingNilaiInstrukturLapangan', $existingNilaiInstrukturLapangan)
                ->with('datanilaiInstrukturLapangan', $datanilaiInstrukturLapangan)
                ->with('allowAccess', $this->authAccessKey());
        } else {
            $this->authAction('read');
            $this->authCheckDetailAccess();

            $breadcrumb = [
                'title' => $this->menuTitle,
                'list'  => ['Transaksi', 'Ujian Seminar Hasil']
            ];

            $activeMenu = [
                'l1' => 'transaction',
                'l2' => 'transaksi-ujasem',
                'l3' => null
            ];

            $page = [
                'url' => $this->menuUrl,
                'title' => 'Daftar ' . $this->menuTitle
            ];

            $magang_status = Magang::where('mahasiswa_id', $mahasiswa_id)
                ->where('status', 0) // Status 0 menunjukkan 'Belum keterima'
                ->exists();

            if ($magang_status) {
                $message = "Anda belum keterima dalam magang. Silahkan untuk menunggu.";
            } elseif (Magang::where('mahasiswa_id', $mahasiswa_id)->exists()) {
                // Mahasiswa telah mendaftar magang tetapi belum diterima atau ditolak
                $message = "Anda belum keterima dalam magang. Silahkan untuk mendaftar ulang.";
            } else {
                // Mahasiswa belum mendaftar magang
                $message = "Anda belum mendaftar magang. Silahkan untuk mendaftar magang.";
            }
            return view('transaction.instruktur.index1')
                ->with('breadcrumb', (object) $breadcrumb)
                ->with('activeMenu', (object) $activeMenu)
                ->with('page', (object) $page)
                ->with('message', $message)
                ->with('allowAccess', $this->authAccessKey());
        }
    }

    public function generateBeritaAcara()
    {
        $user = auth()->user();
        // dd($user);
        // $user = auth()->user()->id;
        $user_id = $user->user_id;
        // $userId = Auth::id();
        $mahasiswa = MahasiswaModel::where('user_id', $user_id)->first();
        $mahasiswa_id = $mahasiswa->mahasiswa_id;
        $activePeriods = PeriodeModel::where('is_active', 1)->pluck('periode_id');
        $datamsemhasdaftar = SemhasDaftarModel::where('created_by', $user_id)->pluck('magang_id');
        $datamagang = Magang::where('magang_id', $datamsemhasdaftar)->pluck('magang_kode');
        $anggota = Magang::where('magang_kode', $datamagang)
            ->with('mahasiswa')
            ->where('periode_id', $activePeriods->toArray())
            ->get();
        $data = SemhasDaftarModel::where('created_by', $user_id)
            ->whereHas('magang', function ($query) use ($activePeriods) {
                $query->where('periode_id', $activePeriods->toArray());
            })
            ->with('pembimbingDosen.dosen')
            ->with('magang.mitra')
            ->with('magang.mitra.kegiatan')
            ->with('magang.periode')
            ->with('magang.mahasiswa')
            ->first();
        $dataJadwalSeminar = JadwalSidangMagangModel::where('semhas_daftar_id', $data->semhas_daftar_id)->first();
        $dataJadwalSeminar->jam_sidang_mulai = substr($dataJadwalSeminar->jam_sidang_mulai, 0, 5); // Mengambil karakter pertama hingga karakter ke-4
        $dataJadwalSeminar->jam_sidang_selesai = substr($dataJadwalSeminar->jam_sidang_selesai, 0, 5); // Mengambil karakter pertama hingga karakter ke-4

        $pdf = Pdf::loadView('transaction.ujian-seminar-hasil.cetak_berita_acara', compact('dataJadwalSeminar', 'data', 'anggota'));
        return $pdf->stream();
    }
    public function uploadBeritaAcara(Request $request)
    {
        // Validasi ukuran file
        $request->validate([
            'berita_acara_file' => 'required|file|max:2048', // Maksimal 2 MB
        ]);

        $activePeriods = PeriodeModel::where('is_current', 1)->pluck('periode_id');
        $user = auth()->user();
        $user_id = $user->user_id;
        $mahasiswa = MahasiswaModel::where('user_id', $user_id)->first();
        $mahasiswa_id = $mahasiswa->mahasiswa_id;

        // Mendapatkan data SemhasDaftarModel
        $data = SemhasDaftarModel::where('created_by', $user_id)
            ->where('periode_id', $activePeriods)
            ->with('pembimbingDosen.dosen')
            ->with('magang.mitra')
            ->with('magang.mitra.kegiatan')
            ->with('magang.periode')
            ->with('magang.mahasiswa')
            ->first();

        // Pastikan data ditemukan sebelum memperbarui
        if ($data) {
            if ($request->hasFile('berita_acara_file')) {
                $file = $request->file('berita_acara_file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/assets/berita-acara', $filename); // Simpan file ke direktori 'storage/app/berita_acara'
                // $disk = 'public'; // Ganti dengan nama disk yang diinginkan, misalnya 's3'

                // Simpan file ke dalam disk storage yang diinginkan
                // Storage::disk($disk)->put('assets/berita-acara/' . $filename, file_get_contents($file));

                // Update kolom 'berita_acara' pada data SemhasDaftarModel dengan nama file
                $data->update([
                    'Berita_acara' => $filename,
                    // update other fields as needed
                ]);
                return response()->json(['message' => 'File uploaded successfully', 'filename' => $filename], 200);
            } else {
                return response()->json(['error' => 'No file uploaded.'], 400);
            }
        } else {
            // Jika data tidak ditemukan, lakukan sesuatu, misalnya tampilkan pesan kesalahan
            return response()->json(['error' => 'Data not found.'], 404);
        }
    }

    public function nilai($id)
    {
        $this->authAction('read', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

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
        $encryption = Crypt::encrypt($id);

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
        // dd($existingNilai);
        $page = [
            'title' => 'Nilai Dosen Pembimbing'
        ];

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'nilai-dosbing')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('kriteriaNilai', $kriteriaNilai)
            ->with('activePeriods', $activePeriods)
            ->with('semhas_daftar_id', $semhas_daftar_id)
            ->with('datanilai', $datanilai)
            ->with('existingNilai', $existingNilai)
            ->with('encryption', $encryption)
            ->with('data', $data);
    }
    public function nilaiDosenPembahas($id)
    {
        $this->authAction('read', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

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
        $encryption = Crypt::encrypt($id);

        $magang = Magang::where('magang_id', $data->magang_id)
            ->with('mitra')
            ->with('mitra.kegiatan')
            ->with('periode')
            ->where('periode_id', $activePeriods)
            ->first();
        $datajadwal = JadwalSidangMagangModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->first();
        $datanilai = TNilaiPembahasDosenModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->get();

        $kriteriaNilai = NilaiPembahasDosenModel::with('subKriteria')->where('periode_id', $activePeriods)->get();
        $subkriteria = NilaiPembahasDosenModel::with('parent')
            ->whereNotNull('parent_id')
            ->where('periode_id', $activePeriods)
            ->count();

        $existingNilai = RevisiPembahasDosenModel::where('semhas_daftar_id', $data->semhas_daftar_id)
            ->where('periode_id', $activePeriods)
            ->first();
        $page = [
            'title' => 'Nilai Dosen Pembahas'
        ];

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'nilai-dospem')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('kriteriaNilai', $kriteriaNilai)
            ->with('activePeriods', $activePeriods)
            ->with('semhas_daftar_id', $semhas_daftar_id)
            ->with('datanilai', $datanilai)
            ->with('existingNilai', $existingNilai)
            ->with('encryption', $encryption)
            ->with('data', $data);
    }
    public function nilaiInstrukturLapangan($id)
    {
        $this->authAction('read', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

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
        $encryption = Crypt::encrypt($id);

        $magang = Magang::where('magang_id', $data->magang_id)
            ->with('mitra')
            ->with('mitra.kegiatan')
            ->with('periode')
            ->where('periode_id', $activePeriods)
            ->first();
        // dd($magang);
        $datajadwal = JadwalSidangMagangModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->first();
        $datanilai = TNilaiInstrukturLapanganModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->get();
        // dd($datanilai);

        $kriteriaNilai = NilaiInstrukturLapanganModel::with('subKriteria')->where('periode_id', $activePeriods)->get();
        $subkriteria = NilaiInstrukturLapanganModel::with('parent')

            ->whereNotNull('parent_id')
            ->where('periode_id', $activePeriods)
            ->count();

        // dd($subkriteria1);
        $existingNilai = RevisiInstrukturLapanganModel::where('semhas_daftar_id', $data->semhas_daftar_id)
            ->where('periode_id', $activePeriods)
            ->first();
        // dd($existingNilai);
        $page = [
            'title' => 'Nilai Instruktur Lapangan'
        ];

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'nilai-instruktur-lapangan')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('kriteriaNilai', $kriteriaNilai)
            ->with('activePeriods', $activePeriods)
            ->with('semhas_daftar_id', $semhas_daftar_id)
            ->with('datanilai', $datanilai)
            ->with('existingNilai', $existingNilai)
            ->with('encryption', $encryption)
            ->with('data', $data);
    }
    public function nilaiAkhir($id)
    {
        $this->authAction('read', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

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
        $encryption = Crypt::encrypt($id);

        $magang = Magang::where('magang_id', $data->magang_id)
            ->with('mitra')
            ->with('mitra.kegiatan')
            ->with('periode')
            ->where('periode_id', $activePeriods)
            ->first();
        // dd($magang);
        $datajadwal = JadwalSidangMagangModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->first();
        $datanilaiinstruktur = TNilaiInstrukturLapanganModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->get();


        $kriteriaNilaiintruktur = NilaiInstrukturLapanganModel::with('subKriteria')->where('periode_id', $activePeriods)->get();

        $datanilaiPembahas = TNilaiPembahasDosenModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->get();

        $kriteriaNilaiPembahas = NilaiPembahasDosenModel::with('subKriteria')->where('periode_id', $activePeriods)->get();

        $datanilaiPembimbing = TNilaiPembimbingDosenModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->get();
        // dd($datanilai);

        $kriteriaNilaiPembimbing = NilaiPembimbingDosenModel::with('subKriteria')->where('periode_id', $activePeriods)->get();

        $page = [
            'title' => 'Nilai Akhir'
        ];

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'nilai-akhir')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('kriteriaNilaiintruktur', $kriteriaNilaiintruktur)
            ->with('kriteriaNilaiPembahas', $kriteriaNilaiPembahas)
            ->with('kriteriaNilaiPembimbing', $kriteriaNilaiPembimbing)
            ->with('activePeriods', $activePeriods)
            ->with('semhas_daftar_id', $semhas_daftar_id)
            ->with('datanilaiPembahas', $datanilaiPembahas)
            ->with('datanilaiinstruktur', $datanilaiinstruktur)
            ->with('datanilaiPembimbing', $datanilaiPembimbing)
            ->with('encryption', $encryption)
            ->with('data', $data);
    }

    public function cetakNilaiDosenPembimbing($id)
    {
        $id = Crypt::decrypt($id);
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

        $magang = Magang::where('magang_id', $data->magang_id)
            ->with('mitra')
            ->with('mitra.kegiatan')
            ->with('periode')
            ->where('periode_id', $activePeriods)
            ->first();

        $datanilai = TNilaiPembimbingDosenModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->get();
        $kriteriaNilai = NilaiPembimbingDosenModel::with('subKriteria')->where('periode_id', $activePeriods)->get();
        $subkriteria = NilaiPembimbingDosenModel::with('parent')
            ->whereNotNull('parent_id')
            ->where('periode_id', $activePeriods)
            ->count();

        $existingNilai = RevisiPembimbingDosenModel::where('semhas_daftar_id', $data->semhas_daftar_id)
            ->where('periode_id', $activePeriods)
            ->first();

        // Perhitungan nilai
        $totalNilai = 0;
        $nilaiDetails = [];

        foreach ($kriteriaNilai as $nilai) {
            $nilaiModel = $datanilai->firstWhere('nilai_pembimbing_dosen_id', $nilai->nilai_pembimbing_dosen_id);
            $nilaiValue = $nilaiModel ? $nilaiModel->nilai : 0;
            $nilaiXBobot = $nilai->bobot ? (float)$nilaiValue * (float)$nilai->bobot : 0;
            $totalNilai += $nilaiXBobot;

            $nilaiDetails[] = [
                'name' => $nilai->name_kriteria_pembimbing_dosen,
                'nilai' => $nilaiValue,
                'bobot' => $nilai->bobot,
                'nilaiXBobot' => number_format($nilaiXBobot, 2, '.', '')
            ];
        }
        $totalNilai = sprintf("%.2f", $totalNilai);


        $pdf = Pdf::loadView('transaction.ujian-seminar-hasil.cetak-nilai-pembimbing-dosen', compact('data', 'magang', 'nilaiDetails', 'totalNilai', 'existingNilai'));
        return $pdf->stream();
    }
    public function cetakNilaiDosenPembahas($id)
    {
        $id = Crypt::decrypt($id);
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

        $magang = Magang::where('magang_id', $data->magang_id)
            ->with('mitra')
            ->with('mitra.kegiatan')
            ->with('periode')
            ->where('periode_id', $activePeriods)
            ->first();

        $datajadwal = JadwalSidangMagangModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->first();
        $datanilai = TNilaiPembahasDosenModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->get();

        $kriteriaNilai = NilaiPembahasDosenModel::with('subKriteria')->where('periode_id', $activePeriods)->get();
        $subkriteria = NilaiPembahasDosenModel::with('parent')
            ->whereNotNull('parent_id')
            ->where('periode_id', $activePeriods)
            ->count();

        $existingNilai = RevisiPembahasDosenModel::where('semhas_daftar_id', $data->semhas_daftar_id)
            ->where('periode_id', $activePeriods)
            ->first();

        // Perhitungan nilai
        $totalNilai = 0;
        $nilaiDetails = [];

        foreach ($kriteriaNilai as $nilai) {
            $nilaiModel = $datanilai->firstWhere('nilai_pembahas_dosen_id', $nilai->nilai_pembahas_dosen_id);
            $nilaiValue = $nilaiModel ? $nilaiModel->nilai : 0;
            $nilaiXBobot = $nilai->bobot ? (float)$nilaiValue * (float)$nilai->bobot : 0;
            $totalNilai += $nilaiXBobot;

            $nilaiDetails[] = [
                'name' => $nilai->name_kriteria_pembahas_dosen,
                'nilai' => $nilaiValue,
                'bobot' => $nilai->bobot,
                'nilaiXBobot' => sprintf("%.2f", $nilaiXBobot)
            ];
        }

        // Format totalNilai ke dua desimal sebagai string untuk tampilan
        $totalNilai = sprintf("%.2f", $totalNilai);

        $pdf = Pdf::loadView('transaction.ujian-seminar-hasil.cetak-nilai-pembahas-dosen', compact('data', 'magang', 'nilaiDetails', 'totalNilai', 'existingNilai'));
        return $pdf->stream();
    }
    public function cetakNilaiInstrukturLapangan($id)
    {
        $id = Crypt::decrypt($id);
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
        $encryption = Crypt::encrypt($id);

        $magang = Magang::where('magang_id', $data->magang_id)
            ->with('mitra')
            ->with('mitra.kegiatan')
            ->with('periode')
            ->where('periode_id', $activePeriods)
            ->first();
        // dd($magang);
        $datajadwal = JadwalSidangMagangModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->first();
        $datanilai = TNilaiInstrukturLapanganModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->get();
        // dd($datanilai);

        $kriteriaNilai = NilaiInstrukturLapanganModel::with('subKriteria')->where('periode_id', $activePeriods)->get();
        $subkriteria = NilaiInstrukturLapanganModel::with('parent')

            ->whereNotNull('parent_id')
            ->where('periode_id', $activePeriods)
            ->count();

        // dd($subkriteria1);
        $existingNilai = RevisiInstrukturLapanganModel::where('semhas_daftar_id', $data->semhas_daftar_id)
            ->where('periode_id', $activePeriods)
            ->first();

        // Perhitungan nilai
        $totalNilai = 0;
        $nilaiDetails = [];

        foreach ($kriteriaNilai as $nilai) {
            $nilaiModel = $datanilai->firstWhere('nilai_instruktur_lapangan_id', $nilai->nilai_instruktur_lapangan_id);
            $nilaiValue = $nilaiModel ? $nilaiModel->nilai : 0;
            $nilaiXBobot = $nilai->bobot ? (float)$nilaiValue * (float)$nilai->bobot : 0;
            $totalNilai += $nilaiXBobot;

            // Modify the sprintf format to always display two decimal places
            $nilaiDetails[] = [
                'name' => $nilai->name_kriteria_instruktur_lapangan,
                'nilai' => $nilaiValue,
                'bobot' => $nilai->bobot,
                'nilaiXBobot' => sprintf("%.2f", $nilaiXBobot)
            ];
        }
        $totalNilai = sprintf("%.2f", $totalNilai);

        $pdf = Pdf::loadView('transaction.ujian-seminar-hasil.cetak-nilai-Instruktur-lapangan', compact('data', 'magang', 'nilaiDetails', 'totalNilai', 'existingNilai'));
        return $pdf->stream();
    }
    public function cetakNilaiAkhir($id)
    {
        $id = Crypt::decrypt($id);
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
        $encryption = Crypt::encrypt($id);

        $magang = Magang::where('magang_id', $data->magang_id)
            ->with('mitra')
            ->with('mitra.kegiatan')
            ->with('periode')
            ->where('periode_id', $activePeriods)
            ->first();
        // dd($magang);
        $datanilaiinstruktur = TNilaiInstrukturLapanganModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->get();


        $kriteriaNilaiintruktur = NilaiInstrukturLapanganModel::with('subKriteria')->where('periode_id', $activePeriods)->get();

        $datanilaiPembahas = TNilaiPembahasDosenModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->get();

        $kriteriaNilaiPembahas = NilaiPembahasDosenModel::with('subKriteria')->where('periode_id', $activePeriods)->get();

        $datanilaiPembimbing = TNilaiPembimbingDosenModel::where('semhas_daftar_id', $data->semhas_daftar_id)->where('periode_id', $activePeriods)->get();
        // dd($datanilai);

        $kriteriaNilaiPembimbing = NilaiPembimbingDosenModel::with('subKriteria')->where('periode_id', $activePeriods)->get();

        // Perhitungan nilai instruktur
        $totalNilaiInstruktur = 0;
        $nilaiDetailsInstruktur = [];

        foreach ($kriteriaNilaiintruktur as $nilai) {
            $nilaiModel = $datanilaiinstruktur->firstWhere('nilai_instruktur_lapangan_id', $nilai->nilai_instruktur_lapangan_id);
            $nilaiValue = $nilaiModel ? $nilaiModel->nilai : 0;
            $nilaiXBobot = $nilai->bobot ? (float)$nilaiValue * (float)$nilai->bobot : 0;
            $totalNilaiInstruktur += $nilaiXBobot;

            // Modify the sprintf format to always display two decimal places
            $nilaiDetailsInstruktur[] = [
                'name' => $nilai->name_kriteria_instruktur_lapangan,
                'nilai' => $nilaiValue,
                'bobot' => $nilai->bobot,
                'nilaiXBobot' => sprintf("%.2f", $nilaiXBobot)
            ];
        }
        // Perhitungan nilai pembimbing
        // Perhitungan nilai pembimbing
        $totalNilaiPembimbing = 0;
        $nilaiDetailsPembimbing = [];

        foreach ($kriteriaNilaiPembimbing as $nilai) {
            $nilaiModel = $datanilaiPembimbing->firstWhere('nilai_pembimbing_dosen_id', $nilai->nilai_pembimbing_dosen_id);
            $nilaiValue = $nilaiModel ? $nilaiModel->nilai : 0;
            $nilaiXBobot = $nilai->bobot ? (float)$nilaiValue * (float)$nilai->bobot : 0;
            $totalNilaiPembimbing += $nilaiXBobot;

            // Perbaiki penamaan variabel di bawah dari $nilaiDetailsPembimbin menjadi $nilaiDetailsPembimbing
            $nilaiDetailsPembimbing[] = [
                'name' => $nilai->name_kriteria_pembimbing_dosen,
                'nilai' => $nilaiValue,
                'bobot' => $nilai->bobot,
                'nilaiXBobot' => sprintf("%.2f", $nilaiXBobot)
            ];
        }


        // Perhitungan nilai pembahas
        $totalNilaiPembahas = 0;
        $nilaiDetailsPembahas = [];

        foreach ($kriteriaNilaiPembahas as $nilai) {
            $nilaiModel = $datanilaiPembahas->firstWhere('nilai_pembahas_dosen_id', $nilai->nilai_pembahas_dosen_id);
            $nilaiValue = $nilaiModel ? $nilaiModel->nilai : 0;
            $nilaiXBobot = $nilai->bobot ? (float)$nilaiValue * (float)$nilai->bobot : 0;
            $totalNilaiPembahas += $nilaiXBobot;

            $nilaiDetailsPembahas[] = [
                'name' => $nilai->name_kriteria_pembahas_dosen,
                'nilai' => $nilaiValue,
                'bobot' => $nilai->bobot,
                'nilaiXBobot' => sprintf("%.2f", $nilaiXBobot)
            ];
        }

        // Hitung nilai total instruktur
        $totalNilaiInstruktur = 0;
        foreach ($nilaiDetailsInstruktur as $detail) {
            $totalNilaiInstruktur += $detail['nilaiXBobot'];
        }

        // Hitung nilai total pembahas
        $totalNilaiPembahas = 0;
        foreach ($nilaiDetailsPembahas as $detail) {
            $totalNilaiPembahas += $detail['nilaiXBobot'];
        }

        // Hitung nilai total pembimbing
        $totalNilaiPembimbing = 0;
        foreach ($nilaiDetailsPembimbing as $detail) {
            $totalNilaiPembimbing += $detail['nilaiXBobot'];
        }

        $totalNilaiInstruktur = sprintf("%.2f", $totalNilaiInstruktur);
        $totalNilaiPembahas = sprintf("%.2f", $totalNilaiPembahas);
        $totalNilaiPembimbing = sprintf("%.2f", $totalNilaiPembimbing);
        // Hitung nilai akhir
        // dd($nilaiDetailsPembimbing);
        $nilaiAkhirdemo = ($totalNilaiInstruktur * 0.5) + ($totalNilaiPembahas * 0.15) + ($totalNilaiPembimbing * 0.35);
        $nilaiAkhir = sprintf("%.2f", $nilaiAkhirdemo);

        $pdf = Pdf::loadView('transaction.ujian-seminar-hasil.cetak-nilai-akhir', compact('data', 'magang', 'totalNilaiInstruktur', 'totalNilaiPembahas', 'totalNilaiPembimbing', 'nilaiAkhir'));
        return $pdf->stream();
    }
}

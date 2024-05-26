<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Master\PeriodeModel;
use App\Models\Transaction\Magang;
use App\Models\Master\NilaiPembahasDosenModel;
use App\Models\Master\NilaiPembimbingDosenModel;
use App\Models\Master\NilaiInstrukturLapanganModel;
use App\Models\Transaction\RevisiInstrukturLapanganModel;
use App\Models\Transaction\RevisiPembahasDosenModel;
use App\Models\Transaction\RevisiPembimbingDosenModel;
use App\Models\Transaction\SemhasDaftarModel;
use App\Models\Transaction\TNilaiInstrukturLapanganModel;
use App\Models\Transaction\TNilaiPembahasDosenModel;
use App\Models\Transaction\TNilaiPembimbingDosenModel;
use App\Models\Transaction\JadwalSidangMagangModel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DaftarMahasiswaSudahMagangController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'DAFTAR.DONE.MAGANG';
        $this->menuUrl   = url('daftar-mahasiswa-selesai-magang');     // set URL untuk menu ini
        $this->menuTitle = 'Daftar Mahasiswa Selesai Magang';                       // set nama menu
        $this->viewPath  = 'report.daftar_mahasiswa_selesai_magang.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Daftar Mahasiswa Diterima']
        ];

        $activeMenu = [
            'l1' => 'daftar-donma',
            'l2' => null,
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl,
            'title' => $this->menuTitle
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

        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
        $data = SemhasDaftarModel::where('t_semhas_daftar.periode_id', $activePeriods)
            ->leftJoin('s_user', 't_semhas_daftar.created_by', '=', 's_user.user_id')
            ->leftJoin('m_mahasiswa', 's_user.user_id', '=', 'm_mahasiswa.user_id')
            ->leftJoin('t_pembimbing_dosen', 't_semhas_daftar.pembimbing_dosen_id', '=', 't_pembimbing_dosen.pembimbing_dosen_id')
            ->leftJoin('m_dosen', 't_pembimbing_dosen.dosen_id', '=', 'm_dosen.dosen_id')
            ->leftJoin('t_instruktur_lapangan', 't_semhas_daftar.instruktur_lapangan_id', '=', 't_instruktur_lapangan.instruktur_lapangan_id')
            ->leftJoin('m_instruktur', 't_instruktur_lapangan.instruktur_id', '=', 'm_instruktur.instruktur_id')
            ->select(
                't_semhas_daftar.semhas_daftar_id',
                'm_mahasiswa.nama_mahasiswa',
                'm_mahasiswa.prodi_id',
                't_semhas_daftar.Judul',
                'm_dosen.dosen_name AS nama_dosen',
                'm_instruktur.nama_instruktur AS nama_instruktur',
                't_semhas_daftar.Berita_acara',
                DB::raw("CASE 
                        WHEN t_semhas_daftar.Berita_acara IS NOT NULL 
                          AND t_semhas_daftar.Berita_acara != '' 
                          AND EXISTS (SELECT 1 FROM t_nilai_instruktur_lapangan WHERE semhas_daftar_id = t_semhas_daftar.semhas_daftar_id AND periode_id = {$activePeriods}) 
                          AND EXISTS (SELECT 1 FROM t_nilai_pembimbing_dosen WHERE semhas_daftar_id = t_semhas_daftar.semhas_daftar_id AND periode_id = {$activePeriods}) 
                          AND EXISTS (SELECT 1 FROM t_nilai_pembahas_dosen WHERE semhas_daftar_id = t_semhas_daftar.semhas_daftar_id AND periode_id = {$activePeriods}) 
                        THEN 'Sudah Selesai Magang' 
                        ELSE 'Belum Selesai Magang' 
                    END AS magang_status")
            );

        if (auth()->user()->group_id == 1) {
            $data = $data->get();
        } else {
            $prodi_id = auth()->user()->getProdiId();
            $data = $data->where('m_mahasiswa.prodi_id', $prodi_id)->get();
        }
        $data->each(function ($item) use ($activePeriods) {
            $nilaiExistInstruktur = TNilaiInstrukturLapanganModel::where('semhas_daftar_id', $item->semhas_daftar_id)
                ->where('periode_id', $activePeriods)
                ->exists();
            $item->nilai_exist_instruktur = $nilaiExistInstruktur;
        });
        $data->each(function ($item) use ($activePeriods) {
            $nilaiExistPembimbing = TNilaiPembimbingDosenModel::where('semhas_daftar_id', $item->semhas_daftar_id)
                ->where('periode_id', $activePeriods)
                ->exists();
            $item->nilai_exist_pembimbing = $nilaiExistPembimbing;
        });
        $data->each(function ($item) use ($activePeriods) {
            $nilaiExistPembahas = TNilaiPembahasDosenModel::where('semhas_daftar_id', $item->semhas_daftar_id)
                ->where('periode_id', $activePeriods)
                ->exists();
            $item->nilai_exist_pembahas = $nilaiExistPembahas;
        });

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }
    public function nilaiPembimbing($id)
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


        $pdf = Pdf::loadView('report.daftar_mahasiswa_selesai_magang.cetak-nilai-pembimbing-dosen', compact('data', 'magang', 'nilaiDetails', 'totalNilai', 'existingNilai'));
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

        $pdf = Pdf::loadView('report.daftar_mahasiswa_selesai_magang.cetak-nilai-pembahas-dosen', compact('data', 'magang', 'nilaiDetails', 'totalNilai', 'existingNilai'));
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

        $pdf = Pdf::loadView('report.daftar_mahasiswa_selesai_magang.cetak-nilai-Instruktur-lapangan', compact('data', 'magang', 'nilaiDetails', 'totalNilai', 'existingNilai'));
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

        $pdf = Pdf::loadView('report.daftar_mahasiswa_selesai_magang.cetak-nilai-akhir', compact('data', 'magang', 'totalNilaiInstruktur', 'totalNilaiPembahas', 'totalNilaiPembimbing', 'nilaiAkhir'));
        return $pdf->stream();
    }
}

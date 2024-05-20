<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Master\PeriodeModel;
use App\Models\Transaction\Magang;
use App\Models\Transaction\SemhasDaftarModel;
use Illuminate\Http\Request;
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
                't_semhas_daftar.Judul',
                'm_dosen.dosen_name AS nama_dosen',
                'm_instruktur.nama_instruktur AS nama_instruktur'
            )
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }
}

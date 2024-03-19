<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Master\PeriodeModel;
use App\Models\Transaction\Magang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DaftarMahasiswaDiterimaController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'LAPORAN.DAFTAR.ACC';
        $this->menuUrl   = url('laporan/daftar-mahasiswa-diterima');     // set URL untuk menu ini
        $this->menuTitle = 'Daftar Mahasiswa Diterima';                       // set nama menu
        $this->viewPath  = 'report.daftar_mahasiswa_diterima.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Report', 'Daftar Mahasiswa Diterima']
        ];

        $activeMenu = [
            'l1' => 'report',
            'l2' => 'daftar-mahasiswa-diterima',
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

        $active_periode = PeriodeModel::where('is_current', 1)->first();

        $data  = Magang::with('mahasiswa')
            ->with('mitra')
            ->with('periode')
            ->with('prodi')
            ->with('mitra.kegiatan')
            ->where('periode_id', $active_periode->periode_id)
            ->where('status', 1);

        if (auth()->user()->group_id == 1) {
            $data = $data->get();
        } else if (auth()->user()->group_id == 4) {
            $data = $data->where('mahasiswa_id', auth()->user()->getUserMahasiswa->mahasiswa_id)->get();
        } else {
            $prodi_id = auth()->user()->getProdiId();
            $data = $data->where('prodi_id', $prodi_id)->get();
        }

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function confirm($id)
    {
        $this->authAction('delete', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = Magang::with('mahasiswa')
            ->with('mitra')
            ->with('periode')
            ->with('prodi')
            ->with('mitra.kegiatan')
            ->where('magang_id', $id)
            ->first();

        return (!$data) ? $this->showModalError() :
            $this->showModalConfirm(
                $this->menuUrl . '/' . $id,
                [
                    'Magang ID' => $data->magang_kode,
                    'Skema' => $data->magang_skema,
                    'Mitra' => $data->mitra->mitra_nama,
                    'Kegiatan' => $data->mitra->kegiatan->kegiatan_nama,
                    'Nama' => $data->mahasiswa->nama_mahasiswa,
                    'NIM' => $data->mahasiswa->nim,
                ],
            );
    }

    public function destroy(Request $request, $id)
    {
        $this->authAction('delete', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {
            $res = Magang::deleteData($id);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => Magang::getDeleteMessage()
            ]);
        }

        return redirect('/');
    }
}

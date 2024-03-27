<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Master\MahasiswaModel;
use App\Models\Master\PeriodeModel;
use App\Models\Master\ProdiModel;
use App\Models\Transaction\Magang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DaftarMahasiswaController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'LAPORAN.DAFTAR.MHS';
        $this->menuUrl   = url('laporan/daftar-mahasiswa');     // set URL untuk menu ini
        $this->menuTitle = 'Daftar Mahasiswa';                       // set nama menu
        $this->viewPath  = 'report.daftar_mahasiswa.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Report', 'Daftar Mahasiswa']
        ];

        $activeMenu = [
            'l1' => 'report',
            'l2' => 'daftar-mahasiswa',
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl,
            'title' => $this->menuTitle
        ];

        //make static statues with 0=Diterima, 1=Terdaftar, 2=Belum Terdaftar/Ditolak
        $statuses = [
            (object) ['id' => 1, 'name' => 'Diterima'],
            (object) ['id' => 2, 'name' => 'Terdaftar'],
            (object) ['id' => 3, 'name' => 'Belum Terdaftar/Ditolak']
        ];

        $prodis = ProdiModel::select('prodi_id', 'prodi_name', 'prodi_code')->get();

        return view($this->viewPath . 'index')
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $activeMenu)
            ->with('page', (object) $page)
            ->with('statuses', $statuses)
            ->with('prodis', $prodis)
            ->with('allowAccess', $this->authAccessKey());
    }

    public function list(Request $request)
    {
        $this->authAction('read', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $active_periode = PeriodeModel::where('is_current', 1)->first();

        $data  = MahasiswaModel::with('prodi');


        if ($request->prodi_id) {
            $data->where('prodi_id', $request->prodi_id);
        }

        if (auth()->user()->group_id == 1) {
            $data = $data->get();
        } else if (auth()->user()->group_id == 4) {
            $data = $data->where('mahasiswa_id', auth()->user()->getUserMahasiswa->mahasiswa_id)->get();
        } else {
            $prodi_id = auth()->user()->getProdiId();
            $data = $data->where('prodi_id', $prodi_id)->get();
        }

        $data = $data->map(function ($item) use ($active_periode) {
            $magang = Magang::with('mitra')
                ->with('mitra.kegiatan')
                ->where('mahasiswa_id', $item->mahasiswa_id)
                ->where('periode_id', $active_periode->periode_id)
                ->latest()
                ->first();

            if (!$magang) {
                $status_magagng = 3;
            } else {
                if ($magang->magang_tipe == "1") {
                    if ($magang->is_accept == "2") {
                        $status_magagng = 3;
                    } else {
                        if ($magang->status == 1) {
                            $status_magagng = 1;
                        } elseif ($magang->status == 0) {
                            $status_magagng = 2;
                        } elseif ($magang->status == 2) {
                            $status_magagng = 3;
                        } elseif ($magang->status == 3) {
                            $status_magagng = 2;
                        }
                    }
                } else {
                    if ($magang->status == 1) {
                        $status_magagng = 1;
                    } elseif ($magang->status == 0) {
                        $status_magagng = 2;
                    } elseif ($magang->status == 2) {
                        $status_magagng = 3;
                    } elseif ($magang->status == 3) {
                        $status_magagng = 2;
                    }
                }
            }

            $item->status_magang = (string)$status_magagng;

            $item->magang = $magang;

            return $item;
        });

        if ($request->status) {
            $data = $data->where('status_magang', (string)$request->status);
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

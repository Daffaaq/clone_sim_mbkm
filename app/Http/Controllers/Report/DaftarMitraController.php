<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Master\PeriodeModel;
use App\Models\MitraModel;
use App\Models\Transaction\Magang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DaftarMitraController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'LAPORAN.DAFTAR.MITRA';
        $this->menuUrl   = url('laporan/daftar-mitra');     // set URL untuk menu ini
        $this->menuTitle = 'Daftar Mitra';                       // set nama menu
        $this->viewPath  = 'report.daftar_mitra.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Report', 'Daftar Mitra']
        ];

        $activeMenu = [
            'l1' => 'report',
            'l2' => 'daftar-mitra',
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

        $data  = MitraModel::with('kegiatan')
            ->with('periode');
        // ->get();

        // if (auth()->user()->group_id != 1) {
        //     //data in mitra with column mitra_prodi is [1,2,3,etc]
        //     //how to get with getProdiId() include with mitra_prodi
        //     $prodi_id = auth()->user()->getProdiId();

        //     // $data = $data->filter(function ($item) use ($prodi_id) {
        //     //     dd($prodi_id, json_decode($item->mitra_prodi));
        //     //     return in_array($prodi_id, json_decode($item->mitra_prodi));
        //     // });
        //     $data->whereRaw('find_in_set(?, mitra_prodi)', $prodi_id);
        // }
        $data = $data->get();
        $data = $data->map(function ($item) {
            //TODO: get jumlah pendaftar
            $item['mitra_jumlah_pendaftar'] = Magang::where('mitra_id', $item->mitra_id)
                ->where('periode_id', PeriodeModel::where('is_current', 1)->first()->periode_id)
                ->whereIn('status', [1])->get();
            //if magang_tipe == 1 and is_accept == 2 then remove
            $item['mitra_jumlah_pendaftar'] = $item['mitra_jumlah_pendaftar']->filter(function ($item) {
                return $item->magang_tipe != 1 || $item->is_accept != 2;
            })->count();

            return $item;
        });

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }
}

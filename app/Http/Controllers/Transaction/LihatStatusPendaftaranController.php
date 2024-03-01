<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Transaction\Magang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LihatStatusPendaftaranController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'TRANSACTION.LIHAT.STATUS.PENDAFTARAN';
        $this->menuUrl   = url('transaksi/lihat-status-pendaftaran');     // set URL untuk menu ini
        $this->menuTitle = 'Lihat Status Pendafatran';                       // set nama menu
        $this->viewPath  = 'transaction.lihat-status-pendaftaran.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Transaksi', 'Lihat Status Pendafatran']
        ];

        $activeMenu = [
            'l1' => 'transaction',
            'l2' => 'transaksi-lihat-status-pendaftaran',
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


        $data  = Magang::with('mahasiswa')
            ->with('mitra')
            ->with('periode')
            ->with('prodi')
            ->with('mitra.kegiatan');

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
}

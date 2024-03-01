<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Master\MahasiswaModel;
use App\Models\MitraModel;
use Illuminate\Http\Request;
use stdClass;
use Yajra\DataTables\Facades\DataTables;

class LihatStatusPengajuanController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'TRANSACTION.LIHAT.STATUS.PENGAJUAN';
        $this->menuUrl   = url('transaksi/lihat-status-pengajuan');     // set URL untuk menu ini
        $this->menuTitle = 'Lihat Status Pengajuan Mitra';                       // set nama menu
        $this->viewPath  = 'transaction.lihat-status-pengajuan.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Transaksi', 'Lihat Status Pengajuan Mitra']
        ];

        $activeMenu = [
            'l1' => 'transaction',
            'l2' => 'transaksi-lihat-status-pengajuan',
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
            ->with('periode')
            ->where('created_by', auth()->user()->user_id)
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function alasan($id)
    {
        $this->authAction('read', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = MitraModel::find($id);
        $page = [
            'title' => 'Detail ' . $this->menuTitle
        ];

        $mitra = MitraModel::where('mitra_id', $id)
            ->with('kegiatan')
            ->with('periode')
            ->first();

        $datas = [
            [
                "title" => "Nama Kegiatan",
                "value" => $mitra->kegiatan->kegiatan_nama,
                "bold" => false
            ],
            [
                "title" => "Nama Mitra",
                "value" => $mitra->mitra_nama,
                "bold" => true
            ],
            [
                "title" => "Periode",
                "value" => $mitra->periode->periode_nama,
                "bold" => false
            ],
            [
                "title" => "Status",
                "value" => $mitra->status == 0 ? 'Menunggu' : ($mitra->status == 1 ? 'Diterima' : 'Ditolak'),
                "bold" => false,
                "color" => $mitra->status == 0 ? 'info' : ($mitra->status == 1 ? 'success' : 'danger')
            ],
            [
                "title" => "Keterangan Ditolak",
                "value" => $mitra->mitra_keterangan_ditolak ?? '-',
                "bold" => false
            ]
        ];



        //change to stdClass loop
        $datas = array_map(function ($item) {
            $obj = new stdClass;
            $obj->title = $item['title'];
            $obj->value = $item['value'];
            $obj->bold = $item['bold'];
            $obj->color = $item['color'] ?? null;
            return $obj;
        }, $datas);

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'alasan')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data)
            ->with('datas', $datas)
            ->with('url', $this->menuUrl . '/' . $id . '/kuota')
            ->with('action', 'PUT')
            ->with('mitra', $mitra);
    }
}

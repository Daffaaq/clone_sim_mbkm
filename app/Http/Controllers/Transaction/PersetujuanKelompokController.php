<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Transaction\Magang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PersetujuanKelompokController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'TRANSACTION.PERSETUJUAN.KELOMPOK';
        $this->menuUrl   = url('transaksi/persetujuan-kelompok');     // set URL untuk menu ini
        $this->menuTitle = 'Persetujuan Kelompok';                       // set nama menu
        $this->viewPath  = 'transaction.persetujuan-kelompok.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Transaksi', 'Persetujuan Kelompok']
        ];

        $activeMenu = [
            'l1' => 'transaction',
            'l2' => 'transaksi-persetujuan-kelompok',
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
            ->with('mitra.kegiatan')
            ->where('magang_tipe', '!=', '2');
        // ->where('is_accept', 0);

        if (auth()->user()->group_id == 1) {
            $data = $data->get();
        } else if (auth()->user()->group_id == 4) {
            $data = $data->where('mahasiswa_id', auth()->user()->getUserMahasiswa->mahasiswa_id)->get();
        } else {
            $prodi_id = auth()->user()->getProdiId();
            $data = $data->where('prodi_id', $prodi_id)->get();
        }

        $data = $data->map(function ($item) {
            $ketua = Magang::with('mahasiswa')->where('magang_kode', $item->magang_kode)->where('magang_tipe', 0)->first();
            $item->ketua = $ketua->mahasiswa->nama_mahasiswa;
            return $item;
        });

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function confirm_approve($id)
    {
        $this->authAction('update', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = Magang::with('mahasiswa')
            ->with('mitra')
            ->with('periode')
            ->with('prodi')
            ->with('mitra.kegiatan')
            ->where('magang_id', $id)
            ->first();

        $ketua = Magang::with('mahasiswa')->where('magang_kode', $data->magang_kode)->where('magang_tipe', 0)->first();

        return (!$data) ? $this->showModalError() :
            $this->showModalConfirmCustom(
                $this->viewPath . 'action',
                $this->menuUrl . '/' . $id . '/approve',
                [
                    'Nama Ketua' => $ketua->mahasiswa->nama_mahasiswa,
                    'Mitra' => $data->mitra->mitra_nama,
                    'Kegiatan' => $data->mitra->kegiatan->kegiatan_nama,
                ],
                'Konfirmasi Terima',
                'Apakah anda yakin ingin menerima undangan dari ketua:',
                'Ya, Approve',
                'PUT'
            );
    }

    public function confirm_reject($id)
    {
        $this->authAction('update', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = Magang::with('mahasiswa')
            ->with('mitra')
            ->with('periode')
            ->with('prodi')
            ->with('mitra.kegiatan')
            ->where('magang_id', $id)
            ->first();

        $ketua = Magang::with('mahasiswa')->where('magang_kode', $data->magang_kode)->where('magang_tipe', 0)->first();

        return (!$data) ? $this->showModalError() :
            $this->showModalConfirm($this->menuUrl . '/' . $id . '/reject', [
                'Nama Ketua' => $ketua->mahasiswa->nama_mahasiswa,
                'Mitra' => $data->mitra->mitra_nama,
                'Kegiatan' => $data->mitra->kegiatan->kegiatan_nama,
            ], 'Konfirmasi Tolak', 'Apakah anda yakin ingin menolak undangan dari ketua:', 'Ya, Reject', 'PUT');
    }

    public function approve(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $request['is_accept'] = 1; // [0: pending, 1: approved, 2: rejected]
            $res = Magang::updateData($id, $request);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? 'Undangan berhasil diapprove.' : 'Undangan gagal diapprove.'
            ]);
        }

        return redirect('/');
    }

    public function reject(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $request['is_accept'] = 2; // [0: pending, 1: approved, 2: rejected]
            // $request['mitra_keterangan_ditolak'] = $request->reason;
            // unset($request['reason']);
            $res = Magang::updateData($id, $request);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? 'Undangan berhasil direject.' : 'Undangan gagal direject.'
            ]);
        }

        return redirect('/');
    }
}

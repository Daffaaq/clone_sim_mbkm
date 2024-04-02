<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Master\DosenModel;
use App\Models\Setting\UserModel;
use App\Models\Master\ProdiModel;
use App\Models\Master\SemhasModel;
use App\Models\Transaction\KuotaDosenModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SemhasController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'TRANSACTION.SEMHAS';
        $this->menuUrl   = url('transaksi/seminar-hasil');
        $this->menuTitle = 'Seminar Hasil';
        $this->viewPath  = 'transaction.semhas.';
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Transaksi', 'Seminar Hasil']
        ];

        $activeMenu = [
            'l1' => 'transaction',
            'l2' => 'transaksi-semhas',
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
        // $prodi_id = auth()->user()->prodi_id;
        // $data = SemhasModel::leftJoin('m_prodi', 'm_semhas.prodi_id', '=', 'm_prodi.prodi_id')
        //     ->selectRaw('m_semhas.semhas_id, m_semhas.judul_semhas, m_semhas.gelombang, m_semhas.kuota_bimbingan, 
        //          m_semhas.tanggal_mulai_pendaftaran, m_semhas.tanggal_akhir_pendaftaran,
        //          m_prodi.prodi_name')
        //     ->get();
        // $prodi_id = auth()->user()->prodi_id;
        // dd($prodi_id);
        // // $dataall = SemhasModel::all();
        // // dd($dataall);
        // $data = SemhasModel::leftJoin('m_prodi', 'm_semhas.prodi_id', '=', 'm_prodi.prodi_id')
        //     ->selectRaw('m_semhas.semhas_id, m_semhas.judul_semhas, m_semhas.gelombang, m_semhas.kuota_bimbingan, 
        //      m_semhas.tanggal_mulai_pendaftaran, m_semhas.tanggal_akhir_pendaftaran,
        //      m_prodi.prodi_name')
        //     ->where(function ($query) use ($prodi_id) {
        //         $query->where('m_semhas.prodi_id', $prodi_id)
        //             ->orWhereNull('m_semhas.prodi_id');
        //     })
        //     ->get();
        // $dataall = SemhasModel::all();
        // dd($dataall);
        $prodi_id = auth()->user()->prodi_id;

        $data = SemhasModel::select(
            'm_semhas.semhas_id',
            'm_semhas.judul_semhas',
            'm_semhas.gelombang',
            'm_semhas.kuota_bimbingan',
            'm_semhas.tanggal_mulai_pendaftaran',
            'm_semhas.tanggal_akhir_pendaftaran',
            'm_prodi.prodi_name'
        )
            ->leftJoin('m_prodi', 'm_semhas.prodi_id', '=', 'm_prodi.prodi_id');

        if ($prodi_id !== null) {
            // Ketika user_id tidak null
            $data->where(function ($query) use ($prodi_id) {
                // Filter data yang sesuai dengan prodi_id pengguna atau yang prodi_id-nya null
                $query->where('m_prodi.prodi_id', $prodi_id)
                    ->orWhereNull('m_prodi.prodi_id');
            });
        }

        $data = $data->get();

        // dd($data);

        // $data = SemhasModel::leftJoin('m_prodi', function ($join) use ($prodi_id) {
        //     $join->on('m_semhas.prodi_id', '=', 'm_prodi.prodi_id')
        //         ->where(function ($query) use ($prodi_id) {
        //             $query->where('m_semhas.prodi_id', $prodi_id)
        //                 ->orWhereNull('m_semhas.prodi_id');
        //         });
        // })
        //     ->selectRaw('m_semhas.semhas_id, m_semhas.judul_semhas, m_semhas.gelombang, m_semhas.kuota_bimbingan, 
        //  m_semhas.tanggal_mulai_pendaftaran, m_semhas.tanggal_akhir_pendaftaran,
        //  m_prodi.prodi_name')
        //     ->get();




        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function create()
    {
        $this->authAction('create', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $page = [
            'url' => $this->menuUrl,
            'title' => 'Tambah ' . $this->menuTitle
        ];

        $prodis = ProdiModel::selectRaw("prodi_id, prodi_name, prodi_code")->get();

        return view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('prodis', $prodis);
    }

    public function store(Request $request)
    {
        $this->authAction('create', 'json');

        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'judul_semhas' => 'required|string|max:100',
                'gelombang' => 'required|integer',
                'kuota_bimbingan' => 'required|integer',
                'tanggal_mulai_pendaftaran' => 'required|date',
                'tanggal_akhir_pendaftaran' => 'required|date',
                'prodi_id' => 'required',
                // Add other rules for DosenModel fields
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'stat' => false,
                    'mc' => false,
                    'msg' => 'Terjadi kesalahan.',
                    'msgField' => $validator->errors()
                ]);
            }
            $Semhas = SemhasModel::insertData($request);

            return response()->json([
                'stat' => $Semhas,
                'mc' => $Semhas,
                'msg' => ($Semhas) ? $this->getMessage('insert.success') : $this->getMessage('insert.failed')
            ]);
        }

        return redirect('/');
    }


    public function edit($id)
    {
        $this->authAction('update', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $page = [
            'url' => $this->menuUrl . '/' . $id,
            'title' => 'Edit ' . $this->menuTitle
        ];

        $prodis = ProdiModel::selectRaw("prodi_id, prodi_name, prodi_code")->get();
        $data = SemhasModel::find($id);

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data)
            ->with('prodis', $prodis);
    }

    public function update(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'judul_semhas' => 'required|string|max:100',
                'gelombang' => 'required|integer',
                'kuota_bimbingan' => 'required|integer',
                'tanggal_mulai_pendaftaran' => 'required|date',
                'tanggal_akhir_pendaftaran' => 'required|date',
                'prodi_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'stat'     => false,
                    'mc'       => false,
                    'msg'      => 'Terjadi kesalahan.',
                    'msgField' => $validator->errors()
                ]);
            }

            $res = SemhasModel::updateData($id, $request);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? $this->getMessage('update.success') : $this->getMessage('update.failed')
            ]);
        }

        return redirect('/');
    }

    public function show($id)
    {
        $this->authAction('read', 'modal');
        // if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = SemhasModel::find($id);
        $page = [
            'title' => 'Detail ' . $this->menuTitle
        ];

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'detail')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data);
    }

    public function confirm($id)
    {
        $this->authAction('delete', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = SemhasModel::find($id);

        return (!$data) ? $this->showModalError() :
            $this->showModalConfirm($this->menuUrl . '/' . $id, [
                'Judul Semhas' => $data->judul_semhas,
                'Tanggal Mulai Pendftaran' => $data->tanggal_mulai_pendaftaran,
                'Tanggal Akhir Pendftaran' => $data->tanggal_akhir_pendaftaran,
                'Prodi' => $data->prodi->prodi_name,
            ]);
    }

    public function destroy(Request $request, $id)
    {
        $this->authAction('delete', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $res = SemhasModel::deleteData($id);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => DosenModel::getDeleteMessage()
            ]);
        }

        return redirect('/');
    }
}

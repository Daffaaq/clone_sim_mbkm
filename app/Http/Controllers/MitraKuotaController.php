<?php

namespace App\Http\Controllers;

use App\Models\Master\ProdiModel;
use App\Models\MitraKuotaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MitraKuotaController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'MITRA';
        $this->menuUrl   = url('mitra');     // set URL untuk menu ini
        $this->menuTitle = 'Mitra Kuota';                       // set nama menu
        $this->viewPath  = 'kuota.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index($id)
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Mitra']
        ];

        $activeMenu = [
            'l1' => 'mitra',
            'l2' => null,
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl . '/' . $id . '/kuota',
            'title' => 'Daftar ' . $this->menuTitle
        ];

        return view($this->viewPath . 'index')
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $activeMenu)
            ->with('page', (object) $page)
            ->with('allowAccess', $this->authAccessKey());
    }

    public function list(Request $request, $id)
    {
        $this->authAction('read', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data  = MitraKuotaModel::with('prodi')->where('mitra_id', $id)->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }


    public function create($id)
    {
        $this->authAction('create', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $page = [
            'url' => $this->menuUrl . '/' . $id . '/kuota',
            'title' => 'Tambah ' . $this->menuTitle
        ];

        $prodis = ProdiModel::all();


        return view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('prodis', $prodis);
    }


    public function store(Request $request, $id)
    {
        $this->authAction('create', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
                'prodi_id' => 'required',
                'kuota' => 'required|numeric',

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

            // $random_username = 'per_' . rand(100000, 999999);
            // $user = [
            //     'username' => $random_username,
            //     'name' => $request->nama_perusahaan,
            //     'password' => Hash::make($random_username),
            //     'group_id' => 5,
            //     'is_active' => 1,
            //     'email' => $request->email,
            // ];
            // $insert = UserModel::create($user);

            // $request['user_id'] = $insert->user_id;

            $request['mitra_id'] = $id;
            $res = MitraKuotaModel::insertData($request);



            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? $this->getMessage('insert.success') : $this->getMessage('insert.failed')
            ]);
        }

        return redirect('/');
    }

    public function edit($mitra_id, $id)
    {
        $this->authAction('update', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $page = [
            'url' => $this->menuUrl . '/' . $mitra_id . '/kuota' . '/' . $id,
            'title' => 'Edit ' . $this->menuTitle
        ];

        $data = MitraKuotaModel::find($id);

        // dd($data, $id);

        $prodis = ProdiModel::all();

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('prodis', $prodis)
            ->with('data', $data);
    }


    public function update(Request $request, $mitra_id, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
                'prodi_id' => 'required',
                'kuota' => 'required|numeric',
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

            $res = MitraKuotaModel::updateData($id, $request);

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
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = MitraKuotaModel::find($id);
        $page = [
            'title' => 'Detail ' . $this->menuTitle
        ];

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'detail')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data);
    }


    public function confirm($mitra_id, $id)
    {
        $this->authAction('delete', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = MitraKuotaModel::with('mitra')->find($id);

        return (!$data) ? $this->showModalError() :
            $this->showModalConfirm($this->menuUrl . '/' . $mitra_id . '/kuota/' . $id, [
                'Nama' => $data->mitra->mitra_nama,
                'Prodi' => $data->prodi->prodi_name,
                'Kuota' => $data->kuota,
            ]);
    }

    public function destroy(Request $request, $mitra_id, $id)
    {
        $this->authAction('delete', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $res = MitraKuotaModel::deleteData($id);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => MitraKuotaModel::getDeleteMessage()
            ]);
        }

        return redirect('/');
    }
}

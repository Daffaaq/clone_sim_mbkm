<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Master\MahasiswaModel;
use App\Models\Master\ProdiModel;
use App\Models\Setting\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MahasiswaController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'TRANSACTION.MAHASISWA';
        $this->menuUrl   = url('transaksi/mahasiswa');     // set URL untuk menu ini
        $this->menuTitle = 'Mahasiswa';                       // set nama menu
        $this->viewPath  = 'transaction.mahasiswa.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Transaksi', 'Mahasiswa']
        ];

        $activeMenu = [
            'l1' => 'transaction',
            'l2' => 'transaksi-mahasiswa',
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl,
            'title' => 'Daftar ' . $this->menuTitle
        ];

        $prodis = ProdiModel::select('prodi_id', 'prodi_name', 'prodi_code')->get();

        return view($this->viewPath . 'index')
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $activeMenu)
            ->with('page', (object) $page)
            ->with('prodis', $prodis)
            ->with('allowAccess', $this->authAccessKey());
    }

    public function list(Request $request)
    {
        $this->authAction('read', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data  = MahasiswaModel::selectRaw("mahasiswa_id, prodi_id, user_id, nim, nama_mahasiswa, email_mahasiswa, no_hp, jenis_kelamin, kelas, nama_ortu, hp_ortu")
            ->with('prodi:prodi_id,prodi_id,prodi_name,prodi_code');

        if ($request->prodi_id) {
            $data->where('prodi_id', $request->prodi_id);
        }

        $group_id = Auth::user()->group_id;
        if ($group_id == 2) {
            $data->where('prodi_id', Auth::user()->prodi_id);
        }
        //append provinsi and kota to $data with value "dummy"

        // dd($data);

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

        $prodis = ProdiModel::select('prodi_id', 'prodi_name', 'prodi_code')->get();

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
                'prodi_id' => 'required',
                'nim'  => 'required',
                'nama_mahasiswa' => 'required',
                'email_mahasiswa' => 'required',
                'no_hp' => 'required',
                'jenis_kelamin' => 'required',
                'kelas' => 'required',
            ];

            //if group == 2 then remove prodi_id from request
            if (Auth::user()->group_id == 2) {
                unset($rules['prodi_id']);
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'stat'     => false,
                    'mc'       => false,
                    'msg'      => 'Terjadi kesalahan.',
                    'msgField' => $validator->errors()
                ]);
            }

            $user = [
                'username' => $request->nim,
                'name' => $request->nama_mahasiswa,
                'password' => Hash::make($request->nim),
                'group_id' => 4,
                'is_active' => 1,
                'email' => $request->email_mahasiswa,
            ];
            $insert = UserModel::create($user);

            $request['user_id'] = $insert->user_id;

            if (Auth::user()->group_id == 2) {
                $request['prodi_id'] = Auth::user()->prodi_id;
            }

            $res = MahasiswaModel::insertData($request);



            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? $this->getMessage('insert.success') : $this->getMessage('insert.failed')
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

        $data = MahasiswaModel::find($id);

        $prodis = ProdiModel::select('prodi_id', 'prodi_name', 'prodi_code')->get();

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
                'prodi_id' => 'required',
                'nim'  => 'required',
                'nama_mahasiswa' => 'required',
                'email_mahasiswa' => 'required',
                'no_hp' => 'required',
                'jenis_kelamin' => 'required',
                'kelas' => 'required',
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

            $res = MahasiswaModel::updateData($id, $request);

            $id_mahasiswa = MahasiswaModel::where('mahasiswa_id', $id)->first();

            $res_user = UserModel::where('user_id', $id_mahasiswa->user_id)->update([
                'username' => $request->nim,
                'name' => $request->nama_mahasiswa,
                'email' => $request->email_mahasiswa,
                'password' => Hash::make($request->nim),
            ]);

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

        $data = MahasiswaModel::find($id);
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

        $data = MahasiswaModel::find($id);

        return (!$data) ? $this->showModalError() :
            $this->showModalConfirm($this->menuUrl . '/' . $id, [
                'Nama' => $data->nama_mahasiswa,
                'NIM' => $data->nim,
                'Prodi' => $data->prodi->prodi_name,
                'Email' => $data->email_mahasiswa,
                'No HP' => $data->no_hp,
            ]);
    }

    public function destroy(Request $request, $id)
    {
        $this->authAction('delete', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $res = MahasiswaModel::deleteData($id);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => MahasiswaModel::getDeleteMessage()
            ]);
        }

        return redirect('/');
    }
}

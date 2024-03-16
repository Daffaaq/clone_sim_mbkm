<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\InstrukturModel;
use App\Models\Setting\UserModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class InstrukturController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'MASTER.INSTRUKTUR';
        $this->menuUrl   = url('master/instruktur');     // set URL untuk menu ini
        $this->menuTitle = 'Instruktur';                       // set nama menu
        $this->viewPath  = 'master.instruktur.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Data Master', 'Instruktur']
        ];

        $activeMenu = [
            'l1' => 'master',
            'l2' => 'master-instruktur',
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl,
            'title' => 'Daftar ' . $this->menuTitle
        ];
        // dd($instruktur);
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

        $data  = InstrukturModel::selectRaw("instruktur_id, nama_instruktur, instruktur_email, instruktur_phone");

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

        $instruktur = InstrukturModel::selectRaw("instruktur_id, nama_instruktur, instruktur_email")->get();

        return view($this->viewPath . 'action')
            ->with('instruktur', $instruktur)
            ->with('page', (object) $page);
    }


    public function store(Request $request)
    {
        $this->authAction('create', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
                'instruktur_email' => ['required', 'email:rfc,dns,filter', 'max:20',],
                'nama_instruktur' => 'required|string|max:100',
                'instruktur_phone' => 'required|string|max:15',
                'password' => 'required|string|min:8',
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

            // Create user
            $user = [
                'username' => $request->input('instruktur_email'),
                'name' => $request->input('nama_instruktur'),
                'password' => Hash::make($request->input('password')),
                'group_id' => 5,
                'is_active' => 1,
                'email' => $request->input('instruktur_email'),
            ];
            $insert = UserModel::create($user);
            $request['user_id'] = $insert->user_id;
            $res = InstrukturModel::create([
                'instruktur_email' => $request->input('instruktur_email'),
                'nama_instruktur' => $request->input('nama_instruktur'),
                'instruktur_phone' => $request->input('instruktur_phone'),
                'username' => $request->input('instruktur_email'),
                'password' => Hash::make($request->input('password')),
                'user_id' => $insert->user_id,
            ]);

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

        $data = InstrukturModel::find($id);

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data);
    }


    public function update(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
                'instruktur_email' => [
                    'sometimes',
                    'email:rfc,dns,filter',
                    'max:20',
                    Rule::unique('m_instruktur', 'instruktur_email')->ignore($id, 'instruktur_id'),
                ],
                'nama_instruktur' => 'required|string|max:100',
                'instruktur_phone' => 'required|string|max:15',
                'password' => 'required|string|min:8',
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
            if ($request->has('instruktur_email')) {
                // Cek apakah pengguna dengan email tersebut sudah ada
                $existingUser = UserModel::where('email', $request->input('instruktur_email'))->first();

                if ($existingUser) {
                    // Jika email sudah ada, update data pengguna yang sudah ada
                    $existingUser->update([
                        'name' => $request->input('nama_instruktur'),
                    ]);
                    $request['user_id'] = $existingUser->user_id;
                } else {
                    // Jika email belum ada, abaikan pembuatan pengguna baru
                    unset($request['user_id']);
                }
            }

            $res = InstrukturModel::updateData($id, $request);

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

        $data = InstrukturModel::find($id);
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

        $data = InstrukturModel::find($id);

        return (!$data) ? $this->showModalError() :
            $this->showModalConfirm($this->menuUrl . '/' . $id, [
                'Kode' => $data->jurusan_code,
                'Jurusan' => $data->jurusan_name,
            ]);
    }

    public function destroy(Request $request, $id)
    {
        $this->authAction('delete', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $res = InstrukturModel::deleteData($id);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => InstrukturModel::getDeleteMessage()
            ]);
        }

        return redirect('/');
    }
}

<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\JurusanModel;
use App\Models\Master\ProdiModel;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdiController extends Controller
{
    public function __construct(){
        $this->menuCode  = 'MASTER.PRODI';
        $this->menuUrl   = url('master/prodi');     // set URL untuk menu ini
        $this->menuTitle = 'Program Studi';                       // set nama menu
        $this->viewPath  = 'master.prodi.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index(){
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Data Master', 'Program Studi']
        ];

        $activeMenu = [
            'l1' => 'master',
            'l2' => 'master-prodi',
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl,
            'title' => 'Daftar '. $this->menuTitle
        ];

        return view($this->viewPath . 'index')
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $activeMenu)
            ->with('page', (object) $page)
            ->with('allowAccess', $this->authAccessKey());
    }

    public function list(Request $request){
        $this->authAction('read', 'json');
        if($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data  = ProdiModel::selectRaw("prodi_id, prodi_name, prodi_code, is_active");

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }


    public function create(){
        $this->authAction('create', 'modal');
        if($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $page = [
            'url' => $this->menuUrl,
            'title' => 'Tambah ' . $this->menuTitle
        ];

        $jurusan = JurusanModel::selectRaw("jurusan_id, jurusan_name, jurusan_code")->get();

        return view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('jurusan', $jurusan);
    }


    public function store(Request $request){
        $this->authAction('create', 'json');
        if($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
                'prodi_code' => ['required', 'string', 'max:10', ProdiModel::setUniqueInsert()],
                'prodi_name' => 'required|string|max:100',
                'jurusan_id' => 'required|integer',
                'is_active' => 'required|in:0,1',
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

            $res = ProdiModel::insertData($request);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res)? $this->getMessage('insert.success') : $this->getMessage('insert.failed')
            ]);

        }

        return redirect('/');
    }

    public function edit($id){
        $this->authAction('update', 'modal');
        if($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $page = [
            'url' => $this->menuUrl . '/'.$id,
            'title' => 'Edit ' . $this->menuTitle
        ];

        $data = ProdiModel::find($id);
        $jurusan = JurusanModel::selectRaw("jurusan_id, jurusan_name, jurusan_code")->get();

        return (!$data)? $this->showModalError() :
            view($this->viewPath . 'action')
                ->with('page', (object) $page)
                ->with('id', $id)
                ->with('data', $data)
                ->with('jurusan', $jurusan);
    }


    public function update(Request $request, $id){
        $this->authAction('update', 'json');
        if($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
                'prodi_code' => ['required', 'string', 'max:10', ProdiModel::setUniqueInsertIgnore($id)],
                'prodi_name' => 'required|string|max:100',
                'jurusan_id' => 'required|integer',
                'is_active' => 'required|in:0,1',
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

            $res = ProdiModel::updateData($id, $request);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res)? $this->getMessage('update.success') : $this->getMessage('update.failed')
            ]);
        }

        return redirect('/');
    }

    public function show($id){
        $this->authAction('read', 'modal');
        if($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = ProdiModel::find($id);
        $page = [
            'title' => 'Detail ' . $this->menuTitle
        ];

        return (!$data)? $this->showModalError() :
            view($this->viewPath . 'detail')
                ->with('page', (object) $page)
                ->with('id', $id)
                ->with('data', $data);
    }


    public function confirm($id){
        $this->authAction('delete', 'modal');
        if($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = ProdiModel::find($id);

        return (!$data)? $this->showModalError() :
            $this->showModalConfirm($this->menuUrl.'/'.$id, [
                'Kode Prodi' => $data->prodi_code,
                'Nama Prodi' => $data->prodi_name,
            ]);
    }

    public function destroy(Request $request, $id){
        $this->authAction('delete', 'json');
        if($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $res = ProdiModel::deleteData($id);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ProdiModel::getDeleteMessage()
            ]);
        }

        return redirect('/');
    }
}

<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting\GroupModel;
use App\Models\Setting\UserModel;
use App\Models\View\CustomerListView;
use App\Models\View\UserView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use function redirect;
use function response;
use function url;
use function view;


class UserController extends Controller
{

    public function __construct()
    {
        $this->menuCode  = 'SETTING.USER';                // kode menu, sesuai dengan code di DB
        $this->menuUrl   = url('setting/user');     // set URL untuk menu ini
        $this->menuTitle = 'Setting - Data Pengguna';                 // set nama menu
        $this->viewPath  = 'setting.user.';    // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        // untuk set breadcrumb pada halaman web
        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Setting', 'Data Pengguna']
        ];

        // untuk set aktif menu pada sidebar
        $activeMenu = [
            'l1' => 'setting',           // menu aktif untuk level 1, berdasarkan class_tag pada tabel s_menu
            'l2' => 'setting-user',      // menu aktif untuk level 2, berdasarkan class_tag pada tabel s_menu
            'l3' => null                    // menu aktif untuk level 3, berdasarkan class_tag pada tabel s_menu
        ];

        // untuk set konten halaman web
        $page = [
            'url' => $this->menuUrl,
            'title' => $this->menuTitle
        ];

        $group      = GroupModel::selectRaw('group_id, group_code, group_name')->where('is_active', 1)->get();

        return view($this->viewPath . 'index')
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $activeMenu)
            ->with('page', (object) $page)
            ->with('group', $group)
            ->with('allowAccess', $this->authAccessKey());
    }

    public function list(Request $request)
    {
        $this->authAction('read', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data  = UserModel::with('role')->orderBy('user_id', 'asc');

        if (!empty($request->input('filter_group'))) {
            $data->where('group_id', $request->input('filter_group'));
        }

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }


    public function create()
    {
        $this->authAction('create', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        // untuk set konten halaman web
        $page = [
            'url' => $this->menuUrl,
            'title' => 'Tambah ' . $this->menuTitle
        ];

        $group      = GroupModel::selectRaw('group_id, group_code, group_name')->where('is_active', 1)->get();

        return view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('group', $group);
    }


    public function store(Request $request)
    {
        $this->authAction('create', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        // cek untuk Insert/Update/Delete harus via AJAX
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'username' => ['required', 'string', 'max:10', Rule::unique('s_user', 'username')],
                'name' => 'required|max:50',
                'group_id' => 'required|integer',
                'email' => 'nullable|email|max:50',
                'password' => 'required|string|min:6',
                'is_active'      => 'required|integer',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'stat'     => false,                    // respon json, true: berhasil, false: gagal
                    'mc'       => false,                    // jika diset true, maka respon json akan membuat popup modal menutup/close
                    'msg'      => 'Terjadi kesalahan.',
                    'msgField' => $validator->errors()      // menunjukkan field mana yang error
                ]);
            }

            $res = UserModel::InsertData($request);

            return response()->json([
                'stat' => $res,
                'mc' => false, // close modal
                'msg' => ($res) ? $this->getMessage('insert.success') : $this->getMessage('insert.failed')
            ]);
        }

        return redirect('/');
    }


    // Fungsi show disini diabaikan dulu untuk setting, karena akan digunakan untuk detail data
    public function show($id)
    {
        $this->authAction('read', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        // untuk set konten halaman web
        $page = [
            'title' => 'Detail ' . $this->menuTitle
        ];

        $data  = UserView::where('user_id', $id)->first();

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'detail')
            ->with('page', (object) $page)
            ->with('data', $data);
    }


    public function edit($id)
    {
        $this->authAction('update', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        // untuk set konten halaman web
        $page = [
            'url' => $this->menuUrl . '/' . $id,
            'title' => 'Edit ' . $this->menuTitle
        ];

        $data  = UserView::where('user_id', $id)->first();
        if (!$data) return $this->showModalError();

        if ($data->group_id == 1) return $this->showModalError('Kesalahan', 'Terjadi Kesalahan!!!', 'Data level Admin tidak bisa diedit.');

        $group = GroupModel::selectRaw('group_id, group_code, group_name')->where('is_active', 1)->get();

        return view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('data', $data)
            ->with('group', $group)
            ->with('allowAccess', $this->authAccessKey());
    }


    public function update(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        // cek untuk Insert/Update/Delete harus via AJAX
        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
                'username' => ['required', 'min:4', 'max:20', Rule::unique('s_user')->ignore($id, 'user_id')],
                'name' => 'required|max:50',
                'group_id' => 'required|integer',
                'email' => 'nullable|email|max:50',
                'password' => 'nullable|string|min:6',
                'is_active'      => 'required|integer',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'stat'     => false,
                    'mc'       => false,            // jika diset true, maka respon json akan membuat popup modal menutup/close
                    'msg'      => 'Terjadi kesalahan.',
                    'msgField' => $validator->errors()
                ]);
            }

            $data  = UserView::where('user_id', $id)->first();
            if (!$data or $data->group_id == 1) {
                return response()->json([
                    'stat' => false,
                    'mc' => false, // close modal
                    'msg' => $this->getMessage('update.failed')
                ]);
            }

            $res = UserModel::UpdateData($id, $request);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? $this->getMessage('insert.success') : $this->getMessage('insert.failed')
            ]);
        }

        return redirect('/');
    }


    public function confirm($id)
    {
        $this->authAction('delete', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = UserView::where('user_id', $id)->first();
        if (!$data) return $this->showModalError();

        if ($data->group_id == 1) return $this->showModalError('Kesalahan', 'Terjadi Kesalahan!!!', 'Data level Admin tidak bisa dihapus.');


        return $this->showModalConfirm($this->menuUrl . '/' . $id, [
            'Username' => $data->username,      // tampilkan data-data yang ditampilkan (untuk dihapus)
            'Name' => $data->name,
            'Group' => $data->group_name,
            'Email' => $data->email,
            'HP' => $data->hp,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $this->authAction('delete', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        // cek untuk Insert/Update/Delete harus via AJAX
        if ($request->ajax() || $request->wantsJson()) {
            $data  = UserView::where('user_id', $id)->first();
            if (!$data or $data->group_id == 1) {
                return response()->json([
                    'stat' => false,
                    'mc' => false, // close modal
                    'msg' => $this->getMessage('delete.failed')
                ]);
            }

            $res = UserModel::DeleteData($id);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? $this->getMessage('delete.success') : $this->getMessage('delete.failed')
            ]);
        }

        return redirect('/');
    }
}

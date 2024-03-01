<?php

namespace App\Http\Controllers\Setting;

use function url;
use function view;
use function redirect;
use function response;
use Illuminate\Http\Request;
use App\Models\View\UserView;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use App\Models\Setting\UserModel;
use App\Models\Setting\GroupModel;
use App\Http\Controllers\Controller;
use App\Models\View\CustomerListView;
use App\Models\Setting\GroupMenuModel;
use Illuminate\Support\Facades\Validator;
use PHPUnit\TextUI\XmlConfiguration\Group;


class GroupController extends Controller
{

    public function __construct()
    {
        $this->menuCode  = 'SETTING.GROUP';                // kode menu, sesuai dengan code di DB
        $this->menuUrl   = url('setting/group');     // set URL untuk menu ini
        $this->menuTitle = 'Group Pengguna';                 // set nama menu
        $this->viewPath  = 'setting.group.';    // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        // untuk set breadcrumb pada halaman web
        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Setting', 'Group Pengguna']
        ];

        // untuk set aktif menu pada sidebar
        $activeMenu = [
            'l1' => 'setting',           // menu aktif untuk level 1, berdasarkan class_tag pada tabel s_menu
            'l2' => 'setting-group',      // menu aktif untuk level 2, berdasarkan class_tag pada tabel s_menu
            'l3' => null                    // menu aktif untuk level 3, berdasarkan class_tag pada tabel s_menu
        ];

        // untuk set konten halaman web
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

        $data  = GroupModel::selectRaw('group_id, group_code, group_name, is_active');

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

        return view($this->viewPath . 'action')
            ->with('page', (object) $page);
    }


    public function store(Request $request)
    {
        $this->authAction('create', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        // cek untuk Insert/Update/Delete harus via AJAX
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'group_code' => 'required|string|min:3|max:10|unique:App\Models\Setting\GroupModel',
                'group_name' => 'required|string|max:20',
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

            $res = GroupModel::InsertData($request);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
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
            'url' => $this->menuUrl . '/' . $id . '/menu',
            'title' => 'Detail Hak Akses'
        ];

        $data  = GroupModel::find($id);
        if (!$data) return $this->showModalError();

        $menu = GroupMenuModel::getMenuMap($data->group_id);
        // dd($menu);

        return view($this->viewPath . 'menu')
            ->with('page', (object) $page)
            ->with('data', $data)
            ->with('menu', $menu);
    }

    public function menu_save(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        // cek untuk Insert/Update/Delete harus via AJAX
        if ($request->ajax() || $request->wantsJson()) {

            $res = GroupMenuModel::setGroupMenu($id, $request);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? $this->getMessage('update.success') : $this->getMessage('update.failed')
            ]);
        }

        return redirect('/');
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

        $data  = GroupModel::find($id);
        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('data', $data)
            ->with('allowAccess', $this->authAccessKey());
    }


    public function update(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        // cek untuk Insert/Update/Delete harus via AJAX
        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
                'group_code' => ['required', 'max:10', Rule::unique('s_group')->ignore($id, 'group_id')],
                'group_name' => 'required|string|max:20',
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

            $res = GroupModel::UpdateData($id, $request);

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

        $data  = GroupModel::find($id);
        return (!$data) ? $this->showModalError() :
            $this->showModalConfirm($this->menuUrl . '/' . $id, [
                'Group Code' => $data->group_code,      // tampilkan data-data yang ditampilkan (untuk dihapus)
                'Name' => $data->group_name
            ]);
    }

    public function destroy(Request $request, $id)
    {
        $this->authAction('delete', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        // cek untuk Insert/Update/Delete harus via AJAX
        if ($request->ajax() || $request->wantsJson()) {

            $res = GroupModel::deleteData($id);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? $this->getMessage('delete.success') : $this->getMessage('delete.failed')
            ]);
        }

        return redirect('/');
    }
}

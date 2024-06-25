<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use App\Models\View\MenuView;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use App\Models\Setting\MenuModel;
use App\Http\Controllers\Controller;
use App\Models\View\PeriodeRangeView;
use Illuminate\Support\Facades\Validator;


class MenuController extends Controller
{

    public function __construct()
    {
        $this->menuCode  = 'SETTING.MENU';
        $this->menuUrl   = url('setting/menu');     // set URL untuk menu ini
        $this->menuTitle = 'Pengaturan - Menu';                      // set nama menu
        $this->viewPath  = 'setting.menu.';      // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        // untuk set breadcrumb pada halaman web
        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Pengaturan', 'Menu']
        ];

        // untuk set aktif menu pada sidebar
        $activeMenu = [
            'l1' => 'setting',            // menu aktif untuk level 1, berdasarkan class yang ada di sidebar
            'l2' => 'setting-menu',    // menu aktif untuk level 2, berdasarkan class yang ada di sidebar
            'l3' => null                    // menu aktif untuk level 3, berdasarkan class yang ada di sidebar
        ];

        // untuk set konten halaman web
        $page = [
            'url' => $this->menuUrl,
            'title' => 'Daftar ' . $this->menuTitle
        ];

        $parent = MenuModel::whereNull('parent_id')
            ->selectRaw('menu_id as id, menu_code as code, menu_name as name')
            ->orderBy('order_no')
            ->get();

        return view($this->viewPath . 'index')
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $activeMenu)
            ->with('page', (object) $page)
            ->with('parent', $parent)
            ->with('allowAccess', $this->authAccessKey());
    }

    public function list(Request $request)
    {
        $this->authAction('read', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        // $data  = MenuView::selectRaw('menu_id, menu_code, menu_name, menu_url, menu_level, order_no, is_active, parent_code');
        // $data  = MenuModel::selectRaw('menu_id, menu_code, menu_name, menu_url, menu_level, order_no, is_active');

        $data = MenuModel::with('parent')->where('deleted_at', null);

        if ($request->level != null) {
            $data->where('menu_level', $request->level);
        }

        if ($request->parent != null) {
            $data->where('parent_id', $request->parent);
        }

        $data->get();

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

        $menu = MenuModel::whereNull('parent_id')
            ->orwhereNull('menu_url')
            ->orderBy('order_no')
            ->get();

        return view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('menu', $menu);
    }


    public function store(Request $request)
    {
        $this->authAction('create', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        // cek untuk Insert/Update/Delete harus via AJAX
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'menu_code' => ['required', 'string', 'min:3', 'max:50', MenuModel::setUniqueInsert()],
                'menu_name' => ['required', 'string', 'min:3', 'max:100'],
                'menu_level' => ['required', 'integer', 'between:1,3'],
                'order_no' => ['required', 'integer', 'between:1,100'],
                'class_tag' => ['required', 'string', 'min:4', 'max:20'],
                'icon' => ['required', 'string', 'min:5', 'max:50'],
                'is_active' => ['required'],
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

            $res = MenuModel::InsertData($request);

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

        $menu = MenuModel::whereNull('parent_id')
            ->orwhereNull('menu_url')
            ->orderBy('order_no')
            ->get();

        $data = MenuModel::find($id);

        // untuk set konten halaman web
        $page = [
            'url' => $this->menuUrl . '/' . $id,
            'title' => 'Ubah ' . $this->menuTitle
        ];

        return view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('menu', $menu)
            ->with('data', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
                'menu_code' => ['required', 'string', 'min:3', 'max:50', Rule::unique('s_menu')->ignore($id, 'menu_id')],
                'menu_name' => ['required', 'string', 'min:3', 'max:100'],
                'menu_level' => ['required', 'integer', 'between:1,3'],
                'order_no' => ['required', 'integer', 'between:1,100'],
                'class_tag' => ['required', 'string', 'min:4', 'max:20'],
                'icon' => ['required', 'string', 'min:5', 'max:50'],
                'is_active' => ['required'],
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

            $res = MenuModel::updateData($id, $request);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? $this->getMessage('update.success') : $this->getMessage('update.failed')
            ]);
        }

        return redirect('/');
    }

    public function confirm($id)
    {
        $this->authAction('delete', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data  = MenuModel::find($id);
        return (!$data) ? $this->showModalError() :
            $this->showModalConfirm($this->menuUrl . '/' . $id, [
                'Lingkup' => $data->menu_scope,
                'Kode' => $data->menu_code,
                'Nama' => $data->menu_name,
                'URL' => $data->menu_url,
                'Level' => $data->menu_level,
                'Urutan' => $data->order_no,
                'Aktif' => $data->is_active == 1 ? 'Aktif' : 'Tidak Aktif',
            ]);
    }

    public function destroy(Request $request, $id)
    {
        $this->authAction('delete', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        // cek untuk Insert/Update/Delete harus via AJAX
        if ($request->ajax() || $request->wantsJson()) {

            $res = MenuModel::deleteData($id);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? $this->getMessage('delete.success') : $this->getMessage('delete.failed')
            ]);
        }

        return redirect('/');
    }


    // public function set_active(Request $request, $id)
    // {
    //     $this->authAction('update', 'json');
    //     if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

    //     // cek untuk Insert/Update/Delete harus via AJAX
    //     if ($request->ajax() || $request->wantsJson()) {

    //         // validasinya tidak usah dibuatkan class sendiri, biar disini saja
    //         // karena validasi untuk insert dan update bisa berbeda
    //         $rules = [
    //             'periode_id'      => 'required|numeric|between:202301,203012'
    //         ];

    //         $validator = Validator::make($request->all(), $rules);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'stat'     => false,
    //                 'mc'       => false,            // jika diset true, maka respon json akan membuat popup modal menutup/close
    //                 'msg'      => 'Terjadi kesalahan.',
    //                 'msgField' => $validator->errors()
    //             ]);
    //         }

    //         // update data via function yg ada di model
    //         $res = MenuModel::updateData($id, $request);

    //         if ($res->status) {
    //             $periode_active = MenuModel::where('is_active', 1)
    //                 ->selectRaw('periode_id, periode_name')
    //                 ->first();

    //             session()->put('periode_active', $periode_active);

    //             $periode = PeriodeRangeView::all();
    //             session()->put('periode', $periode);
    //         }

    //         return response()->json([
    //             'stat' => $res->status,
    //             'msg' => $res->message
    //         ]);
    //     }

    //     return redirect('/');
    // }
}

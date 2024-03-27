<?php

namespace App\Http\Controllers\Transaction;

use App\Exports\MahasiswaExport;
use App\Http\Controllers\Controller;
use App\Imports\MahasiswaImport;
use App\Models\Master\MahasiswaModel;
use App\Models\Master\PeriodeModel;
use App\Models\Master\ProdiModel;
use App\Models\Setting\UserModel;
use App\Models\Transaction\Magang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
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

    public function cari($nim)
    {
        $current_mahasiswa = MahasiswaModel::where('user_id', Auth::user()->user_id)->first();
        $id_prodi = $current_mahasiswa->prodi_id;

        $data = MahasiswaModel::where('nim', $nim)->first();

        if (!$data) {
            return response()->json([
                'stat' => false,
                'mc' => false, // close modal
                'msg' => 'Mahasiswa tidak ditemukan',
            ]);
        }

        //check if $data->prodi_id != $id_prodi
        if ($data->prodi_id != $id_prodi) {
            return response()->json([
                'stat' => false,
                'mc' => false, // close modal
                'msg' => 'Mahasiswa beda prodi',
            ]);
        }

        $check = Magang::where('mahasiswa_id', $data->mahasiswa_id)->where('is_accept', '!=', 2)->count();

        if ($check > 0) {
            return response()->json([
                'stat' => false,
                'mc' => false, // close modal
                'msg' => 'Mahasiswa sudah mendaftar',
            ]);
        }


        return response()->json([
            'stat' => true,
            'mc' => false, // close modal
            'msg' => 'Data ditemukan',
            'data' => $data,
        ]);
    }

    public function export(Request $request)
    {

        $active_periode = PeriodeModel::where('is_current', 1)->first();

        $data  = MahasiswaModel::with('prodi');


        if ($request->prodi_id) {
            $data->where('prodi_id', $request->prodi_id);
        }

        if (auth()->user()->group_id == 1) {
            $data = $data->get();
        } else if (auth()->user()->group_id == 4) {
            $data = $data->where('mahasiswa_id', auth()->user()->getUserMahasiswa->mahasiswa_id)->get();
        } else {
            $prodi_id = auth()->user()->getProdiId();
            $data = $data->where('prodi_id', $prodi_id)->get();
        }

        $data = $data->map(function ($item) use ($active_periode) {
            $magang = Magang::with('mitra')
                ->with('mitra.kegiatan')
                ->where('mahasiswa_id', $item->mahasiswa_id)
                ->where('periode_id', $active_periode->periode_id)
                ->latest()
                ->first();

            if (!$magang) {
                $status_magagng = "Belum Terdaftar";
            } else {
                if ($magang->magang_tipe == "1") {
                    if ($magang->is_accept == "2") {
                        $status_magagng = "Belum Terdaftar";
                    } else {
                        if ($magang->status == 1) {
                            $status_magagng = "Diterima";
                        } elseif ($magang->status == 0) {
                            $status_magagng = "Terdaftar";
                        } elseif ($magang->status == 2) {
                            $status_magagng = "Belum Terdaftar";
                        } elseif ($magang->status == 3) {
                            $status_magagng = "Terdaftar";
                        }
                    }
                } else {
                    if ($magang->status == 1) {
                        $status_magagng = "Diterima";
                    } elseif ($magang->status == 0) {
                        $status_magagng = "Terdaftar";
                    } elseif ($magang->status == 2) {
                        $status_magagng = "Belum Terdaftar";
                    } elseif ($magang->status == 3) {
                        $status_magagng = "Terdaftar";
                    }
                }
            }

            $item->status_magang = (string)$status_magagng;

            $item->magang = $magang;

            return $item;
        });

        if ($request->status) {
            $data = $data->where('status_magang', (string)$request->status);
        }

        $random = rand(100, 999);
        return Excel::download(new MahasiswaExport($data), 'daftar_mahasiswa_' . $random . '.xlsx');
    }

    public function import()
    {
        $this->authAction('update', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $page = [
            'url' => $this->menuUrl . '/import',
            'title' => 'Import ' . $this->menuTitle
        ];

        $prodis = ProdiModel::select('prodi_id', 'prodi_name', 'prodi_code')->get();

        return view($this->viewPath . 'import')
            ->with('page', (object) $page)
            ->with('prodis', $prodis);
    }

    public function import_action(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
                'file' => 'required|mimes:xls,xlsx'
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

            $file = $request->file('file');

            $nama_file = rand() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('assets/temp_import'), $nama_file);

            $collection = Excel::toCollection(new MahasiswaImport, public_path('assets/temp_import/' . $nama_file));
            $collection = $collection[0];
            //remove 0,1,2 index
            $datas = $collection->splice(3);

            // dd($datas);

            $prodi_id = $request->prodi_id ?? auth()->user()->getProdiId();

            $datas->map(function ($item) use ($prodi_id) {
                $nim = $item[0];
                if ($nim != null) {
                    $user = UserModel::insertGetId([
                        'username' => $nim,
                        'name' => $item[1],
                        'password' => Hash::make($nim),
                        'group_id' => 4,
                        'is_active' => 1,
                    ]);
                    // dd($user);
                    MahasiswaModel::insert([
                        'user_id' => $user,
                        'prodi_id' => $prodi_id,
                        'nim' => $item[0],
                        'nama_mahasiswa' => $item[1],
                        'kelas' => $item[2],
                    ]);
                }
            });

            //remove file
            unlink(public_path('assets/temp_import/' . $nama_file));

            return response()->json([
                'stat' => true,
                'mc' => true, // close modal
                'msg' => 'Mahasiswa berhasil diimport'
            ]);
        }
    }
}

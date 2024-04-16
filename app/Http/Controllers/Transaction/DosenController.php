<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Master\DosenModel;
use App\Models\Setting\UserModel;
use App\Models\Master\ProdiModel;
use App\Imports\MahasiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Transaction\KuotaDosenModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DosenController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'TRANSACTION.DOSEN';
        $this->menuUrl   = url('transaksi/dosen');
        $this->menuTitle = 'Dosen';
        $this->viewPath  = 'transaction.dosen.';
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Transaksi', 'Dosen']
        ];

        $activeMenu = [
            'l1' => 'transaction',
            'l2' => 'transaksi-dosen',
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

        // $data  = DosenModel::selectRaw("dosen_id, dosen_name, dosen_email, kuota");
        $data  = DosenModel::withCount('pembimbingDosen')->selectRaw("dosen_id, dosen_name, dosen_email, kuota");
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

        $prodi = ProdiModel::selectRaw("prodi_id, prodi_name, prodi_code")->get();

        return view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('prodi', $prodi);
    }

    public function store(Request $request)
    {
        $this->authAction('create', 'json');

        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'dosen_name' => 'required|string|max:50',
                'dosen_email' => ['required', 'email:rfc,dns,filter', 'max:50', 'unique:m_dosen,dosen_email'],
                'dosen_phone' => ['required', 'numeric', 'digits_between:8,15', 'unique:m_dosen,dosen_phone'],
                'dosen_gender' => 'required|in:L,P',
                'dosen_tahun' => 'required|integer',
                'dosen_nip' => 'nullable',
                'dosen_nidn' => 'nullable',
                'kuota' => 'nullable',
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

            $username = null;
            if (!empty($request->input('dosen_nip'))) {
                $username = $request->input('dosen_nip');
            } elseif (!empty($request->input('dosen_nidn'))) {
                $username = $request->input('dosen_nidn');
            }

            // If neither nip nor nidn is empty, use nip as the default username
            if (!empty($request->input('dosen_nip')) && !empty($request->input('dosen_nidn'))) {
                $username = $request->input('dosen_nip');
            }

            // If both nip and nidn are empty, do not assign any username
            if (empty($username)) {
                // Handle the case where neither nip nor nidn is provided
                return response()->json([
                    'stat' => false,
                    'mc' => false,
                    'msg' => 'NIP atau NIDN Tidak Boleh Kosong',
                ]);
            }

            // Create user
            $user = [
                'username' => $username,
                'name' => $request->input('dosen_name'),
                'password' => Hash::make($username),
                'group_id' => 3,
                'is_active' => 1,
                'email' => $request->input('dosen_email'),
            ];
            $insert = UserModel::create($user);
            $request['user_id'] = $insert->user_id;
            // Create DosenModel
            // $dosen = DosenModel::insertData($request);
            $dosen = DosenModel::create([
                'dosen_name' => $request->input('dosen_name'),
                'dosen_email' => $request->input('dosen_email'),
                'dosen_phone' => $request->input('dosen_phone'),
                'dosen_gender' => $request->input('dosen_gender'),
                'dosen_tahun' => $request->input('dosen_tahun'),
                'dosen_nidn' => $request->input('dosen_nidn'),
                'dosen_nip' => $request->input('dosen_nip'),
                'kuota' => $request->input('kuota'),
                'user_id' => $insert->user_id,
                // fill other fields as needed
            ]);

            return response()->json([
                'stat' => $dosen,
                'mc' => $dosen,
                'msg' => ($dosen) ? $this->getMessage('insert.success') : $this->getMessage('insert.failed')
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

        $data = DosenModel::find($id);

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
                'dosen_name' => 'required|string|max:50',
                'dosen_email' => [
                    'sometimes',
                    'email:rfc,dns,filter',
                    'max:50',
                    Rule::unique('m_dosen', 'dosen_email')->ignore($id, 'dosen_id'),
                ],
                'dosen_phone' => [
                    'sometimes',
                    'numeric',
                    'digits_between:8,15',
                    Rule::unique('m_dosen', 'dosen_phone')->ignore($id, 'dosen_id'),
                ],
                'dosen_gender' => 'required|in:L,P',
                'dosen_tahun' => 'required|integer',
                'kuota' => 'required',
                // Add other rules for DosenModel fields
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

            // Check if the email has changed
            // Check if the email has changed
            if ($request->has('dosen_email')) {
                // Cek apakah pengguna dengan email tersebut sudah ada
                $existingUser = UserModel::where('email', $request->input('dosen_email'))->first();

                if ($existingUser) {
                    // Jika email sudah ada, update data pengguna yang sudah ada
                    $existingUser->update([
                        'name' => $request->input('dosen_name'),
                    ]);
                    $request['user_id'] = $existingUser->user_id;
                } else {
                    // Jika email belum ada, abaikan pembuatan pengguna baru
                    unset($request['user_id']);
                }
            }

            // Update DosenModel data
            $res = DosenModel::updateData($id, $request);

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

        $data = DosenModel::find($id);
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

        $data = DosenModel::find($id);

        return (!$data) ? $this->showModalError() :
            $this->showModalConfirm($this->menuUrl . '/' . $id, [
                'NIP' => $data->dosen_nip,
                'Nama Dosen' => $data->dosen_name,
            ]);
    }

    public function destroy(Request $request, $id)
    {
        $this->authAction('delete', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $res = DosenModel::deleteData($id);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => DosenModel::getDeleteMessage()
            ]);
        }

        return redirect('/');
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

            $collection->each(function ($item) {
                // Inisialisasi username dan password
                $username = null;
                $password = null;

                // Periksa apakah ada data di kolom yang dapat digunakan sebagai username atau password
                if (!empty($item[0]) || !empty($item[1])) {
                    // Jika kolom pertama berisi "-", ubah menjadi null
                    if ($item[0] === '-') {
                        $item[0] = null;
                    }

                    // Jika kolom kedua berisi "-", ubah menjadi null
                    if ($item[1] === '-') {
                        $item[1] = null;
                    }

                    // Tentukan nilai untuk username
                    $username = $item[1] ?? $item[0];

                    // Tentukan nilai untuk password
                    $password = $item[1] ?? $item[0];

                    // Memeriksa apakah ada data yang valid untuk digunakan sebagai username dan password
                    if ($username && $password) {
                        $user = UserModel::insertGetId([
                            'username' => $username,
                            'name' => $item[2], // Misalkan indeks 2 adalah nama pengguna
                            'password' => Hash::make($password),
                            'group_id' => 3,
                            'is_active' => 1,
                            'email' => $item[3],
                        ]);

                        DosenModel::insert([
                            'user_id' => $user,
                            'dosen_nip' => !empty($item[0]) ? $item[0] : null,
                            'dosen_nidn' => !empty($item[1]) ? $item[1] : null,
                            'dosen_name' => $item[2],
                            'dosen_email' => $item[3],
                            'dosen_phone' => $item[4],
                            'dosen_gender' => $item[5],
                            'dosen_tahun' => $item[6],
                            'kuota' => $item[7],
                        ]);
                    }
                }
            });

            unlink(public_path('assets/temp_import/' . $nama_file));
            // Hapus file setelah selesai

            return response()->json([
                'stat' => true,
                'mc' => true, // close modal
                'msg' => 'Dosen berhasil diimport'
            ]);
        }
    }
}

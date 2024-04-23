<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Master\DosenModel;
use App\Models\Master\NilaiPembimbingDosenModel;
use App\Models\Setting\UserModel;
use App\Models\Master\ProdiModel;
use App\Models\Master\SemhasModel;
use App\Models\Transaction\KuotaDosenModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NilaiPembimbingDosenController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'CATEGORY.NILAI.PEMBIMBING.DOSEN';
        $this->menuUrl   = url('category/nilai-pembimbing-dosen');
        $this->menuTitle = 'Category Nilai Pembimbing Dosen';
        $this->viewPath  = 'category.nilai-pembimbing-dosen.';
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Category', 'Nilai pembimbing dosen']
        ];

        $activeMenu = [
            'l1' => 'Category',
            'l2' => 'kategori-nilaipemdos',
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl,
            'title' => 'Daftar ' . $this->menuTitle
        ];

        $data = NilaiPembimbingDosenModel::all();
        // dd($data);
        return view($this->viewPath . 'index')
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $activeMenu)
            ->with('page', (object) $page)
            ->with('data', $data)
            ->with('allowAccess', $this->authAccessKey());
    }

    public function list(Request $request)
    {
        $this->authAction('read', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();
        $prodi_id = auth()->user()->prodi_id;

        $data = NilaiPembimbingDosenModel::select("nilai_pembimbing_dosen_id", "name_kriteria_pembimbing_dosen", "bobot")
            ->whereNull('parent_id')
            ->get();

        foreach ($data as $item) {
            $item->bobot = sprintf("%.0f%%", $item->bobot * 100);
        }

        $data;


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


        return view($this->viewPath . 'action')
            ->with('page', (object) $page);
    }

    public function store(Request $request)
    {
        $this->authAction('create', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
                'name_kriteria_pembimbing_dosen' => 'required|string|max:255',
                'bobot' => 'required|numeric',
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
            // Ambil nilai bobot dari request
            $bobot = $request->input('bobot');

            // Ubah nilai bobot menjadi format yang diinginkan (misalnya, dari '50' menjadi '0.50')
            $formattedBobot = $this->formatBobot($bobot);

            // Ganti nilai bobot dalam request dengan yang sudah diformat
            $request->merge(['bobot' => $formattedBobot]);

            // Insert data ke dalam basis data
            $res = NilaiPembimbingDosenModel::insertData($request);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? $this->getMessage('insert.success') : $this->getMessage('insert.failed')
            ]);
        }

        return redirect('/');
    }
    private function formatBobot($bobot)
    {
        // Konversi bobot menjadi string dengan format desimal dua tempat
        $formattedBobot = number_format($bobot / 100, 2, '.', '');

        return $formattedBobot;
    }

    public function tambah_subcategory($id)
    {
        $this->authAction('read', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $page = [
            'url' => $this->menuUrl . '/' . $id,
            'title' => 'tambah Sub Category ' . $this->menuTitle
        ];

        $data = NilaiPembimbingDosenModel::find($id);

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'tambah_subcategory')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data);
    }
    public function tambah_sub_category(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'name_kriteria_pembimbing_dosen.*' => 'required|string|max:255',
                'parent_id' => 'required|exists:m_nilai_pembimbing_dosen,nilai_pembimbing_dosen_id', // Pastikan parent_id valid
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

            // Menentukan kategori berdasarkan parent_id yang diterima dari permintaan
            $parent_id = $id;

            // Membuat array data baru untuk setiap subkategori yang ditambahkan
            $newData = [];
            foreach ($request->input('name_kriteria_pembimbing_dosen') as $subcategory) {
                $newData[] = [
                    'name_kriteria_pembimbing_dosen' => $subcategory,
                    'parent_id' => $parent_id,
                    'bobot' => null,
                    // Kolom lain yang perlu ditambahkan di sini
                ];
            }

            // Menyimpan data subkategori baru
            $res = NilaiPembimbingDosenModel::insert($newData);

            $firstTimeAddition = false;

            // Jika data berhasil ditambahkan
            if ($res) {
                // Cek apakah data ditambahkan untuk pertama kalinya
                $firstTimeAddition = (NilaiPembimbingDosenModel::where('parent_id', $id)->count() == count($newData));
            }

            return response()->json([
                'stat' => $res,
                'mc' => $res, // Menutup modal jika berhasil
                'msg' => ($res) ? $this->getMessage('update.success') : $this->getMessage('update.failed'),
                'firstTimeAddition' => $firstTimeAddition // Sertakan status apakah data ditambahkan untuk pertama kali atau tidak
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

        $data = NilaiPembimbingDosenModel::with('subKriteria')->find($id);

        // Konversi bobot dari desimal ke persen sebelum mengirimkannya ke tampilan
        if ($data && isset($data->bobot)) {
            $data->bobot = $data->bobot * 100;
        }
        // dd($data);

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
                'name_kriteria_pembimbing_dosen' => 'required|string|max:255',
                'bobot' => 'required|numeric',
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

            // Ambil nilai bobot dari request
            $bobot = $request->input('bobot');

            // Ubah nilai bobot menjadi format yang diinginkan (misalnya, dari '50' menjadi '0.50')
            $formattedBobot = $this->formatBobot($bobot);

            // Ganti nilai bobot dalam request dengan yang sudah diformat
            $request->merge(['bobot' => $formattedBobot]);

            // Update data utama
            $nilaiPembimbingDosen = NilaiPembimbingDosenModel::find($id);
            if (!$nilaiPembimbingDosen) {
                return response()->json([
                    'stat' => false,
                    'mc' => false,
                    'msg' => 'Data tidak ditemukan.'
                ]);
            }

            $nilaiPembimbingDosen->name_kriteria_pembimbing_dosen = $request->input('name_kriteria_pembimbing_dosen');
            $nilaiPembimbingDosen->bobot = $request->input('bobot');
            $nilaiPembimbingDosen->save();

            // Update data sub kriteria jika ada
            if ($request->has('sub_kriteria') && $request->has('sub_kriteria_ids')) {
                foreach ($request->input('sub_kriteria_ids') as $index => $subKriteriaId) {
                    $subKriteria = NilaiPembimbingDosenModel::find($subKriteriaId);
                    if ($subKriteria) {
                        $subKriteria->name_kriteria_pembimbing_dosen = $request->input('sub_kriteria.' . $index);
                        $subKriteria->save();
                    }
                }
            }

            return response()->json([
                'stat' => true,
                'mc' => true, // close modal
                'msg' => $this->getMessage('update.success')
            ]);
        }

        return redirect('/');
    }

    public function showsub($id)
    {
        $this->authAction('read', 'modal');
        // if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = NilaiPembimbingDosenModel::with('subKriteria')->find($id);

        // Konversi bobot dari desimal ke persen sebelum mengirimkannya ke tampilan
        if ($data && isset($data->bobot)) {
            $data->bobot = $data->bobot * 100;
        }
        $page = [
            'title' => 'Detail Sub' . $this->menuTitle
        ];

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'detail_subcategory')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data);
    }

    public function show($id)
    {
        $this->authAction('read', 'modal');
        // if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = NilaiPembimbingDosenModel::with('subKriteria')->find($id);

        // Konversi bobot dari desimal ke persen sebelum mengirimkannya ke tampilan
        if ($data && isset($data->bobot)) {
            $data->bobot = $data->bobot * 100;
        }
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

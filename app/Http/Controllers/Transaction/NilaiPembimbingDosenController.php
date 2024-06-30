<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Master\DosenModel;
use App\Models\Master\NilaiPembimbingDosenModel;
use App\Models\Master\PeriodeModel;
use App\Models\Setting\UserModel;
use App\Models\Master\ProdiModel;
use App\Models\Master\SemhasModel;
use App\Models\Transaction\KuotaDosenModel;
use App\Models\Transaction\LogModel;
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
            'l1' => 'category-nilai',
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
        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
        $data = NilaiPembimbingDosenModel::select("nilai_pembimbing_dosen_id", "name_kriteria_pembimbing_dosen", "bobot")
            ->where('periode_id', $activePeriods)
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
            $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
            // Ambil nilai bobot dari request
            $bobot = $request->input('bobot');

            // Ubah nilai bobot menjadi format yang diinginkan (misalnya, dari '50' menjadi '0.50')
            $formattedBobot = $this->formatBobot($bobot);

            // Ganti nilai bobot dalam request dengan yang sudah diformat
            $request->merge(['bobot' => $formattedBobot]);

            // Masukkan periode_id ke dalam request
            $request->merge(['periode_id' => $activePeriods]);

            // Insert data ke dalam basis data
            $res = NilaiPembimbingDosenModel::insertData($request);

            $logData = 'Name Kriteria: ' . $request->input('name_kriteria_pembimbing_dosen') .
                ', Bobot: ' . $formattedBobot;

            // Log the action
            LogModel::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'url' => $request->fullUrl(),
                'data' => $logData,
                'periode_id' => $activePeriods,
                'created_by' => auth()->id(),
            ]);

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

            $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
            // Menentukan kategori berdasarkan parent_id yang diterima dari permintaan
            $parent_id = $id;

            // Membuat array data baru untuk setiap subkategori yang ditambahkan
            $newData = [];
            foreach ($request->input('name_kriteria_pembimbing_dosen') as $subcategory) {
                $newData[] = [
                    'name_kriteria_pembimbing_dosen' => $subcategory,
                    'parent_id' => $parent_id,
                    'bobot' => null,
                    'periode_id' => $activePeriods,
                    // Kolom lain yang perlu ditambahkan di sini
                ];
                $logData = 'Parent ID: ' . $parent_id . ', Subcategory: ' . $subcategory;

                // Log the action
                LogModel::create([
                    'user_id' => auth()->id(),
                    'action' => 'update',
                    'url' => $request->fullUrl(),
                    'data' => $logData,
                    'periode_id' => $activePeriods,
                    'created_by' => auth()->id(),
                ]);
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


    public function destroy_sub_category(Request $request, $id)
    {
        // Cek apakah $id merupakan subkategori yang valid dengan parent_id yang sesuai
        $subcategory = NilaiPembimbingDosenModel::with('subKriteria')->find($id);
        // dd($subcategory);
        if (!$subcategory) {
            return response()->json([
                'stat' => false,
                'msg' => 'Subkategori tidak ditemukan atau tidak sesuai dengan parent_id yang diberikan.'
            ]);
        }

        // Hapus subkategori
        $res = $subcategory->delete();

        $dataOut = false;

        if ($res) {
            // Cek apakah data ditambahkan untuk pertama kalinya
            $dataOut = (NilaiPembimbingDosenModel::with('subKriteria')->count() == 0);
            $logData = 'Subkategori: ' . $subcategory->name_kriteria_pembimbing_dosen;

            // Log the deletion action
            LogModel::create([
                'user_id' => auth()->id(),
                'action' => 'delete',
                'url' => $request->fullUrl(),
                'data' => $logData,
                'periode_id' => $subcategory->periode_id,
                'created_by' => auth()->id(),
            ]);
            // dd($dataOut);
        }

        return response()->json([
            'stat' => $res,
            'mc' => $res, // Menutup modal jika berhasil
            'msg' => ($res) ? 'Subkategori "' . $subcategory->name_kriteria_pembimbing_dosen . '" berhasil dihapus.' : 'Gagal menghapus subkategori.',
            'dataOut' => $dataOut
        ]);

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
        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
        $data = NilaiPembimbingDosenModel::with('subKriteria')->where('periode_id', $activePeriods)->find($id);

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

            // Check if the data has changed
            $logData = [];
            if ($nilaiPembimbingDosen->name_kriteria_pembimbing_dosen !== $request->input('name_kriteria_pembimbing_dosen')) {
                $logData[] = 'Name Kriteria: ' . $request->input('name_kriteria_pembimbing_dosen');
                $nilaiPembimbingDosen->name_kriteria_pembimbing_dosen = $request->input('name_kriteria_pembimbing_dosen');
            }
            if ($nilaiPembimbingDosen->bobot !== $request->input('bobot')) {
                $logData[] = 'Bobot: ' . $formattedBobot;
                $nilaiPembimbingDosen->bobot = $request->input('bobot');
            }

            // Save if there are changes
            if (!empty($logData)) {
                $nilaiPembimbingDosen->save();
                LogModel::create([
                    'user_id' => auth()->id(),
                    'action' => 'update',
                    'url' => $request->fullUrl(),
                    'data' => implode(', ', $logData),
                    'periode_id' => $nilaiPembimbingDosen->periode_id,
                    'created_by' => auth()->id(),
                ]);
            }

            // Update data sub kriteria jika ada
            if ($request->has('sub_kriteria') && $request->has('sub_kriteria_ids')) {
                foreach ($request->input('sub_kriteria_ids') as $index => $subKriteriaId) {
                    $subKriteria = NilaiPembimbingDosenModel::find($subKriteriaId);
                    if ($subKriteria) {
                        $subLogData = [];
                        if ($subKriteria->name_kriteria_pembimbing_dosen !== $request->input('sub_kriteria.' . $index)) {
                            $subLogData[] = 'Sub Kriteria ID: ' . $subKriteriaId . ', Name Kriteria: ' . $request->input('sub_kriteria.' . $index);
                            $subKriteria->name_kriteria_pembimbing_dosen = $request->input('sub_kriteria.' . $index);
                        }

                        // Save if there are changes
                        if (!empty($subLogData)) {
                            $subKriteria->save();
                            LogModel::create([
                                'user_id' => auth()->id(),
                                'action' => 'update',
                                'url' => $request->fullUrl(),
                                'data' => implode(', ', $subLogData),
                                'periode_id' => $subKriteria->periode_id,
                                'created_by' => auth()->id(),
                            ]);
                        }
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

    // public function update(Request $request, $id)
    // {
    //     $this->authAction('update', 'json');
    //     if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

    //     if ($request->ajax() || $request->wantsJson()) {
    //         $rules = [
    //             'name_kriteria_pembimbing_dosen' => 'required|string|max:255',
    //             'bobot' => 'required|numeric',
    //         ];

    //         $validator = Validator::make($request->all(), $rules);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'stat'     => false,
    //                 'mc'       => false,
    //                 'msg'      => 'Terjadi kesalahan.',
    //                 'msgField' => $validator->errors()
    //             ]);
    //         }

    //         // Ambil nilai bobot dari request
    //         $bobot = $request->input('bobot');

    //         // Ubah nilai bobot menjadi format yang diinginkan (misalnya, dari '50' menjadi '0.50')
    //         $formattedBobot = $this->formatBobot($bobot);

    //         // Ganti nilai bobot dalam request dengan yang sudah diformat
    //         $request->merge(['bobot' => $formattedBobot]);

    //         // Update data utama
    //         $nilaiPembimbingDosen = NilaiPembimbingDosenModel::find($id);
    //         if (!$nilaiPembimbingDosen) {
    //             return response()->json([
    //                 'stat' => false,
    //                 'mc' => false,
    //                 'msg' => 'Data tidak ditemukan.'
    //             ]);
    //         }

    //         $nilaiPembimbingDosen->name_kriteria_pembimbing_dosen = $request->input('name_kriteria_pembimbing_dosen');
    //         $nilaiPembimbingDosen->bobot = $request->input('bobot');
    //         $nilaiPembimbingDosen->save();

    //         $logData = 'Name Kriteria: ' . $request->input('name_kriteria_pembimbing_dosen') . ', Bobot: ' . $formattedBobot;

    //         LogModel::create([
    //             'user_id' => auth()->id(),
    //             'action' => 'update',
    //             'url' => $request->fullUrl(),
    //             'data' => $logData,
    //             'periode_id' => $nilaiPembimbingDosen->periode_id,
    //             'created_by' => auth()->id(),
    //         ]);


    //         // Update data sub kriteria jika ada
    //         if ($request->has('sub_kriteria') && $request->has('sub_kriteria_ids')) {
    //             foreach ($request->input('sub_kriteria_ids') as $index => $subKriteriaId) {
    //                 $subKriteria = NilaiPembimbingDosenModel::find($subKriteriaId);
    //                 if ($subKriteria) {
    //                     $subKriteria->name_kriteria_pembimbing_dosen = $request->input('sub_kriteria.' . $index);
    //                     $subKriteria->save();

    //                     $subLogData = 'Sub Kriteria ID: ' . $subKriteriaId . ', Name Kriteria: ' . $request->input('sub_kriteria.' . $index);


    //                 }

    //             }
    //             LogModel::create([
    //                 'user_id' => auth()->id(),
    //                 'action' => 'update',
    //                 'url' => $request->fullUrl(),
    //                 'data' => $subLogData,
    //                 'periode_id' => $subKriteria->periode_id,
    //                 'created_by' => auth()->id(),
    //             ]);
    //         }

    //         return response()->json([
    //             'stat' => true,
    //             'mc' => true, // close modal
    //             'msg' => $this->getMessage('update.success')
    //         ]);
    //     }

    //     return redirect('/');
    // }

    public function showsub($id)
    {
        $this->authAction('read', 'modal');
        // if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = NilaiPembimbingDosenModel::with('subKriteria')->find($id);

        $datasub = $data->subKriteria->count();
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
            ->with('datasub', $datasub)
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

        $data = NilaiPembimbingDosenModel::find($id);

        // Jika aturan memiliki child, tampilkan pesan error
        if ($data->subKriteria()->exists()) {
            return $this->showModalError('Data tidak dapat dihapus karena memiliki relasi dengan data lain.');
        }

        return (!$data) ? $this->showModalError() :
            $this->showModalConfirm($this->menuUrl . '/' . $id, [
                'Nilai Kriteria' => $data->name_kriteria_pembimbing_dosen,
                'Bobot' => $data->bobot,
            ]);
    }

    public function destroy(Request $request, $id)
    {
        $this->authAction('delete', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $res = NilaiPembimbingDosenModel::deleteData($id);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => NilaiPembimbingDosenModel::getDeleteMessage()
            ]);
        }

        return redirect('/');
    }
}

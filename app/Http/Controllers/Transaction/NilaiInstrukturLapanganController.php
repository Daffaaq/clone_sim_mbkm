<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Master\DosenModel;
use App\Models\Master\NilaiInstrukturLapanganModel;
use App\Models\Master\NilaiPembahasDosenModel;
use App\Models\Master\NilaiPembimbingDosenModel;
use App\Models\Master\PeriodeModel;
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

class NilaiInstrukturLapanganController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'CATEGORY.NILAI.INSTRUKTUR.LAPANGAN';
        $this->menuUrl   = url('category/nilai-instruktur-lapangan');
        $this->menuTitle = 'Category Nilai Instruktur Lapangan';
        $this->viewPath  = 'category.nilai-instruktur-lapangan.';
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Category', 'Nilai instruktur lapangan']
        ];

        $activeMenu = [
            'l1' => 'category-nilai',
            'l2' => 'kategory-nilaiinlap',
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl,
            'title' => 'Daftar ' . $this->menuTitle
        ];

        $data = NilaiInstrukturLapanganModel::all();
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
        $data = NilaiInstrukturLapanganModel::select("nilai_instruktur_lapangan_id", "name_kriteria_instruktur_lapangan", "bobot")
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
                'name_kriteria_instruktur_lapangan' => 'required|string|max:255',
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

            $request->merge(['periode_id' => $activePeriods]);

            // Ganti nilai bobot dalam request dengan yang sudah diformat
            $request->merge(['bobot' => $formattedBobot]);

            // Insert data ke dalam basis data
            $res = NilaiInstrukturLapanganModel::insertData($request);

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

        $data = NilaiInstrukturLapanganModel::find($id);

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
                'name_kriteria_instruktur_lapangan.*' => 'required|string|max:255',
                'parent_id' => 'required|exists:m_nilai_instruktur_lapangan,nilai_instruktur_lapangan_id',
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
            foreach ($request->input('name_kriteria_instruktur_lapangan') as $subcategory) {
                $newData[] = [
                    'name_kriteria_instruktur_lapangan' => $subcategory,
                    'parent_id' => $parent_id,
                    'bobot' => null,
                    'periode_id' => $activePeriods,
                    // Kolom lain yang perlu ditambahkan di sini
                ];
            }

            // Menyimpan data subkategori baru
            $res = NilaiInstrukturLapanganModel::insert($newData);

            $firstTimeAddition = false;

            // Jika data berhasil ditambahkan
            if ($res) {
                // Cek apakah data ditambahkan untuk pertama kalinya
                $firstTimeAddition = (NilaiInstrukturLapanganModel::where('parent_id', $id)->count() == count($newData));
            }

            return response()->json([
                'stat' => $res,
                'mc' => $res, // Menutup modal jika berhasil
                'msg' => ($res) ? $this->getMessage('update.success') : $this->getMessage('update.failed'),
                'firstTimeAddition' => $firstTimeAddition
            ]);
        }

        return redirect('/');
    }

    public function destroy_sub_category(Request $request, $id)
    {
        // Cek apakah $id merupakan subkategori yang valid dengan parent_id yang sesuai
        $subcategory = NilaiInstrukturLapanganModel::with('subKriteria')->find($id);
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
            $dataOut = (NilaiInstrukturLapanganModel::with('subKriteria')->count() == 0);
            // dd($dataOut);
        }

        return response()->json([
            'stat' => $res,
            'mc' => $res, // Menutup modal jika berhasil
            'msg' => ($res) ? 'Subkategori "' . $subcategory->name_kriteria_instruktur_lapangan . '" berhasil dihapus.' : 'Gagal menghapus subkategori.',
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
        $data = NilaiInstrukturLapanganModel::with('subKriteria')->where('periode_id', $activePeriods)->find($id);

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
                'name_kriteria_instruktur_lapangan' => 'required|string|max:255',
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
            $nilaiPembimbingDosen = NilaiInstrukturLapanganModel::find($id);
            if (!$nilaiPembimbingDosen) {
                return response()->json([
                    'stat' => false,
                    'mc' => false,
                    'msg' => 'Data tidak ditemukan.'
                ]);
            }

            $nilaiPembimbingDosen->name_kriteria_instruktur_lapangan = $request->input('name_kriteria_instruktur_lapangan');
            $nilaiPembimbingDosen->bobot = $request->input('bobot');
            $nilaiPembimbingDosen->save();

            // Update data sub kriteria jika ada
            if ($request->has('sub_kriteria') && $request->has('sub_kriteria_ids')) {
                foreach ($request->input('sub_kriteria_ids') as $index => $subKriteriaId) {
                    $subKriteria = NilaiInstrukturLapanganModel::find($subKriteriaId);
                    if ($subKriteria) {
                        $subKriteria->name_kriteria_instruktur_lapangan = $request->input('sub_kriteria.' . $index);
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

        $data = NilaiInstrukturLapanganModel::with('subKriteria')->find($id);

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

        $data = NilaiInstrukturLapanganModel::with('subKriteria')->find($id);

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

        $data = NilaiInstrukturLapanganModel::find($id);
        if ($data->subKriteria()->exists()) {
            return $this->showModalError('Data tidak Bisa dihapus karena memiliki Subcategory.');
        }

        return (!$data) ? $this->showModalError() :
            $this->showModalConfirm($this->menuUrl . '/' . $id, [
                'Nilai Kriteria' => $data->name_kriteria_instruktur_lapangan,
                'Bobot' => $data->bobot,
            ]);
    }

    public function destroy(Request $request, $id)
    {
        $this->authAction('delete', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $res = NilaiInstrukturLapanganModel::deleteData($id);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => DosenModel::getDeleteMessage()
            ]);
        }

        return redirect('/');
    }
}

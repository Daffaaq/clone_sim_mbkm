<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Master\DosenModel;
use App\Models\Master\InstrukturModel;
use App\Models\Master\MahasiswaModel;
use App\Models\Master\PeriodeModel;
use App\Models\Transaction\InstrukturLapanganModel;
use App\Models\Transaction\LogModel;
use App\Models\Transaction\Magang;
use App\Models\Transaction\PembimbingDosenModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PembimbingDosenController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'TRANSACTION.PEMBIMBING.DOSEN';
        $this->menuUrl   = url('transaksi/pembimbing-dosen');     // set URL untuk menu ini
        $this->menuTitle = 'Pembimbing Dosen';                       // set nama menu
        $this->viewPath  = 'transaction.pembimbing-dosen.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Transaksi', 'Pembimbing Dosen']
        ];

        $activeMenu = [
            'l1' => 'transaction',
            'l2' => 'transaksi-pembimdos',
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl,
            'title' => $this->menuTitle
        ];
        // $dataall = dosenModel::all();
        // dd($dataall);
        // $dataall = PembimbingDosenModel::all();
        // dd($dataall);
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
        // $role = Auth::user()->prodi_id;
        // $data = PembimbingDosenModel::select(
        //     't_pembimbing_dosen.pembimbing_dosen_id',
        //     'm_mahasiswa.nama_mahasiswa',
        //     'm_dosen.dosen_name',
        //     'm_prodi.prodi_name'
        // )
        //     ->leftJoin('m_mahasiswa', 't_pembimbing_dosen.mahasiswa_id', '=', 'm_mahasiswa.mahasiswa_id')
        //     ->leftJoin('m_dosen', 't_pembimbing_dosen.dosen_id', '=', 'm_dosen.dosen_id')
        //     ->leftJoin('t_magang', 't_pembimbing_dosen.magang_id', '=', 't_magang.magang_id')
        //     ->leftJoin('m_prodi', 't_magang.prodi_id', '=', 'm_prodi.prodi_id')
        //     ->where('t_magang.status', 1) // Pastikan status magang adalah 1 (diterima)
        //     ->get();

        $prodi_id = auth()->user()->prodi_id;
        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
        // dd($activePeriods);
        $data = PembimbingDosenModel::select(
            't_pembimbing_dosen.pembimbing_dosen_id',
            'm_mahasiswa.nama_mahasiswa',
            'm_dosen.dosen_name',
            'm_prodi.prodi_name'
        )
            ->leftJoin('m_mahasiswa', 't_pembimbing_dosen.mahasiswa_id', '=', 'm_mahasiswa.mahasiswa_id')
            ->leftJoin('m_dosen', 't_pembimbing_dosen.dosen_id', '=', 'm_dosen.dosen_id')
            ->leftJoin('t_magang', 't_pembimbing_dosen.magang_id', '=', 't_magang.magang_id')
            ->leftJoin('m_prodi', 't_magang.prodi_id', '=', 'm_prodi.prodi_id')
            ->where('t_magang.status', 1)
            ->where('t_pembimbing_dosen.periode_id', $activePeriods);

        if ($prodi_id !== null) {
            // Ketika prodi_id tidak null
            $data->where(function ($query) use ($prodi_id) {
                // Filter data yang sesuai dengan prodi_id atau yang prodi_id-nya null
                $query->where('m_prodi.prodi_id', $prodi_id)
                    ->orWhereNull('m_prodi.prodi_id');
            });
        }
        $data = $data->get();




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
        $prodi_id = auth()->user()->prodi_id;
        // Ambil mahasiswa yang sudah memiliki magang dengan status 1 dan belum memiliki entri dalam PembimbingDosenModel
        // $mahasiswaWithMagang = MahasiswaModel::selectRaw("m_mahasiswa.mahasiswa_id, m_mahasiswa.nama_mahasiswa, t_magang.magang_id")
        //     ->join('t_magang', 't_magang.mahasiswa_id', '=', 'm_mahasiswa.mahasiswa_id')
        //     ->where('t_magang.status', 1)
        //     ->whereNotExists(function ($query) {
        //         $query->select(DB::raw(1))
        //             ->from('t_pembimbing_dosen')
        //             ->whereRaw('t_magang.magang_id = t_pembimbing_dosen.magang_id');
        //     })
        //     ->get();
        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
        $mahasiswaWithMagang = MahasiswaModel::selectRaw("m_mahasiswa.mahasiswa_id, m_mahasiswa.nama_mahasiswa, t_magang.magang_id")
            ->join('t_magang', 't_magang.mahasiswa_id', '=', 'm_mahasiswa.mahasiswa_id')
            ->where('t_magang.status', 1)
            ->where('t_magang.periode_id', $activePeriods)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('t_pembimbing_dosen')
                    ->whereRaw('t_magang.magang_id = t_pembimbing_dosen.magang_id');
            })
            ->when($prodi_id, function ($query) use ($prodi_id) {
                // Jika pengguna memiliki prodi_id, maka batasi query untuk hanya mahasiswa dengan magang yang sesuai dengan prodi_id tersebut
                return $query->whereHas('prodi', function ($subQuery) use ($prodi_id) {
                    $subQuery->where('prodi_id', $prodi_id);
                });
            })
            ->get();

        // Buat array untuk menyimpan mahasiswa beserta magang_id-nya
        $mahasiswa = [];
        foreach ($mahasiswaWithMagang as $data) {
            $mahasiswa[$data->mahasiswa_id] = [
                'nama_mahasiswa' => $data->nama_mahasiswa,
                'magang_id' => $data->magang_id
            ];
        }

        $dosen = DosenModel::selectRaw("dosen_id, dosen_name, kuota")->get();
        // $instuktur = InstrukturModel::selectRaw("instruktur_id, nama_instruktur")->get();
        // $prodi = ProdiModel::selectRaw("prodi_id, prodi_name, prodi_code")->get();

        return view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('mahasiswa', $mahasiswa)
            ->with('dosen', $dosen);
        // ->with('instruktur', $instuktur);
    }

    public function store(Request $request)
    {
        $this->authAction('create', 'json');

        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'mahasiswa_id' => 'required',
                'dosen_id' => 'required|exists:m_dosen,dosen_id',
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

            $dosen = DosenModel::findOrFail($request->input('dosen_id'));

            // Periksa apakah kuota dosen telah tercapai
            if ($dosen->kuota <= $dosen->pembimbingDosen()->count()) {
                return response()->json([
                    'stat' => false,
                    'mc' => false,
                    'msg' => 'Kuota dosen ' . $dosen->nama . ' sudah tercapai. Tidak bisa menambahkan pembimbing baru.'
                ]);
            }

            // Sekarang Anda dapat melanjutkan untuk menambahkan mahasiswa

            // Hitung jumlah mahasiswa yang dipilih
            $jumlahMahasiswaBaru = count($request->input('mahasiswa_id'));

            // Periksa apakah jumlah mahasiswa yang akan ditambahkan melebihi kuota yang tersedia
            if (($dosen->kuota - $dosen->pembimbingDosen()->count()) < $jumlahMahasiswaBaru) {
                return response()->json([
                    'stat' => false,
                    'mc' => false,
                    'msg' => 'Kuota dosen ' . $dosen->nama . ' tidak mencukupi untuk menambahkan semua mahasiswa yang dipilih.'
                ]);
            }
            $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
            // Sekarang Anda dapat menambahkan mahasiswa tanpa mengurangi kuota dosen

            $magang_ids = Magang::whereIn('mahasiswa_id', $request->input('mahasiswa_id'))
                ->where('periode_id', $activePeriods)
                ->where('status', 1)
                ->pluck('magang_id')
                ->toArray();
            $pembimbingDosen = [];
            $mahasiswa_ids = $request->input('mahasiswa_id');
            foreach ($magang_ids as $magang_id) {
                // Ambil satu mahasiswa dari input
                $mahasiswa_id = array_shift($mahasiswa_ids);
                $periode_id = $activePeriods;
                // Simpan data ke dalam PembimbingDosenModel
                if ($mahasiswa_id && $periode_id) {
                    // Simpan data ke dalam PembimbingDosenModel
                    $pembimbing = PembimbingDosenModel::create([
                        'magang_id' => $magang_id,
                        'mahasiswa_id' => $mahasiswa_id,
                        'dosen_id' => $request->input('dosen_id'),
                        'periode_id' => $periode_id
                        // Isi kolom-kolom lainnya sesuai kebutuhan
                    ]);
                    // Ambil informasi mahasiswa dan dosen dari relasi
                    $namaMahasiswa = $pembimbing->mahasiswa->nama_mahasiswa;
                    $namaDosen = $pembimbing->dosen->dosen_name;

                    // Simpan log
                    LogModel::create([
                        'user_id' => auth()->id(),
                        'action' => 'create',
                        'url' => $this->menuUrl,
                        'data' => 'Nama Mahasiswa: ' . $namaMahasiswa . ', Nama Dosen: ' . $namaDosen,
                        'created_by' => auth()->id(),
                        'periode_id' => $activePeriods,
                    ]);

                    // Tambahkan objek $pembimbing ke dalam array $pembimbingDosen
                    $pembimbingDosen[] = $pembimbing;
                }
            }
            return response()->json([
                'stat' => !empty($pembimbingDosen),
                'mc' => !empty($pembimbingDosen),
                'msg' => !empty($pembimbingDosen) ? $this->getMessage('insert.success') : $this->getMessage('insert.failed')
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

        $data = PembimbingDosenModel::find($id);
        if (!$data) return $this->showModalError();

        // Ambil ID mahasiswa yang telah dipilih sebelumnya
        $selectedMahasiswaId = $data->mahasiswa_id;

        // Load data mahasiswa setelah memastikan bahwa data PembimbingDosen ditemukan
        $mahasiswaWithMagang = MahasiswaModel::selectRaw("m_mahasiswa.mahasiswa_id, m_mahasiswa.nama_mahasiswa, t_magang.magang_id")
            ->join('t_magang', 't_magang.mahasiswa_id', '=', 'm_mahasiswa.mahasiswa_id')
            ->where('t_magang.status', 1)
            ->get();

        $mahasiswa = [];
        foreach ($mahasiswaWithMagang as $mahasiswaData) {
            if ($mahasiswaData->mahasiswa_id == $selectedMahasiswaId) {
                $mahasiswa[$mahasiswaData->mahasiswa_id] = [
                    'nama_mahasiswa' => $mahasiswaData->nama_mahasiswa,
                    'magang_id' => $mahasiswaData->magang_id
                ];
                break; // Keluar dari loop setelah menemukan mahasiswa yang dipilih sebelumnya
            }
        }

        $dosen = DosenModel::selectRaw("dosen_id, dosen_name")->get();

        return view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data)
            ->with('mahasiswa', $mahasiswa)
            ->with('dosen', $dosen);
    }


    // public function update(Request $request, $id)
    // {
    //     $this->authAction('update', 'json');
    //     if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

    //     if ($request->ajax() || $request->wantsJson()) {

    //         $rules = [
    //             'mahasiswa_id' => 'required',
    //             'dosen_id' => 'required|exists:m_dosen,dosen_id',
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

    //         $dosen = DosenModel::findOrFail($request->input('dosen_id'));

    //         // Periksa apakah kuota dosen telah tercapai
    //         if ($dosen->kuota <= $dosen->pembimbingDosen()->count()) {
    //             return response()->json([
    //                 'stat' => false,
    //                 'mc' => false,
    //                 'msg' => 'Kuota dosen ' . $dosen->nama . ' sudah tercapai. Tidak bisa menambahkan pembimbing baru.'
    //             ]);
    //         }

    //         // Sekarang Anda dapat melanjutkan untuk menambahkan mahasiswa

    //         // Hitung jumlah mahasiswa yang dipilih
    //         $jumlahMahasiswaBaru = count($request->input('mahasiswa_id'));

    //         // Periksa apakah jumlah mahasiswa yang akan ditambahkan melebihi kuota yang tersedia
    //         if (($dosen->kuota - $dosen->pembimbingDosen()->count()) < $jumlahMahasiswaBaru) {
    //             return response()->json([
    //                 'stat' => false,
    //                 'mc' => false,
    //                 'msg' => 'Kuota dosen ' . $dosen->nama . ' tidak mencukupi untuk menambahkan semua mahasiswa yang dipilih.'
    //             ]);
    //         }
    //         $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
    //         $mahasiswa_ids = $request->input('mahasiswa_id');
    //         $pembimbingDosen = null;
    //         $magang_ids = [];
    //         if (!empty($mahasiswa_ids)) {
    //             // Ambil nilai pertama dari array, karena saat update hanya memungkinkan satu nilai mahasiswa_id
    //             $mahasiswa_id = !empty($mahasiswa_ids) ? intval($mahasiswa_ids[0]) : null;
    //             // dd($mahasiswa_id);

    //             // Pastikan mahasiswa_id tidak null sebelum menyimpan data
    //             if (!is_null($mahasiswa_id)) {
    //                 // Cari nilai magang_id
    //                 $magang_id = Magang::where('mahasiswa_id', $mahasiswa_id)
    //                     ->where('periode_id', $activePeriods)
    //                     ->value('magang_id');
    //                 // dd($magang_id);
    //                 $magang_ids[] = $magang_id;
    //                 // Temukan model PembimbingDosen berdasarkan ID
    //                 $pembimbingDosen = PembimbingDosenModel::find($id);
    //                 if ($pembimbingDosen) {
    //                     // Perbarui atribut model dan simpan perubahan
    //                     $pembimbingDosen->magang_id = $magang_id;
    //                     $pembimbingDosen->mahasiswa_id = $mahasiswa_id;
    //                     $pembimbingDosen->dosen_id = $request->input('dosen_id');
    //                     $pembimbingDosen->periode_id = $activePeriods;
    //                     $pembimbingDosen->save();
    //                 }
    //             }
    //         }

    //         return response()->json([
    //             'stat' => $pembimbingDosen,
    //             'mc' => $pembimbingDosen, // close modal
    //             'msg' => ($pembimbingDosen) ? $this->getMessage('update.success') : $this->getMessage('update.failed')
    //         ]);
    //     }

    //     return redirect('/');
    // }
    public function update(Request $request, $id)
    {
        $this->authAction('update', 'json');

        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'dosen_id' => 'required|exists:m_dosen,dosen_id',
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

            $newDosenId = $request->input('dosen_id');
            $newDosen = DosenModel::findOrFail($newDosenId);

            // Periksa apakah kuota dosen baru telah tercapai
            if ($newDosen->kuota <= $newDosen->pembimbingDosen()->count()) {
                return response()->json([
                    'stat' => false,
                    'mc' => false,
                    'msg' => 'Kuota dosen ' . $newDosen->dosen_name . ' sudah tercapai. Tidak bisa menambahkan pembimbing baru.'
                ]);
            }

            // Mulai transaksi untuk memastikan konsistensi data
            DB::beginTransaction();
            try {
                $pembimbingDosen = PembimbingDosenModel::find($id);
                if ($pembimbingDosen) {
                    $oldDosenId = $pembimbingDosen->dosen_id;

                    // Update dosen_id di PembimbingDosenModel
                    $pembimbingDosen->dosen_id = $newDosenId;
                    $pembimbingDosen->save();

                    // Ambil informasi mahasiswa dan dosen dari relasi
                    $namaMahasiswa = $pembimbingDosen->mahasiswa->nama_mahasiswa;
                    $namaDosen = $newDosen->dosen_name;

                    // Simpan log
                    LogModel::create([
                        'user_id' => auth()->id(),
                        'action' => 'update',
                        'url' => $this->menuUrl,
                        'data' => 'Nama Mahasiswa: ' . $namaMahasiswa . ', Nama Dosen: ' . $namaDosen,
                        'created_by' => auth()->id(),
                        'periode_id' => $pembimbingDosen->periode_id,
                    ]);

                    // Commit transaksi jika semuanya berhasil
                    DB::commit();

                    return response()->json([
                        'stat' => $pembimbingDosen,
                        'mc' => $pembimbingDosen, // close modal
                        'msg' => $this->getMessage('update.success')
                    ]);
                } else {
                    return response()->json([
                        'stat' => false,
                        'mc' => false,
                        'msg' => $this->getMessage('update.failed')
                    ]);
                }
            } catch (\Exception $e) {
                // Rollback transaksi jika terjadi kesalahan
                DB::rollBack();
                return response()->json([
                    'stat' => false,
                    'mc' => false,
                    'msg' => 'Terjadi kesalahan saat memperbarui kuota dosen.'
                ]);
            }
        }

        return redirect('/');
    }
}

<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Master\DosenModel;
use App\Models\Master\InstrukturModel;
use App\Models\Master\MahasiswaModel;
use App\Models\Master\PeriodeModel;
use App\Models\Setting\UserModel;
use App\Models\Master\ProdiModel;
use App\Models\Transaction\InstrukturLapanganModel;
use App\Models\Transaction\JadwalSidangMagangModel;
use App\Models\Transaction\KuotaDosenModel;
use App\Models\Transaction\LogBimbinganModel;
use App\Models\Transaction\Magang;
use App\Models\Transaction\PembimbingDosenModel;
use App\Models\Transaction\SemhasDaftarModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;

class UjianSeminarHasilController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'TRANSACTION.UJIAN.SEMHAS';
        $this->menuUrl   = url('transaksi/ujian-seminar-hasil');
        $this->menuTitle = 'Ujian Seminar Hasil';
        $this->viewPath  = 'transaction.ujian-seminar-hasil.';
    }

    public function index()
    {
        $user = auth()->user();
        // $user = auth()->user()->id;
        $user_id = $user->user_id;
        $mahasiswa = MahasiswaModel::where('user_id', $user_id)->first();
        $mahasiswa_id = $mahasiswa->mahasiswa_id;
        $activePeriods = PeriodeModel::where('is_active', 1)->pluck('periode_id');
        // Gunakan mahasiswa_id untuk mencari data magang
        $magang_data = Magang::where('mahasiswa_id', $mahasiswa_id)->get();

        $magang_status = Magang::where('mahasiswa_id', $mahasiswa_id)
            ->where('status', 1)
            ->where('periode_id', $activePeriods->toArray()) // Status 1 menunjukkan 'Diterima'
            ->exists();
        if ($magang_status) {
            $this->authAction('read');
            $this->authCheckDetailAccess();

            $breadcrumb = [
                'title' => $this->menuTitle,
                'list'  => ['Transaksi', 'Ujian Seminar Hasil']
            ];

            $activeMenu = [
                'l1' => 'transaction',
                'l2' => 'transaksi-ujasem',
                'l3' => null
            ];

            $page = [
                'url' => $this->menuUrl,
                'title' => 'Jadwal ' . $this->menuTitle
            ];
            $user = auth()->user();
            // dd($user);
            // $user = auth()->user()->id;
            $user_id = $user->user_id;
            // $userId = Auth::id();
            $mahasiswa = MahasiswaModel::where('user_id', $user_id)->first();
            $mahasiswa_id = $mahasiswa->mahasiswa_id;
            $data = SemhasDaftarModel::where('created_by', $user_id)
                ->whereHas('magang', function ($query) use ($activePeriods) {
                    $query->where('periode_id', $activePeriods->toArray());
                })
                ->with('pembimbingDosen.dosen')
                ->with('magang.mitra')
                ->with('magang.mitra.kegiatan')
                ->with('magang.periode')
                ->with('magang.mahasiswa')
                ->first();
            if (!$data) {
                $message = "halaman belum bisa diakses. Silahkan untuk mendaftar seminar magang terlebih dahulu";
                return view($this->viewPath . 'index')
                    ->with('breadcrumb', (object) $breadcrumb)
                    ->with('activeMenu', (object) $activeMenu)
                    ->with('page', (object) $page)
                    ->with('data', $data)
                    ->with('user', $user)
                    ->with('message', $message)
                    ->with('allowAccess', $this->authAccessKey());
            }
            // dd($data);
            $dataJadwalSeminar = JadwalSidangMagangModel::where('semhas_daftar_id', $data->semhas_daftar_id)->first();
            $dataJadwalSeminar->jam_sidang_mulai = substr($dataJadwalSeminar->jam_sidang_mulai, 0, 5); // Mengambil karakter pertama hingga karakter ke-4
            $dataJadwalSeminar->jam_sidang_selesai = substr($dataJadwalSeminar->jam_sidang_selesai, 0, 5); // Mengambil karakter pertama hingga karakter ke-4
            // dd($dataJadwalSeminar);
            if (!$dataJadwalSeminar) {
                $message = "halaman belum bisa diakses. Silahkan menunggu untuk pembagian jadwal sidang magang";
                return view($this->viewPath . 'index')
                    ->with('breadcrumb', (object) $breadcrumb)
                    ->with('activeMenu', (object) $activeMenu)
                    ->with('page', (object) $page)
                    ->with('data', $data)
                    ->with('user', $user)
                    ->with('message', $message)
                    ->with('allowAccess', $this->authAccessKey());
            }
            return view($this->viewPath . 'index')
                ->with('breadcrumb', (object) $breadcrumb)
                ->with('activeMenu', (object) $activeMenu)
                ->with('page', (object) $page)
                ->with('data', $data)
                ->with('user', $user)
                ->with('dataJadwalSeminar', $dataJadwalSeminar)
                ->with('allowAccess', $this->authAccessKey());
        } else {
            $this->authAction('read');
            $this->authCheckDetailAccess();

            $breadcrumb = [
                'title' => $this->menuTitle,
                'list'  => ['Transaksi', 'Ujian Seminar Hasil']
            ];

            $activeMenu = [
                'l1' => 'transaction',
                'l2' => 'transaksi-ujasem',
                'l3' => null
            ];

            $page = [
                'url' => $this->menuUrl,
                'title' => 'Daftar ' . $this->menuTitle
            ];

            $magang_status = Magang::where('mahasiswa_id', $mahasiswa_id)
                ->where('status', 0) // Status 0 menunjukkan 'Belum keterima'
                ->exists();

            if ($magang_status) {
                $message = "Anda belum keterima dalam magang. Silahkan untuk menunggu.";
            } elseif (Magang::where('mahasiswa_id', $mahasiswa_id)->exists()) {
                // Mahasiswa telah mendaftar magang tetapi belum diterima atau ditolak
                $message = "Anda belum keterima dalam magang. Silahkan untuk mendaftar ulang.";
            } else {
                // Mahasiswa belum mendaftar magang
                $message = "Anda belum mendaftar magang. Silahkan untuk mendaftar magang.";
            }
            return view('transaction.instruktur.index1')
                ->with('breadcrumb', (object) $breadcrumb)
                ->with('activeMenu', (object) $activeMenu)
                ->with('page', (object) $page)
                ->with('message', $message)
                ->with('allowAccess', $this->authAccessKey());
        }
    }

    public function list(Request $request)
    {
        $this->authAction('read', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $userId = Auth::id();

        // $data  = LogBimbinganModel::selectRaw("log_bimbingan_id, tanggal, topik_bimbingan, jam_mulai, jam_selesai, status1, status2")
        //     ->where('created_by', $userId);
        // $data = LogBimbinganModel::select('log_bimbingan_id', 'tanggal', 'topik_bimbingan', 'jam_mulai', 'jam_selesai', 'status1', 'status2')
        //     ->where('created_by', $userId)
        //     ->get();
        $activePeriods = PeriodeModel::where('is_active', 1)->pluck('periode_id');
        $data = LogBimbinganModel::select(
            'log_bimbingan_id',
            'tanggal',
            'topik_bimbingan',
            DB::raw('TIME_FORMAT(jam_mulai, "%H:%i") AS jam_mulai'),
            DB::raw('TIME_FORMAT(jam_selesai, "%H:%i") AS jam_selesai'),
            'status1',
            'status2'
        )
            ->whereIn('pembimbing_dosen_id', function ($query) use ($activePeriods) {
                $query->select('pembimbing_dosen_id')
                    ->from('t_pembimbing_dosen')
                    ->whereIn('magang_id', function ($innerQuery) use ($activePeriods) {
                        $innerQuery->select('magang_id')
                            ->from('t_magang')
                            ->where('periode_id', $activePeriods->toArray());
                    });
            })
            ->where('created_by', $userId)
            ->get();


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
        $user = auth()->user();
        $user_id = $user->user_id;
        $mahasiswa = MahasiswaModel::where('user_id', $user_id)->first();
        $mahasiswa_id = $mahasiswa->mahasiswa_id;

        // Gunakan mahasiswa_id untuk mencari data magang
        $instrukturLapangan = InstrukturLapanganModel::where('mahasiswa_id', $mahasiswa_id)->first();
        $pembimbingdosen = PembimbingDosenModel::where('mahasiswa_id', $mahasiswa_id)->first();
        $instrukturLapangan_id = InstrukturLapanganModel::where('mahasiswa_id', $mahasiswa_id)->pluck('instruktur_lapangan_id')->first();
        $pembimbingdosen_id = PembimbingDosenModel::where('mahasiswa_id', $mahasiswa_id)->pluck('pembimbing_dosen_id')->first();
        // dd($pembimbingdosen);

        $dosen_name = $pembimbingdosen->dosen->dosen_name;
        $instruktur_name = $instrukturLapangan->instruktur->nama_instruktur;
        // dd($instruktur_name);
        // dd($dosen_name);

        return view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('instrukturLapangan_id', $instrukturLapangan_id)
            ->with('pembimbingdosen_id', $pembimbingdosen_id)
            ->with('dosen_name', $dosen_name)
            ->with('instruktur_name', $instruktur_name);
    }

    public function store(Request $request)
    {
        $this->authAction('create', 'json');

        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'tanggal' => 'required|date',
                'jam_mulai' => 'required',
                'jam_selesai' => 'required',
                'topik_bimbingan' => 'required|string',
                'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Menggunakan aturan image untuk validasi file gambar
                // Tambahkan aturan validasi lainnya untuk field DosenModel
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
            // $user = auth()->user();
            // $user_id = $user->user_id;
            // $mahasiswa = MahasiswaModel::where('user_id', $user_id)->first();
            // $mahasiswa_id = $mahasiswa->mahasiswa_id;
            // $instrukturLapangan = InstrukturLapanganModel::where('mahasiswa_id', $mahasiswa_id)->pluck('instruktur_lapangan_id')->first();
            // $pembimbingdosen = PembimbingDosenModel::where('mahasiswa_id', $mahasiswa_id)->pluck('pembimbing_dosen_id')->first();
            // dd($pembimbingdosen);
            // dd($instrukturLapangan);
            // Dapatkan file foto dari request
            $file = $request->file('foto');

            // Generate nama file yang unik berdasarkan waktu dan ekstensi asli file
            $fileName = 'logbimbingan_' . time() . '.' . $file->getClientOriginalExtension();

            // Simpan foto ke dalam direktori penyimpanan
            $file->storeAs('public/assets/logbimbingan', $fileName);

            $log_bimbingan = LogBimbinganModel::create([
                'tanggal' => $request->input('tanggal'),
                'jam_mulai' => $request->input('jam_mulai'),
                'jam_selesai' => $request->input('jam_selesai'),
                'topik_bimbingan' => $request->input('topik_bimbingan'),
                // 'pembimbing_dosen_id' => $pembimbingdosen,
                // 'instruktur_lapangan_id' => $instrukturLapangan,
                'pembimbing_dosen_id' => $request->input('pembimbing_dosen_id'),
                'instruktur_lapangan_id' =>  $request->input('instruktur_lapangan_id'),
                'status1' => 0, // Status 1 defaultnya adalah 0
                'status2' => 0, // Status 2 defaultnya adalah 0
                'foto' => $fileName,
                'created_by' => Auth::id(),
                // fill other fields as needed
            ]);
            // dd($log_bimbingan);
            return response()->json([
                'stat' => $log_bimbingan,
                'mc' => $log_bimbingan,
                'msg' => ($log_bimbingan) ? $this->getMessage('insert.success') : $this->getMessage('insert.failed')
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
        $user = auth()->user();
        $user_id = $user->user_id;
        $mahasiswa = MahasiswaModel::where('user_id', $user_id)->first();
        $mahasiswa_id = $mahasiswa->mahasiswa_id;

        // Gunakan mahasiswa_id untuk mencari data magang
        $instrukturLapangan = InstrukturLapanganModel::where('mahasiswa_id', $mahasiswa_id)->first();
        $pembimbingdosen = PembimbingDosenModel::where('mahasiswa_id', $mahasiswa_id)->first();
        $instrukturLapangan_id = InstrukturLapanganModel::where('mahasiswa_id', $mahasiswa_id)->pluck('instruktur_lapangan_id')->first();
        $pembimbingdosen_id = PembimbingDosenModel::where('mahasiswa_id', $mahasiswa_id)->pluck('pembimbing_dosen_id')->first();
        // dd($pembimbingdosen);

        $dosen_name = $pembimbingdosen->dosen->dosen_name;
        $instruktur_name = $instrukturLapangan->instruktur->nama_instruktur;
        $data = LogBimbinganModel::find($id);

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('instrukturLapangan_id', $instrukturLapangan_id)
            ->with('pembimbingdosen_id', $pembimbingdosen_id)
            ->with('dosen_name', $dosen_name)
            ->with('instruktur_name', $instruktur_name)
            ->with('data', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'tanggal' => 'required|date',
                'jam_mulai' => 'required',
                'jam_selesai' => 'required',
                'topik_bimbingan' => 'required|string',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Gunakan 'sometimes' agar validasi tidak wajib
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

            // Periksa apakah ada file foto yang diunggah
            if ($request->hasFile('foto')) {
                // Dapatkan file foto dari request
                $file = $request->file('foto');

                // Generate nama file yang unik berdasarkan waktu dan ekstensi asli file
                $fileName = 'logbimbingan_' . time() . '.' . $file->getClientOriginalExtension();

                // Simpan foto baru ke dalam direktori penyimpanan
                $file->storeAs('public/assets/logbimbingan', $fileName);

                // Hapus foto lama jika ada
                $log_bimbingan = LogBimbinganModel::find($id);
                if ($log_bimbingan->foto) {
                    Storage::delete('public/assets/logbimbingan/' . $log_bimbingan->foto);
                }

                // Update foto baru dalam database
                $log_bimbingan->foto = $fileName;
                $log_bimbingan->save();
            }

            // Update data LogBimbinganModel
            $log_bimbingan = LogBimbinganModel::find($id);
            $log_bimbingan->tanggal = $request->input('tanggal');
            $log_bimbingan->jam_mulai = $request->input('jam_mulai');
            $log_bimbingan->jam_selesai = $request->input('jam_selesai');
            $log_bimbingan->topik_bimbingan = $request->input('topik_bimbingan');
            $log_bimbingan->pembimbing_dosen_id = $request->input('pembimbing_dosen_id');
            $log_bimbingan->instruktur_lapangan_id = $request->input('instruktur_lapangan_id');
            $log_bimbingan->save();

            return response()->json([
                'stat' => $log_bimbingan,
                'mc' => $log_bimbingan,
                'msg' => ($log_bimbingan) ? $this->getMessage('update.success') : $this->getMessage('update.failed')
            ]);
        }

        return redirect('/');
    }


    public function show($id)
    {
        $this->authAction('read', 'modal');
        // if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();
        $userId = Auth::id();
        $data = LogBimbinganModel::find($id);
        // Memeriksa apakah entitas ditemukan dan apakah dibuat oleh pengguna yang saat ini diotentikasi
        if (!$data || $data->created_by != $userId) {
            // Jika tidak, mungkin Anda ingin menampilkan pesan error atau melakukan tindakan lainnya
            return $this->showModalError();
        }
        $page = [
            'title' => 'Detail ' . $this->menuTitle
        ];

        return view($this->viewPath . 'detail')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data);
        // return (!$data) ? $this->showModalError() :
        //     view($this->viewPath . 'detail')
        //     ->with('page', (object) $page)
        //     ->with('id', $id)
        //     ->with('data', $data);
    }

    public function confirm($id)
    {
        $this->authAction('delete', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = LogBimbinganModel::find($id);

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

            $res = LogBimbinganModel::deleteData($id);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => LogBimbinganModel::getDeleteMessage()
            ]);
        }

        return redirect('/');
    }

    public function reportLogBimbingan()
    {
        $activePeriods = PeriodeModel::where('is_active', 1)->pluck('periode_id');
        $user = auth()->user();
        $user_id = $user->user_id;
        $mahasiswa = MahasiswaModel::where('user_id', $user_id)->first();
        // dd($mahasiswa);
        $mahasiswa_id = $mahasiswa->mahasiswa_id;
        // $instrukturLapangan = InstrukturLapanganModel::where('mahasiswa_id', $mahasiswa_id)->with('instruktur')->get();
        // $instruktur = $instrukturLapangan->first(); // Ambil objek pertama dari koleksi
        // $nama_instruktur = optional($instruktur->instruktur)->nama_instruktur; // Akses atribut instruktur dari objek pertama
        // $PembimbingDosenModel = PembimbingDosenModel::where('mahasiswa_id', $mahasiswa_id)->with('dosen')->get();
        // $dosen = $PembimbingDosenModel->first(); // Ambil objek pertama dari koleksi
        // $nama_dosen = optional($dosen->dosen)->dosen_name; // Akses atribut instruktur dari objek pertama
        // Mengambil instruktur lapangan untuk mahasiswa tertentu dengan relasi 'instruktur'
        $instrukturLapangan = InstrukturLapanganModel::where('mahasiswa_id', $mahasiswa_id)
            ->whereHas('magang', function ($query) use ($activePeriods) {
                $query->where('periode_id', $activePeriods->toArray());
            })
            ->with('instruktur')->first();
        $nama_instruktur = optional($instrukturLapangan->instruktur)->nama_instruktur;

        // Mengambil pembimbing dosen untuk mahasiswa tertentu dengan relasi 'dosen'
        $pembimbingDosen = PembimbingDosenModel::where('mahasiswa_id', $mahasiswa_id)
            ->whereHas('magang', function ($query) use ($activePeriods) {
                $query->where('periode_id', $activePeriods->toArray());
            })
            ->with('dosen')->first();
        $nama_dosen = optional($pembimbingDosen->dosen)->dosen_name;

        // dd($nama_instruktur);
        // dd($mahasiswa_id);
        $magang_ids = Magang::whereIn('mahasiswa_id', [$mahasiswa_id]) // Perhatikan penambahan tanda kurung siku untuk membungkus nilai dalam array
            ->where('status', 1)
            ->pluck('magang_id');
        // dd($magang_ids);

        $magang = Magang::whereIn('magang_id', $magang_ids) // Perhatikan penggunaan whereIn() untuk memeriksa apakah $magang_ids ada di dalam array
            ->where('mahasiswa_id', $mahasiswa_id) // Tambahkan klausa where untuk mahasiswa_id
            ->with('mitra')
            ->with('mitra.kegiatan')
            ->with('periode')
            ->where('periode_id', $activePeriods->toArray())
            ->first();

        $data = LogBimbinganModel::select(
            'log_bimbingan_id',
            'tanggal',
            'topik_bimbingan',
            DB::raw('TIME_FORMAT(jam_mulai, "%H:%i") AS jam_mulai'),
            DB::raw('TIME_FORMAT(jam_selesai, "%H:%i") AS jam_selesai'),
            'status1',
            'status2'
        )
            ->whereIn('pembimbing_dosen_id', function ($query) use ($activePeriods) {
                $query->select('pembimbing_dosen_id')
                    ->from('t_pembimbing_dosen')
                    ->whereIn('magang_id', function ($innerQuery) use ($activePeriods) {
                        $innerQuery->select('magang_id')
                            ->from('t_magang')
                            ->where('periode_id', $activePeriods->toArray());
                    });
            })
            ->where('created_by', $user_id)
            ->where('status1', 1) // Menambahkan pengecekan status1 == 1
            ->where('status2', 1) // Menambahkan pengecekan status2 == 1
            ->get();

        // dd($data);

        $pdf = Pdf::loadView('transaction.log-bimbingan.cetak_pdf', compact('magang', 'data', 'mahasiswa', 'nama_instruktur', 'nama_dosen'));
        return $pdf->stream();
    }
}

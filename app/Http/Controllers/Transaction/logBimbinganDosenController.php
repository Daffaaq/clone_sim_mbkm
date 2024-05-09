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
use App\Models\Transaction\KuotaDosenModel;
use App\Models\Transaction\LogBimbinganModel;
use App\Models\Transaction\PembimbingDosenModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LogBimbinganDosenController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'DOSEN.PEMBIMBING.LOG.BIMBINGAN.DOSEN';
        $this->menuUrl   = url('dosen-pembimbing/log-bimbingan-dosen');
        $this->menuTitle = 'Log Bimbingan Dosen';
        $this->viewPath  = 'transaction.log-bimbingan-dosen.';
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Dosen Pembimbing', 'Log Bimbingan Dosen']
        ];

        $activeMenu = [
            'l1' => 'dosen-pembimbing',
            'l2' => 'dosenpem-logdosen',
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl,
            'title' => 'Daftar ' . $this->menuTitle
        ];

        $user = auth()->user();
        $user_id = $user->user_id;
        $instruktur = DosenModel::where('user_id', $user_id)->first();
        $instruktur_id = $instruktur->dosen_id;
        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
        // dd($instruktur_id);
        $pembimbing_dosen = PembimbingDosenModel::where('dosen_id', $instruktur_id)->where('periode_id', $activePeriods)->get();
        // dd($pembimbing_dosen);
        // $pembimbing_dosen_id = $pembimbing_dosen->pembimbing_dosen_id;
        $pembimbing_dosen_ids = $pembimbing_dosen->pluck('pembimbing_dosen_id')->toArray();
        // dd($pembimbing_dosen_ids);
        // dd($instruktur_lapangan_id);
        // $all = LogBimbinganModel::all();
        // dd($all);
        // Dapatkan semua log bimbingan yang terkait dengan instruktur lapangan tersebut
        $logBimbingans = LogBimbinganModel::whereIn('pembimbing_dosen_id', $pembimbing_dosen_ids);
        // dd($logBimbingans);

        // Dapatkan semua user ID mahasiswa yang membuat log bimbingan
        $userIdMahasiswa = $logBimbingans->pluck('created_by')->toArray();
        // dd($userIdMahasiswa);

        // Dapatkan data mahasiswa berdasarkan user ID yang diperoleh
        $mahasiswas = MahasiswaModel::whereIn('user_id', $userIdMahasiswa)->get();
        // dd($mahasiswas);

        // Inisialisasi dropdown filter mahasiswa jika ada mahasiswa yang relevan
        $mahasiswaDropdown = [];
        if ($mahasiswas->isNotEmpty()) {
            $mahasiswaDropdown = $mahasiswas->pluck('nama_mahasiswa', 'user_id');
            // dd($mahasiswaDropdown);
        } else {
            // Handle jika tidak ada mahasiswa yang relevan
            $mahasiswaDropdown = ['' => '- Tidak Ada Mahasiswa -'];
        }

        return view($this->viewPath . 'index')
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $activeMenu)
            ->with('page', (object) $page)
            ->with('mahasiswas', $mahasiswas)
            ->with('mahasiswaDropdown', $mahasiswaDropdown)
            ->with('allowAccess', $this->authAccessKey());
    }

    public function list(Request $request)
    {
        $this->authAction('read', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();
        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
        $user = auth()->user();
        $user_id = $user->user_id;
        $instruktur = DosenModel::where('user_id', $user_id)->first();
        $instruktur_id = $instruktur->dosen_id;

        // $pembimbing_dosen = PembimbingDosenModel::where('dosen_id', $instruktur_id)->first();
        // $pembimbing_dosen_id = $pembimbing_dosen->pembimbing_dosen_id;
        // $pembimbing_dosen = PembimbingDosenModel::where('dosen_id', $instruktur_id)->get();
        // $pembimbing_dosen_id = $pembimbing_dosen->pluck('pembimbing_dosen_id')->toArray();
        // // dd($pembimbing_dosen_ids);
        // $pembimbing_dosen_ids = array_unique($pembimbing_dosen_id);
        $pembimbing_dosen = PembimbingDosenModel::where('dosen_id', $instruktur_id)
            ->where('periode_id', $activePeriods)
            ->pluck('pembimbing_dosen_id') // Ambil hanya kolom pembimbing_dosen_id
            ->unique() // Hanya nilai unik
            ->sort() // Urutkan nilai
            ->values() // Reset index array
            ->toArray(); // Konversi ke array

        $pembimbing_dosen_ids = $pembimbing_dosen;
        // dd($pembimbing_dosen_ids);

        // dd($activePeriods);
        // Gunakan instruktur_lapangan_id untuk mengambil data LogBimbinganModel
        // $data = LogBimbinganModel::where('pembimbing_dosen_id', $pembimbing_dosen_id);
        // $data = LogBimbinganModel::whereIn('pembimbing_dosen_id', $pembimbing_dosen_ids);
        // // Filter data log bimbingan berdasarkan mahasiswa jika filter mahasiswa dipilih
        // if ($request->filled('filter_mahasiswa')) {
        //     $data->where('created_by', $request->filter_mahasiswa);
        // }
        $data = LogBimbinganModel::whereIn('pembimbing_dosen_id', $pembimbing_dosen_ids)
            ->whereIn('t_log_bimbingan.pembimbing_dosen_id', function ($query) use ($activePeriods) {
                $query->select('pembimbing_dosen_id')
                    ->from('t_pembimbing_dosen')
                    ->whereIn(
                        'magang_id',
                        function ($innerQuery) use ($activePeriods) {
                            $innerQuery->select('magang_id')
                                ->from('t_magang')
                                ->where('periode_id', $activePeriods);
                        }
                    );
            });
        // Filter data log bimbingan berdasarkan mahasiswa jika filter mahasiswa dipilih
        if ($request->filled('filter_mahasiswa')) {
            $data->where('created_by', $request->filter_mahasiswa);
        }
        // Filter berdasarkan is_active yang bernilai 1 di PeriodeModel

        // $data = LogBimbinganModel::whereIn('pembimbing_dosen_id', $pembimbing_dosen_ids)
        //     ->whereIn('created_by', function ($query) use ($activePeriods) {
        //         $query->select('magang_id')
        //             ->from('t_magang')
        //             ->whereIn('periode_id', $activePeriods);
        //     });
        // $data = LogBimbinganModel::join('t_pembimbing_dosen', 't_log_bimbingan.pembimbing_dosen_id', '=', 't_pembimbing_dosen.pembimbing_dosen_id')
        //     ->join('t_magang', 't_pembimbing_dosen.magang_id', '=', 't_magang.magang_id')
        //     ->whereIn('t_log_bimbingan.pembimbing_dosen_id', $pembimbing_dosen_ids)
        //     ->where('t_magang.periode_id', $activePeriods); // Pengecekan periode
        // dd($request->filter_mahasiswa);
        // $createdBy = (int) $request->filter_mahasiswa;
        if ($request->filled('filter_mahasiswa')) {
            $createdBy = (int) $request->filter_mahasiswa;
            $data->where('created_by', $createdBy);
        }

        $filteredData = $data->get();
        // dd($filteredData);
        foreach ($filteredData as $data) {
            $data->jam_mulai = substr($data->jam_mulai, 0, 5); // Ambil jam dan menit dari jam_mulai
            $data->jam_selesai = substr($data->jam_selesai, 0, 5); // Ambil jam dan menit dari jam_selesai
        }

        return DataTables::of($filteredData)
            ->addIndexColumn()
            ->make(true);
    }

    public function updateStatusDosen(Request $request)
    {
        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
        // Ambil data yang dikirimkan melalui permintaan AJAX
        $logBimbinganId = $request->input('log_bimbingan_id');
        $statusDosen = $request->input('status1');
        $nilaiPembimbingDosen = $request->input('nilai_pembimbing_dosen'); // Ambil nilai pembimbing dosen dari permintaan

        if (
            $statusDosen == 1 && $nilaiPembimbingDosen < 81
        ) {
            return response()->json(['success' => false, 'message' => 'Nilai pembimbing harus minimal 81']);
        }
        if (
            $statusDosen == 1 && $nilaiPembimbingDosen > 100
        ) {
            return response()->json(['success' => false, 'message' => 'Nilai pembimbing harus maksimal 100']);
        }
        // Lakukan proses pembaruan status dosen pembimbing di sini
        $logBimbingan = LogBimbinganModel::where('periode_id', $activePeriods)->find($logBimbinganId);
        // Periksa apakah entitas ditemukan
        // if (!$logBimbingan) {
        //     return response()->json([
        //         'success' => false, 'message' => 'Log bimbingan tidak ditemukan'
        //     ]);
        // }
        $logBimbingan->status1 = $statusDosen;

        // Set tanggal_status_dosen berdasarkan status yang diubah
        if ($statusDosen == 1) {
            // Jika status diubah menjadi 'diterima', atur tanggal_status_dosen menjadi tanggal saat ini
            $logBimbingan->tanggal_status_dosen = now();
        } else if ($statusDosen == 2) {
            // Jika status diubah menjadi 'ditolak', atur nilai_pembimbing_dosen menjadi 0
            $logBimbingan->nilai_pembimbing_dosen = 0;
            // Atur tanggal_status_dosen menjadi null atau kosong
            $logBimbingan->tanggal_status_dosen = now(); // Sesuaikan dengan preferensi Anda
        } else {
            // Jika status diubah menjadi 'pending', atur tanggal_status_dosen menjadi null atau kosong
            $logBimbingan->tanggal_status_dosen = null; // Sesuaikan dengan preferensi Anda
        }

        // Set nilai_pembimbing_dosen berdasarkan input pengguna
        $logBimbingan->nilai_pembimbing_dosen = $nilaiPembimbingDosen; // Gunakan nilai yang diambil dari input pengguna

        // Simpan perubahan
        $logBimbingan->save();

        // Kemudian kembalikan respons
        return response()->json(['success' => true]);
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
        $instrukturLapangan = InstrukturLapanganModel::where('mahasiswa_id', $mahasiswa_id)->get();
        $pembimbingdosen = PembimbingDosenModel::where('mahasiswa_id', $mahasiswa_id)->get();
        $dosen = []; // Inisialisasi array untuk menyimpan data dosen
        $instrktur = []; // Inisialisasi array untuk menyimpan data dosen

        foreach ($instrukturLapangan as $instruktur) {
            // Dapatkan dosen_id dari objek pembimbing
            $instruktur_id = $instruktur->instruktur_id;

            // Ambil data intsruktur berdasarkan instruktur_id
            $instrktur[] = InstrukturModel::findOrFail($instruktur_id);
        }
        foreach ($pembimbingdosen as $pembimbing) {
            // Dapatkan dosen_id dari objek pembimbing
            $dosen_id = $pembimbing->dosen_id;

            // Ambil data dosen berdasarkan dosen_id
            $dosen[] = DosenModel::findOrFail($dosen_id);
        }

        return view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('dosen', $dosen)
            ->with('instrktur', $instrktur);
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
                'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Menggunakan aturan image untuk validasi file gambar
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
                'pembimbing_dosen_id' => $request->input('pembimbing_dosen_id'),
                'instruktur_lapangan_id' => $request->input('instruktur_lapangan_id'),
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
        $instrukturLapangan = InstrukturLapanganModel::where('mahasiswa_id', $mahasiswa_id)->get();
        $pembimbingdosen = PembimbingDosenModel::where('mahasiswa_id', $mahasiswa_id)->get();
        $dosen = []; // Inisialisasi array untuk menyimpan data dosen
        $instrktur = []; // Inisialisasi array untuk menyimpan data dosen

        foreach ($instrukturLapangan as $instruktur) {
            // Dapatkan dosen_id dari objek pembimbing
            $instruktur_id = $instruktur->instruktur_id;

            // Ambil data intsruktur berdasarkan instruktur_id
            $instrktur[] = InstrukturModel::findOrFail($instruktur_id);
        }
        foreach ($pembimbingdosen as $pembimbing) {
            // Dapatkan dosen_id dari objek pembimbing
            $dosen_id = $pembimbing->dosen_id;

            // Ambil data dosen berdasarkan dosen_id
            $dosen[] = DosenModel::findOrFail($dosen_id);
        }
        $data = LogBimbinganModel::find($id);

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('dosen', $dosen)
            ->with('instrktur', $instrktur)
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
                'foto' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048', // Gunakan 'sometimes' agar validasi tidak wajib
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
        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
        $data = LogBimbinganModel::where('periode_id', $activePeriods)->find($id);
        if ($data) {
            $data->jam_mulai = substr($data->jam_mulai, 0, 5); // Ambil jam dan menit dari jam_mulai
            $data->jam_selesai = substr($data->jam_selesai, 0, 5); // Ambil jam dan menit dari jam_selesai
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

    public function updateStatusDosenFromModal(Request $request, $id)
    {
        // Validasi data yang diterima dari permintaan
        $request->validate([
            'status1' => 'required|in:0,1,2',
            'nilai_pembimbing_dosen' => 'required|numeric'
        ]);
        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
        // Ambil data yang dikirimkan melalui permintaan AJAX
        $statusDosen = $request->input('status1');
        $nilaiPembimbingDosen = $request->input('nilai_pembimbing_dosen');

        // Lakukan proses pembaruan status dosen pembimbing di sini
        $logBimbingan = LogBimbinganModel::where('periode_id', $activePeriods)->find($id);

        if (!$logBimbingan) {
            // Jika log bimbingan tidak ditemukan, kembalikan respons dengan pesan error
            return response()->json(['success' => false, 'message' => 'Log bimbingan tidak ditemukan']);
        }

        // Proses update status dosen pembimbing
        $logBimbingan->status1 = $statusDosen;

        // Set tanggal_status_dosen berdasarkan status yang diubah
        if ($statusDosen == 1) {
            // Jika status diubah menjadi 'diterima', atur tanggal_status_dosen menjadi tanggal saat ini
            $logBimbingan->tanggal_status_dosen = now();
        } else if ($statusDosen == 2) {
            // Jika status diubah menjadi 'ditolak', atur nilai_pembimbing_dosen menjadi 0
            $logBimbingan->nilai_pembimbing_dosen = 0;
            // Atur tanggal_status_dosen menjadi null atau kosong
            $logBimbingan->tanggal_status_dosen = now(); // Sesuaikan dengan preferensi Anda
        } else {
            // Jika status diubah menjadi 'pending', atur tanggal_status_dosen menjadi null atau kosong
            $logBimbingan->tanggal_status_dosen = null; // Sesuaikan dengan preferensi Anda
        }

        // Set nilai_pembimbing_dosen berdasarkan input pengguna
        $logBimbingan->nilai_pembimbing_dosen = $nilaiPembimbingDosen; // Gunakan nilai yang diambil dari input pengguna

        // Simpan perubahan
        $logBimbingan->save();

        // Kemudian kembalikan respons
        return response()->json(['success' => true]);
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
}

<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Master\DosenModel;
use App\Models\Master\InstrukturModel;
use App\Models\Master\JurusanModel;
use App\Models\Master\MahasiswaModel;
use App\Models\Master\PeriodeModel;
use App\Models\Setting\UserModel;
use App\Models\Master\ProdiModel;
use App\Models\Master\SemhasModel;
use App\Models\Transaction\InstrukturLapanganModel;
use App\Models\Transaction\KuotaDosenModel;
use App\Models\Transaction\LogBimbinganModel;
use App\Models\Transaction\Magang;
use Carbon\Carbon;
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

class SemhasDaftarController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'TRANSACTION.SEMHAS.DAFTAR';
        $this->menuUrl   = url('transaksi/seminarhasil-daftar');
        $this->menuTitle = 'Seminar Hasil Magang';
        $this->viewPath  = 'transaction.semhas-daftar.';
    }

    public function index()
    {
        $user = auth()->user();
        $user_id = $user->user_id;
        $mahasiswa = MahasiswaModel::where('user_id', $user_id)->first();
        $mahasiswa_id = $mahasiswa->mahasiswa_id;
        $prodi_id = $mahasiswa->prodi_id;

        // dd($prodi_name);
        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
        $magang_status = Magang::where('mahasiswa_id', $mahasiswa_id)
            ->where('status', 1)
            ->where('periode_id', $activePeriods)
            // Status 1 menunjukkan 'Diterima'
            ->exists();

        if ($magang_status) {
            $this->authAction('read');
            $this->authCheckDetailAccess();

            $breadcrumb = [
                'title' => $this->menuTitle,
                'list'  => ['Transaksi', 'Seminar Hasil']
            ];

            $activeMenu = [
                'l1' => 'transaction',
                'l2' => 'transaksi-semhas-daf',
                'l3' => null
            ];

            $page = [
                'url' => $this->menuUrl,
                'title' => 'Pendaftaran ' . $this->menuTitle
            ];
            $semhas = SemhasModel::where('prodi_id', $prodi_id)->first();
            // dd($semhas);
            if (!$semhas) {
                $this->authAction('read');
                $this->authCheckDetailAccess();

                $breadcrumb = [
                    'title' => $this->menuTitle,
                    'list'  => ['Transaksi', 'Seminar Hasil']
                ];

                $activeMenu = [
                    'l1' => 'transaction',
                    'l2' => 'transaksi-semhas-daf',
                    'l3' => null
                ];

                $page = [
                    'url' => $this->menuUrl,
                    'title' => 'Pendaftaran ' . $this->menuTitle
                ];
                $message = "Tidak ada Seminar Hasil (Semhas) yang tersedia untuk prodi Anda.";
                return view('transaction.semhas-daftar.index1')
                    ->with('breadcrumb', (object) $breadcrumb)
                    ->with('activeMenu', (object) $activeMenu)
                    ->with('page', (object) $page)
                    ->with('message', $message)
                    ->with('allowAccess', $this->authAccessKey());
            } else {
                $total_bimbingan = LogBimbinganModel::where('created_by', $user_id)
                    ->where('status1', 1)
                    ->where('status2', 1)
                    ->count();

                if ($total_bimbingan < $semhas->kuota_bimbingan) {

                    $message = "anda belum Eligible untuk mendaftar pada Tahap ini.";
                    $semhasData = $semhas;
                    $jurusan = JurusanModel::all()->first();
                    $jurusanName = $jurusan->jurusan_name;
                    $prodi_name = ProdiModel::find($prodi_id)->prodi_name;
                    // dd($prodi_name);
                    $this->authAction('read');
                    $this->authCheckDetailAccess();

                    $breadcrumb = [
                        'title' => $this->menuTitle,
                        'list'  => ['Transaksi', 'Seminar Hasil']
                    ];

                    $activeMenu = [
                        'l1' => 'transaction',
                        'l2' => 'transaksi-semhas-daf',
                        'l3' => null
                    ];

                    $page = [
                        'url' => $this->menuUrl,
                        'title' => 'Pendaftaran ' . $this->menuTitle
                    ];


                    return view('transaction.semhas-daftar.index2')
                        ->with('breadcrumb', (object) $breadcrumb)
                        ->with('activeMenu', (object) $activeMenu)
                        ->with('page', (object) $page)
                        ->with('message', $message)
                        ->with('semhasData', $semhasData)
                        ->with('jurusanName', $jurusanName)
                        ->with('prodi_name', $prodi_name)
                        ->with('allowAccess', $this->authAccessKey());
                } else {
                    $this->authAction('read');
                    $this->authCheckDetailAccess();

                    $breadcrumb = [
                        'title' => $this->menuTitle,
                        'list'  => ['Transaksi', 'Seminar Hasil']
                    ];

                    $activeMenu = [
                        'l1' => 'transaction',
                        'l2' => 'transaksi-semhas-daf',
                        'l3' => null
                    ];

                    $page = [
                        'url' => $this->menuUrl,
                        'title' => 'Pendaftaran ' . $this->menuTitle
                    ];
                    $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
                    $instrukturLapangan = InstrukturLapanganModel::where('mahasiswa_id', $mahasiswa_id)
                        ->where('periode_id', $activePeriods)
                        ->with('instruktur')
                        ->first();
                    $nama_instruktur = optional($instrukturLapangan->instruktur)->nama_instruktur;

                    // Mengambil pembimbing dosen untuk mahasiswa tertentu dengan relasi 'dosen'
                    $pembimbingDosen = PembimbingDosenModel::where('mahasiswa_id', $mahasiswa_id)
                        ->where('periode_id', $activePeriods)
                        ->with('dosen')->first();
                    $nama_dosen = optional($pembimbingDosen->dosen)->dosen_name;

                    $magang_ids = Magang::whereIn('mahasiswa_id', [$mahasiswa_id]) // Perhatikan penambahan tanda kurung siku untuk membungkus nilai dalam array
                        ->where('status', 1)
                        ->where('periode_id', $activePeriods)
                        ->pluck('magang_id');
                    // dd($magang_ids);

                    $magang = Magang::whereIn('magang_id', $magang_ids) // Perhatikan penggunaan whereIn() untuk memeriksa apakah $magang_ids ada di dalam array
                        ->where('mahasiswa_id', $mahasiswa_id) // Tambahkan klausa where untuk mahasiswa_id
                        ->with('mitra')
                        ->with('mitra.kegiatan')
                        ->with('periode')
                        ->where('periode_id', $activePeriods)
                        ->first();
                    // dd($magang);
                    $kode_magang = $magang->magang_kode;
                    // dd($total_bimbingan >= $semhas->kuota_bimbingan);
                    $success = "anda sudah Eligible untuk mendaftar pada Tahap ini.";
                    $successDaftar1 = "anda sudah Mendaftar untuk Tahap ini.";
                    $semhasData = $semhas;
                    $semhas_id = SemhasModel::where('prodi_id', $prodi_id)
                        ->where('tanggal_mulai_pendaftaran', '<=', now())
                        ->where('tanggal_akhir_pendaftaran', '>=', now())
                        ->value('semhas_id');

                    $instrukturLapangan = InstrukturLapanganModel::where('mahasiswa_id', $mahasiswa_id)->pluck('instruktur_lapangan_id')->first();
                    $pembimbingdosen = PembimbingDosenModel::where('mahasiswa_id', $mahasiswa_id)->pluck('pembimbing_dosen_id')->first();
                    // dd($pembimbingdosen);
                    $magang_id = Magang::where('mahasiswa_id', $mahasiswa_id)
                        ->where('status', 1)
                        ->value('magang_id');
                    $jurusan = JurusanModel::all()->first();
                    $jurusanName = $jurusan->jurusan_name;
                    $prodi_name = ProdiModel::find($prodi_id)->prodi_name;
                    $dataSemhasDaftar = SemhasDaftarModel::where('created_by', $user_id)
                        ->where('periode_id', $activePeriods)
                        ->first();
                    // dd($dataSemhasDaftar);
                    $dataSemhasDaftar1 = SemhasDaftarModel::where('periode_id', $activePeriods)
                        ->whereHas('magang', function ($query) use ($kode_magang) {
                            $query->where('magang_kode', $kode_magang);
                        })->get();
                    // dd($dataSemhasDaftar1);
                    if ($dataSemhasDaftar == null) {
                        return view($this->viewPath . 'index')
                            ->with('breadcrumb', (object) $breadcrumb)
                            ->with('activeMenu', (object) $activeMenu)
                            ->with('nama_instruktur', $nama_instruktur)
                            ->with('nama_dosen', $nama_dosen)
                            ->with('magang', $magang)
                            ->with('dataSemhasDaftar', $dataSemhasDaftar)
                            ->with('dataSemhasDaftar1', $dataSemhasDaftar1)
                            ->with('success', $success)
                            ->with('successDaftar1', $successDaftar1)
                            ->with('semhasData', $semhasData)
                            ->with('semhas_id', $semhas_id)
                            ->with('instrukturLapangan', $instrukturLapangan)
                            ->with('pembimbingdosen', $pembimbingdosen)
                            ->with('magang_id', $magang_id)
                            ->with('jurusanName', $jurusanName)
                            ->with('prodi_name', $prodi_name)
                            ->with('page', (object) $page)
                            ->with('allowAccess', $this->authAccessKey());
                    } else {
                        $this->authAction('read');
                        $this->authCheckDetailAccess();

                        $breadcrumb = [
                            'title' => $this->menuTitle,
                            'list'  => ['Transaksi', 'Seminar Hasil']
                        ];

                        $activeMenu = [
                            'l1' => 'transaction',
                            'l2' => 'transaksi-semhas-daf',
                            'l3' => null
                        ];

                        $page = [
                            'url' => $this->menuUrl,
                            'title' => 'Detail Proposal yang telah didaftarkan pada Tahap ini  '
                        ];
                        return view($this->viewPath . 'index')
                            ->with('breadcrumb', (object) $breadcrumb)
                            ->with('activeMenu', (object) $activeMenu)
                            ->with('successDaftar1', $successDaftar1)
                            ->with('nama_instruktur', $nama_instruktur)
                            ->with('nama_dosen', $nama_dosen)
                            ->with('magang', $magang)
                            ->with('prodi_name', $prodi_name)
                            ->with('dataSemhasDaftar', $dataSemhasDaftar)
                            ->with('success', $success)
                            ->with('semhasData', $semhasData)
                            ->with('semhas_id', $semhas_id)
                            ->with('instrukturLapangan', $instrukturLapangan)
                            ->with('pembimbingdosen', $pembimbingdosen)
                            ->with('magang_id', $magang_id)
                            ->with('jurusanName', $jurusanName)
                            ->with('page', (object) $page)
                            ->with('allowAccess', $this->authAccessKey());
                    }
                }
            }
        } else {
            $this->authAction('read');
            $this->authCheckDetailAccess();

            $breadcrumb = [
                'title' => $this->menuTitle,
                'list'  => ['Transaksi', 'Seminar Hasil']
            ];

            $activeMenu = [
                'l1' => 'transaction',
                'l2' => 'transaksi-semhas-daf',
                'l3' => null
            ];

            $page = [
                'url' => $this->menuUrl,
                'title' => 'Pendaftaran ' . $this->menuTitle
            ];
            $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
            $magang_status = Magang::where('mahasiswa_id', $mahasiswa_id)
                ->where('periode_id', $activePeriods)
                ->where('status', 0) // Status 0 menunjukkan 'Belum keterima'
                ->exists();

            if ($magang_status) {
                $message = "halaman belum bisa diakses. Silahkan untuk menunggu.";
            } elseif (Magang::where('mahasiswa_id', $mahasiswa_id)->exists()) {
                // Mahasiswa telah mendaftar magang tetapi belum diterima atau ditolak
                $message = "halaman belum bisa diakses. Silahkan untuk mendaftar ulang Magang.";
            } else {
                // Mahasiswa belum mendaftar magang
                $message = "halaman belum bisa diakses. Silahkan untuk mendaftar magang.";
            }

            return view('transaction.semhas-daftar.index1')
                ->with('breadcrumb', (object) $breadcrumb)
                ->with('activeMenu', (object) $activeMenu)
                ->with('page', (object) $page)
                ->with('message', $message)
                ->with('allowAccess', $this->authAccessKey());
        }
    }

    public function daftarSemhas(Request $request)
    {
        // dd($request->all());
        // Validasi request
        // Dapatkan ID pengguna yang sedang login
        $userId = Auth::id();
        $mahasiswa = MahasiswaModel::where('user_id', $userId)->first();
        $mahasiswa_id = $mahasiswa->mahasiswa_id;
        $prodi_id = $mahasiswa->prodi_id;

        $semhas = SemhasModel::where('prodi_id', $prodi_id)
            ->where('tanggal_mulai_pendaftaran', '<=', now())
            ->where('tanggal_akhir_pendaftaran', '>=', now())
            ->value('semhas_id');

        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
        $instrukturLapangan = InstrukturLapanganModel::where('mahasiswa_id', $mahasiswa_id)->pluck('instruktur_lapangan_id')->first();
        $pembimbingdosen = PembimbingDosenModel::where('mahasiswa_id', $mahasiswa_id)->pluck('pembimbing_dosen_id')->first();

        $magang_id = Magang::where('mahasiswa_id', $mahasiswa_id)
            ->where('status', 1)
            ->where('periode_id', $activePeriods)
            ->value('magang_id');

        // Periksa apakah dataSemhasDaftar1 tidak kosong sebelum mengakses propertinya
        $dataSemhasDaftar1 = SemhasDaftarModel::where('periode_id', $activePeriods)
            ->whereHas('magang', function ($query) use ($magang_id) {
                $query->where('magang_id', $magang_id);
            })->get();

        // Validasi request berdasarkan kebutuhan aplikasi
        if ($dataSemhasDaftar1->isEmpty()) {
            $request->validate([
                'link_github' => 'required',
                'link_laporan' => 'required',
                'Judul' => 'required' // Validasi hanya diterapkan jika input manual
            ]);
        } else {
            $request->validate([
                'link_github' => 'required',
                'link_laporan' => 'required',
            ]);
        }

        // Simpan nilai-nilai dalam variabel
        $dataToCreate = [
            'semhas_id' => $semhas,
            'magang_id' => $magang_id,
            'pembimbing_dosen_id' => $pembimbingdosen,
            'instruktur_lapangan_id' => $instrukturLapangan,
            'tanggal_daftar' => Carbon::now()->toDateString(), // Menggunakan tanggal dan waktu sekarang
            // Periksa apakah dataSemhasDaftar1 tidak kosong sebelum mengakses propertinya
            'Judul' => $dataSemhasDaftar1->isEmpty() ? $request->Judul : $dataSemhasDaftar1->first()->Judul,
            'link_github' => $request->link_github,
            'link_laporan' => $request->link_laporan,
            'periode_id' => $activePeriods,
            'created_by' => $userId,
        ];

        // Buat entri baru dalam tabel t_semhas_daftar jika dataSemhasDaftar1 kosong
        if ($dataSemhasDaftar1->isEmpty()) {
            SemhasDaftarModel::create($dataToCreate);
        }

        return redirect('/transaksi/seminarhasil-daftar');
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
        $data = LogBimbinganModel::select(
            'log_bimbingan_id',
            'tanggal',
            'topik_bimbingan',
            DB::raw('TIME_FORMAT(jam_mulai, "%H:%i") AS jam_mulai'),
            DB::raw('TIME_FORMAT(jam_selesai, "%H:%i") AS jam_selesai'),
            'status1',
            'status2'
        )
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
}

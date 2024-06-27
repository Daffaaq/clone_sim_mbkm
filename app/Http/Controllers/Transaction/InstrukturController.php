<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Master\InstrukturModel;
use App\Models\Master\MahasiswaModel;
use App\Models\Master\ProdiModel;
use App\Models\Setting\UserModel;
use App\Models\SuratPengantarModel;
use App\Models\Transaction\Magang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Models\DokumenMagangModel;
use App\Models\Master\PeriodeModel;
use App\Models\MitraModel;
use Illuminate\Validation\Rule;
use App\Models\Transaction\InstrukturLapanganModel;
use App\Models\Transaction\LogModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class InstrukturController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'TRANSACTION.INSTRUKTUR';
        $this->menuUrl   = url('transaksi/instruktur');     // set URL untuk menu ini
        $this->menuTitle = 'Instruktur';                       // set nama menu
        $this->viewPath  = 'transaction.instruktur.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $user = auth()->user();
        // $user = auth()->user()->id;
        $user_id = $user->user_id;
        $mahasiswa = MahasiswaModel::where('user_id', $user_id)->first();
        $mahasiswa_id = $mahasiswa->mahasiswa_id;

        // Gunakan mahasiswa_id untuk mencari data magang
        $magang_data = Magang::where('mahasiswa_id', $mahasiswa_id)->get();
        $activePeriods = PeriodeModel::where('is_current', 1)->pluck('periode_id');
        $magang_status = Magang::where('mahasiswa_id', $mahasiswa_id)
            ->where('status', 1) // Status 1 menunjukkan 'Diterima'
            ->where('periode_id', $activePeriods)
            ->exists();
        if ($magang_status) {
            $this->authAction('read');
            $this->authCheckDetailAccess();

            $breadcrumb = [
                'title' => $this->menuTitle,
                'list'  => ['Transaksi', 'Instruktur']
            ];

            $activeMenu = [
                'l1' => 'transaction',
                'l2' => 'transaksi-instruktur',
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
        } else {
            $this->authAction('read');
            $this->authCheckDetailAccess();

            $breadcrumb = [
                'title' => $this->menuTitle,
                'list'  => ['Transaksi', 'Instruktur']
            ];

            $activeMenu = [
                'l1' => 'transaction',
                'l2' => 'transaksi-instruktur',
                'l3' => null
            ];

            $page = [
                'url' => $this->menuUrl,
                'title' => 'Daftar ' . $this->menuTitle
            ];

            $magang_status = Magang::where('mahasiswa_id', $mahasiswa_id)
                ->where('status', 0)
                ->where('periode_id', $activePeriods->toArray()) // Status 0 menunjukkan 'Belum keterima'
                ->exists();

            if ($magang_status) {
                $message = "halaman belum bisa diakses. Silahkan untuk menunggu.";
            } elseif (Magang::where('mahasiswa_id', $mahasiswa_id)->exists()) {
                // Mahasiswa telah mendaftar magang tetapi belum diterima atau ditolak
                $message = "halaman belum bisa diakses. Silahkan untuk mendaftar ulang.";
            } else {
                // Mahasiswa belum mendaftar magang
                $message = "halaman belum bisa diakses. Silahkan untuk mendaftar magang.";
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
        $activePeriods = PeriodeModel::where('is_current', 1)->pluck('periode_id');
        $data  = Magang::with('mahasiswa')
            ->with('mitra')
            ->with('periode')
            ->where('periode_id', $activePeriods)
            ->with('prodi')
            ->with('mitra.kegiatan')
            ->where('status', 1);
        // dd($data);

        //where is_accept == NULL or is_accept == 1
        // $data = $data->where('is_accept', 0);

        if (auth()->user()->group_id == 1) {
            $data = $data->get();
        } else if (auth()->user()->group_id == 4) {
            $data = $data->where('mahasiswa_id', auth()->user()->getUserMahasiswa->mahasiswa_id)->get();
        } else {
            $prodi_id = auth()->user()->getProdiId();
            $data = $data->where('prodi_id', $prodi_id)->get();
        }
        // $intruktur = InstrukturLapanganModel::all();
        // dd($intruktur);
        $data = $data->map(function ($item) {
            $item['encrypt_magang_id'] = Crypt::encrypt($item->magang_id);
            $magang_id = Crypt::decrypt($item['encrypt_magang_id']);
            // dd($magang_id);
            // dd($magang_id);
            // Fetch the InstrukturLapanganModel for the specified magang_id
            $instrukturLapangan = InstrukturLapanganModel::where('magang_id', $magang_id)->first();
            // dd($instrukturLapangan);
            // dd($instrukturLapangan);
            if ($instrukturLapangan) {
                // If InstrukturLapanganModel exists, fetch the associated InstrukturModel
                $instruktur = $instrukturLapangan->instruktur;
                if ($instruktur) {
                    // If InstrukturModel exists, get the nama_instruktur
                    $item['nama_instruktur'] = $instruktur->nama_instruktur;
                } else {
                    // If InstrukturModel doesn't exist, set nama_instruktur to null
                    $item['nama_instruktur'] = 'Belum ada instruktur tersedia';
                }
            } else {
                // If InstrukturLapanganModel doesn't exist, set nama_instruktur to null
                $item['nama_instruktur'] = null;
                // Set a message indicating that instruktur is not available
                $item['instruktur_message'] = 'Belum ada instruktur tersedia';
            }
            return $item;
        });



        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function show($id)
    {
        $this->authAction('read', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $page = [
            'title' => 'Detail ' . $this->menuTitle
        ];

        $data = Magang::find($id);
        $activePeriods = PeriodeModel::where('is_current', 1)->pluck('periode_id');
        $mitra = MitraModel::where('mitra_id', $data->mitra_id)
            ->with('kegiatan')
            ->with('periode')
            ->where('periode_id', $activePeriods)
            ->first();

        if ($data->magang_tipe == 2) {
            $dokumen = DokumenMagangModel::where('magang_id', $id);
            $proposal = DokumenMagangModel::where('magang_id', $id);
            $surat_balasan = DokumenMagangModel::where('magang_id', $id);
            $anggota = NULL;
        } else {
            $magang = Magang::where('magang_kode', $data->magang_kode)->get();
            $id = $magang->pluck('magang_id');
            $dokumen = DokumenMagangModel::whereIn('magang_id', $id);
            $surat_balasan = DokumenMagangModel::whereIn('magang_id', $id);
            $anggota = ($data->magang_tipe == 0) ? Magang::whereIn('magang_id', $id)->with('mahasiswa')->where('magang_tipe', '=', 1)->get() : NULL;
        }

        $datas = [
            [
                "title" => "Proposal",
                "nama" => "PROPOSAL"
            ],
            [
                "title" => "Surat Balasan",
                "nama" => "SURAT_BALASAN"
            ]
        ];

        foreach ($datas as &$data) {
            $dokumenItem = $dokumen->where('dokumen_magang_nama', $data['nama'])->first();
            $data['value'] = $dokumenItem ? $dokumenItem->dokumen_magang_file : "Belum Ada File";
            $data['bold'] = false;
            $data['link'] = $dokumenItem ? true : false;
            unset($data['nama']);
        }

        if ($mitra->kegiatan->is_submit_proposal == 0) {
            unset($datas[0]);
        }

        // Convert to stdClass objects
        $datas = array_map(function ($item) {
            return (object) $item;
        }, $datas);


        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'detail')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data)
            ->with('datas', $datas)
            ->with('anggota', $anggota)
            ->with('mitra', $data);
    }

    public function lengkapi($id)
    {
        $this->authAction('read', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $id = Crypt::decrypt($id);
        $activePeriods = PeriodeModel::where('is_current', 1)->pluck('periode_id');
        $data = Magang::find($id);
        $magang_id = $data->magang_id;
        $kode_magang = $data->magang_kode;

        // $anggotas = Magang::where('magang_kode', $kode_magang)
        //     ->whereHas('mahasiswa') // Filter hanya Magang yang memiliki relasi dengan MahasiswaModel
        //     ->with('mahasiswa') // Sertakan relasi mahasiswa dalam hasil
        //     ->get();

        $anggota = Magang::where('magang_kode', $kode_magang)
            ->with('mahasiswa')
            ->where('periode_id', $activePeriods)
            ->get();
        $id_mitra = $data->mitra_id;
        // dd($id_mitra);
        $anggotas = Magang::where(function ($query) use ($id_mitra, $kode_magang) {
            $query->where('mitra_id', $id_mitra)
                ->orwhere('magang_kode', $kode_magang);
        })
            ->where('periode_id', $activePeriods)
            ->where('status', 1)
            ->with('mahasiswa')
            ->whereDoesntHave('instrukturLapangan')
            ->get();

        // dd($anggotas);
        $dateString = $data->mitra_batas_pendaftaran;
        $currentDate = date('Y-m-d');
        $disabled = strtotime($dateString) < strtotime($currentDate);

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Transaksi', 'Instruktur']
        ];

        $activeMenu = [
            'l1' => 'transaction',
            'l2' => 'transaksi-instruktur',
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl,
            'title' => $this->menuTitle
        ];

        $id_mahasiswa = MahasiswaModel::where('user_id', auth()->user()->user_id)->first()->mahasiswa_id;

        $magang = Magang::where('magang_id', $id)
            ->with('mitra')
            ->with('mitra.kegiatan')
            ->with('periode')
            ->where('periode_id', $activePeriods)
            ->first();
        $mag = Magang::where('magang_kode', $data->magang_kode)->where('magang_tipe', 1)->where('is_accept', 0)->count();
        $me = Magang::where('magang_kode', $data->magang_kode)->where('mahasiswa_id', $id_mahasiswa)->first();
        if ($me->magang_tipe == 0 || $me->magang_tipe == 2) {
            $magang->ketua = TRUE;
        } else {
            $magang->ketua = FALSE;
        }
        $check = Magang::where('magang_kode', $kode_magang)
            ->where('periode_id', $activePeriods)
            ->get();
        $id_joined = $check->pluck('magang_id');
        $user = auth()->user();
        // $user = auth()->user()->id;
        $user_id = $user->user_id;
        $mahasiswa = MahasiswaModel::where('user_id', $user_id)->first();
        $mahasiswa_id = $mahasiswa->mahasiswa_id;
        // $anggotas = Magang::where('magang_kode', $kode_magang)
        //     ->whereHas('mahasiswa', function ($query) use ($mahasiswa_id) {
        //         $query->where('mahasiswa_id', $mahasiswa_id);
        //     })
        //     ->with('mahasiswa')
        //     ->get();
        $instruktur = InstrukturLapanganModel::whereIn('magang_id', $id_joined)
            ->where('mahasiswa_id', $mahasiswa_id) // Menambahkan kriteria pencarian berdasarkan mahasiswa_id
            ->where('periode_id', $activePeriods)
            ->with('instruktur')
            ->first();
        // dd($instruktur);
        // $instruktur = InstrukturLapanganModel::where('magang_id', $id_joined)->first();

        return view($this->viewPath . 'update')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data)
            ->with('magang', $magang)
            ->with('instruktur', $instruktur)
            ->with('disabled', $disabled)
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $activeMenu)
            ->with('anggotas', $anggotas)
            ->with('anggota', $anggota)
            ->with('page', (object) $page)
            ->with('action', 'POST');
    }

    public function create_instruktur(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'nama_instruktur' => 'required',
            'instruktur_email' => 'required|email',
            'instruktur_phone' => 'required',
            'password' => 'required|string|min:8', // Misalnya, panjang minimal password 6 karakter
            'mahasiswa_id' => 'required|array|min:1' // Misalnya, harus berupa array
        ]);
        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
        // Jika validasi gagal, kembalikan respons dengan pesan error
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $nama_instruktur = $request->input('nama_instruktur');
        $instruktur_email = $request->input('instruktur_email');
        $instruktur_phone = $request->input('instruktur_phone');
        $password = Hash::make($request->input('password'));

        // Create user
        $user = [
            'username' => $instruktur_email,
            'name' => $nama_instruktur,
            'password' => $password,
            'group_id' => 5,
            'is_active' => 1,
            'email' => $instruktur_email,
        ];
        $insert = UserModel::create($user);
        $user_id = $insert->user_id;

        // Simpan data ke dalam InstrukturModel
        $insertInstruktur = InstrukturModel::create([
            'user_id' => $user_id,
            'nama_instruktur' => $nama_instruktur,
            'instruktur_email' => $instruktur_email,
            'instruktur_phone' => $instruktur_phone,
            'password' => $password,
            'periode_id' => PeriodeModel::where('is_current', 1)->value('periode_id')
        ]);
        LogModel::create([
            'user_id' => auth()->id(),
            'action' => 'create',
            'url' => $this->menuUrl,
            'data' => 'Nama Instruktur: ' . $insertInstruktur->nama_instruktur . ', Email Instruktur: ' . $insertInstruktur->instruktur_email,
            'created_by' => auth()->id(),
            'periode_id' => $activePeriods,
        ]);
        $instruktur_id = $insertInstruktur->instruktur_id;
        // Ambil magang_id dari input form
        // dd($request->all()); // Tampilkan semua data yang dikirimkan melalui form

        // Ambil data mahasiswa yang dipilih
        $mahasiswa_ids = $request->input('mahasiswa_id');

        // dd($mahasiswa_ids);
        $magang_ids = Magang::whereIn('mahasiswa_id', $mahasiswa_ids)
            ->where('status', 1)
            ->where('periode_id', $activePeriods)
            ->pluck('magang_id')
            ->toArray();
        // Inisialisasi variabel $insertInstrukturLapangan di luar blok foreach
        $insertInstrukturLapangan = null;
        // Periksa apakah ada mahasiswa yang dipilih
        if (!empty($mahasiswa_ids)) {
            // Loop untuk setiap mahasiswa yang dipilih
            foreach ($magang_ids as $magang_id) {
                // Loop untuk setiap mahasiswa yang dipilih
                $mahasiswa_id = array_shift($mahasiswa_ids);
                // Pastikan mahasiswa_id tidak null sebelum menyimpan data
                if ($mahasiswa_id) {
                    // Simpan data ke dalam InstrukturLapanganModel
                    $insertInstrukturLapangan = InstrukturLapanganModel::create([
                        'magang_id' => $magang_id,
                        'mahasiswa_id' => $mahasiswa_id,
                        'instruktur_id' => $instruktur_id, // Gunakan id instruktur yang baru saja dibuat
                        'periode_id' => $insertInstruktur->periode_id
                        // Isi kolom-kolom lainnya sesuai kebutuhan
                    ]);
                    $namaMahasiswa = $insertInstrukturLapangan->mahasiswa->nama_mahasiswa;
                    $namainstruktur = $insertInstrukturLapangan->instruktur->nama_instruktur;
                    LogModel::create([
                        'user_id' => auth()->id(),
                        'action' => 'create',
                        'url' => $this->menuUrl,
                        'data' => 'Nama Mahasiswa: ' . $namaMahasiswa . ', Nama Instruktur: ' . $namainstruktur,
                        'created_by' => auth()->id(),
                        'periode_id' => $activePeriods,
                    ]);
                }
            }
            // dd($magang_ids);
        } else {
            // Jika tidak ada mahasiswa yang dipilih, berikan pesan kesalahan
            return response()->json([
                'insert_instruktur' => $insertInstruktur,
                'insert_instruktur_lapangan' => null,
                'msg' => 'Tidak ada mahasiswa yang dipilih.'
            ]);
        }

        // Response JSON
        return response()->json([
            'insert_instruktur' => $insertInstruktur,
            'insert_instruktur_lapangan' => $insertInstrukturLapangan,
            'msg' => ($insertInstruktur && $insertInstrukturLapangan) ? $this->getMessage('insert.success') : $this->getMessage('insert.failed')
        ]);
    }




    public function create()
    {
        $this->authAction('create', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $user = auth()->user();
        // $user = auth()->user()->id;
        $user_id = $user->user_id;
        $mahasiswa = MahasiswaModel::where('user_id', $user_id)->first();
        $mahasiswa_id = $mahasiswa->mahasiswa_id;

        // Gunakan mahasiswa_id untuk mencari data magang
        $activePeriods = PeriodeModel::where('is_active', 1)->pluck('periode_id');
        $magang_data = Magang::where('mahasiswa_id', $mahasiswa_id)
            ->where('periode_id', $activePeriods->toArray())
            ->get();
        $magang_status = Magang::where('mahasiswa_id', $mahasiswa_id)
            ->where('status', 1) // Status 1 menunjukkan 'Diterima'
            ->where('periode_id', $activePeriods->toArray())
            ->exists();

        // dd($magang_status);
        // Jika mahasiswa belum memiliki status magang 'Diterima', kembalikan pesan
        if (!$magang_status) {
            return $this->showModalError('Kesalahan', 'Terjadi Kesalahan!!!', 'anda belum keterima Magang.');
        }
        $page = [
            'url' => $this->menuUrl,
            'title' => 'Tambah ' . $this->menuTitle
        ];

        $instruktur = InstrukturModel::selectRaw("instruktur_id, nama_instruktur, instruktur_email")->get();

        return view($this->viewPath . 'action')
            ->with('instruktur', $instruktur)
            ->with('page', (object) $page);
    }


    public function store(Request $request)
    {
        $this->authAction('create', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
                'instruktur_email' => ['required', 'email:rfc,dns,filter', 'max:50',],
                'nama_instruktur' => 'required|string|max:100',
                'instruktur_phone' => 'required|string|max:15',
                'password' => 'required|string|min:8',
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

            // Create user
            $user = [
                'username' => $request->input('instruktur_email'),
                'name' => $request->input('nama_instruktur'),
                'password' => Hash::make($request->input('password')),
                'group_id' => 5,
                'is_active' => 1,
                'email' => $request->input('instruktur_email'),
            ];
            $insert = UserModel::create($user);
            $request['user_id'] = $insert->user_id;
            $res = InstrukturModel::create([
                'instruktur_email' => $request->input('instruktur_email'),
                'nama_instruktur' => $request->input('nama_instruktur'),
                'instruktur_phone' => $request->input('instruktur_phone'),
                'password' => Hash::make($request->input('password')),
                'user_id' => $insert->user_id,
            ]);

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

        $data = InstrukturModel::find($id);

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
                'instruktur_email' => [
                    'sometimes',
                    'email:rfc,dns,filter',
                    'max:20',
                    Rule::unique('m_instruktur', 'instruktur_email')->ignore($id, 'instruktur_id'),
                ],
                'nama_instruktur' => 'required|string|max:100',
                'instruktur_phone' => 'required|string|max:15',
                'password' => 'required|string|min:8',
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
            if ($request->has('instruktur_email')) {
                // Cek apakah pengguna dengan email tersebut sudah ada
                $existingUser = UserModel::where('email', $request->input('instruktur_email'))->first();

                if ($existingUser) {
                    // Jika email sudah ada, update data pengguna yang sudah ada
                    $existingUser->update([
                        'name' => $request->input('nama_instruktur'),
                    ]);
                    $request['user_id'] = $existingUser->user_id;
                } else {
                    // Jika email belum ada, abaikan pembuatan pengguna baru
                    unset($request['user_id']);
                }
            }

            $res = InstrukturModel::updateData($id, $request);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? $this->getMessage('update.success') : $this->getMessage('update.failed')
            ]);
        }

        return redirect('/');
    }

    // public function show($id)
    // {
    //     $this->authAction('read', 'modal');
    //     if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

    //     $data = InstrukturModel::find($id);
    //     $page = [
    //         'title' => 'Detail ' . $this->menuTitle
    //     ];

    //     return (!$data) ? $this->showModalError() :
    //         view($this->viewPath . 'detail')
    //         ->with('page', (object) $page)
    //         ->with('id', $id)
    //         ->with('data', $data);
    // }


    public function confirm($id)
    {
        $this->authAction('delete', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = InstrukturModel::find($id);

        return (!$data) ? $this->showModalError() :
            $this->showModalConfirm($this->menuUrl . '/' . $id, [
                'Kode' => $data->jurusan_code,
                'Jurusan' => $data->jurusan_name,
            ]);
    }

    public function destroy(Request $request, $id)
    {
        $this->authAction('delete', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $res = InstrukturModel::deleteData($id);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => InstrukturModel::getDeleteMessage()
            ]);
        }

        return redirect('/');
    }
}

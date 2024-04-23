<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Master\DosenModel;
use App\Models\Master\MahasiswaModel;
use App\Models\Setting\UserModel;
use App\Models\Master\ProdiModel;
use App\Models\Master\SemhasModel;
use App\Models\Transaction\JadwalSidangMagangModel;
use App\Models\Transaction\KuotaDosenModel;
use App\Models\Transaction\PembimbingDosenModel;
use App\Models\Transaction\SemhasDaftarModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class JadwalSidangMagangController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'TRANSACTION.JADWAL.SEMHAS';
        $this->menuUrl   = url('transaksi/jadwal-semhas');
        $this->menuTitle = 'Jadwal Seminar Hasil';
        $this->viewPath  = 'transaction.jadwal-semhas.';
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Transaksi', 'Jadwal Seminar Hasil']
        ];

        $activeMenu = [
            'l1' => 'transaction',
            'l2' => 'transaksi-jasem',
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl,
            'title' => 'Daftar ' . $this->menuTitle
        ];
        $datall = SemhasDaftarModel::all();
        // dd($datall);
        $datapembimbinglapangan = MahasiswaModel::all();
        // dd($datapembimbinglapangan);
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

        // $data = SemhasDaftarModel::leftJoin('s_user', 'semhas_daftar.created_by', '=', 's_user.user_id')
        //     ->leftJoin('m_mahasiswa', 's_user.user_id', '=', 'm_mahasiswa.user_id')
        //     ->leftJoin('t_pembimbing_dosen', 'semhas_daftar.pembimbing_dosen_id', '=', 't_pembimbing_dosen.pembimbing_dosen_id')
        //     ->leftJoin('t_instruktur_lapangan', 'semhas_daftar.instruktur_lapangan_id', '=', 't_instruktur_lapangan.instruktur_lapangan_id')
        //     ->leftJoin('m_dosen', 't_pembimbing_dosen.dosen_id', '=', 'm_dosen.dosen_id')
        //     ->leftJoin('m_instruktur', 't_instruktur_lapangan.instruktur_id', '=', 'm_instruktur.instruktur_id')
        //     ->select(
        //         'semhas_daftar_id',
        //         'm_mahasiswa.nama_mahasiswa',
        //         'Judul',
        //         'm_dosen.dosen_name AS nama_dosen', // Ubah relasi dan nama kolom di sini
        //         'm_instruktur.nama_instruktur AS nama_instruktur',
        //     )
        //     ->get();
        $data = SemhasDaftarModel::leftJoin('s_user', 't_semhas_daftar.created_by', '=', 's_user.user_id')
            ->leftJoin('m_mahasiswa', 's_user.user_id', '=', 'm_mahasiswa.user_id')
            ->leftJoin('t_pembimbing_dosen', 't_semhas_daftar.pembimbing_dosen_id', '=', 't_pembimbing_dosen.pembimbing_dosen_id')
            ->leftJoin('m_dosen', 't_pembimbing_dosen.dosen_id', '=', 'm_dosen.dosen_id')
            ->leftJoin('t_instruktur_lapangan', 't_semhas_daftar.instruktur_lapangan_id', '=', 't_instruktur_lapangan.instruktur_lapangan_id')
            ->leftJoin('m_instruktur', 't_instruktur_lapangan.instruktur_id', '=', 'm_instruktur.instruktur_id')
            ->select(
                't_semhas_daftar.semhas_daftar_id',
                'm_mahasiswa.nama_mahasiswa',
                't_semhas_daftar.Judul',
                'm_dosen.dosen_name AS nama_dosen',
                'm_instruktur.nama_instruktur AS nama_instruktur'
            )
            ->get();

        // $dataall = SemhasDaftarModel::all();
        // dd($dataall);
        // dd($data);
        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    // public function create()
    // {
    //     $this->authAction('create', 'modal');
    //     if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

    //     $page = [
    //         'url' => $this->menuUrl,
    //         'title' => 'Tambah ' . $this->menuTitle
    //     ];
    //     $prodi_id = auth()->user()->prodi_id;
    //     $prodis = ProdiModel::selectRaw("prodi_id, prodi_name, prodi_code")->get();

    //     return view($this->viewPath . 'action')
    //         ->with('page', (object) $page)
    //         ->with('prodis', $prodis);
    // }

    // public function store(Request $request)
    // {
    //     $this->authAction('create', 'json');

    //     if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

    //     if ($request->ajax() || $request->wantsJson()) {
    //         $rules = [
    //             'judul_semhas' => 'required|string|max:100',
    //             'gelombang' => 'required|integer',
    //             'kuota_bimbingan' => 'required|integer',
    //             'tanggal_mulai_pendaftaran' => 'required|date',
    //             'tanggal_akhir_pendaftaran' => 'required|date',
    //             'prodi_id' => 'required',
    //             // Add other rules for DosenModel fields
    //         ];
    //         $validator = Validator::make($request->all(), $rules);
    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'stat' => false,
    //                 'mc' => false,
    //                 'msg' => 'Terjadi kesalahan.',
    //                 'msgField' => $validator->errors()
    //             ]);
    //         }
    //         $Semhas = SemhasModel::insertData($request);

    //         return response()->json([
    //             'stat' => $Semhas,
    //             'mc' => $Semhas,
    //             'msg' => ($Semhas) ? $this->getMessage('insert.success') : $this->getMessage('insert.failed')
    //         ]);
    //     }

    //     return redirect('/');
    // }


    public function edit($id)
    {
        $this->authAction('update', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $page = [
            'url' => $this->menuUrl . '/' . $id,
            'title' => 'Edit ' . $this->menuTitle
        ];

        $dosen = DosenModel::selectRaw("dosen_id, dosen_name")->get();
        $data = SemhasDaftarModel::find($id);

        // dd($pembimbing_dosen_id);
        // $datapembimbinglapangan = PembimbingDosenModel::all();
        // dd($datapembimbinglapangan);
        // $pembimbing_dosen_id = $data->pembimbing_dosen_id;
        // $datadosen = PembimbingDosenModel::pluck('dosen_id')->toArray();
        // dd($datadosen);

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data)
            ->with('dosen', $dosen);
    }

    public function update(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'dosen_pembahas_id' => 'required|integer',
                'tanggal_sidang' => 'required|date',
                'jam_sidang_mulai' => 'required',
                'jam_sidang_selesai' => 'required',
                'jenis_sidang' => 'required',
                'tempat' => 'required|string|max:255',
                'gedung' => 'nullable|string|max:255',
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

            // $res = SemhasModel::updateData($id, $request);
            $semhasDaftar = SemhasDaftarModel::findOrFail($id);
            $pembimbing_dosen_id = $semhasDaftar->pembimbing_dosen_id;
            $datadosen = PembimbingDosenModel::pluck('dosen_id')->toArray();
            // Dapatkan dosen_pembahas_id yang dipilih oleh pengguna dalam dropdown
            $dosen_pembahas_id_terpilih = $request->dosen_pembahas_id;
            // Periksa apakah dosen_pembahas_id yang dipilih tidak ada dalam kumpulan dosen_id
            if (!in_array($dosen_pembahas_id_terpilih, $datadosen)) {
                // Lakukan pembaruan data jika dosen_pembahas_id yang dipilih tidak ada dalam kumpulan dosen_id
                $semhasDaftar->update(['dosen_pembahas_id' => $dosen_pembahas_id_terpilih]);
                // Buat entri baru di JadwalSidangMagangModel
                $jadwalSidangMagang = new JadwalSidangMagangModel();
                $jadwalSidangMagang->semhas_daftar_id = $semhasDaftar->semhas_daftar_id;
                $jadwalSidangMagang->tanggal_sidang = $request->tanggal_sidang;
                $jadwalSidangMagang->jam_sidang_mulai = $request->jam_sidang_mulai;
                $jadwalSidangMagang->jam_sidang_selesai = $request->jam_sidang_selesai;
                $jadwalSidangMagang->jenis_sidang = $request->jenis_sidang;
                $jadwalSidangMagang->tempat = $request->tempat;
                $jadwalSidangMagang->gedung = $request->gedung;
                $jadwalSidangMagang->save();

                return response()->json([
                    'stat' => $semhasDaftar && $jadwalSidangMagang,
                    'mc' => $semhasDaftar && $jadwalSidangMagang, // close modal
                    'msg' => ($semhasDaftar && $jadwalSidangMagang) ? $this->getMessage('update.success') : $this->getMessage('update.failed')
                ]);
            } else {
                // Jika dosen_pembahas_id yang dipilih ada dalam kumpulan dosen_id, berikan respons JSON dengan pesan kesalahan
                return response()->json([
                    'stat' => false,
                    'mc' => false,
                    'msg' => 'Dosen pembimbing yang dipilih sama dengan pembimbing dosen saat ini.'
                ]);
            }
        }

        return redirect('/');
    }

    public function show($id)
    {
        $this->authAction('read', 'modal');
        // if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = SemhasModel::find($id);
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

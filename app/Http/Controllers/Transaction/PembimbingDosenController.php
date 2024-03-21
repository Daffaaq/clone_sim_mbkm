<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Master\DosenModel;
use App\Models\Master\InstrukturModel;
use App\Models\Master\MahasiswaModel;
use App\Models\Transaction\InstrukturLapanganModel;
use App\Models\Transaction\Magang;
use App\Models\Transaction\PembimbingDosenModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'l2' => 'transaksi-pembimbing-dosen',
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl,
            'title' => $this->menuTitle
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
            ->where('t_magang.status', 1) // Pastikan status magang adalah 1 (diterima)
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
        // Ambil mahasiswa yang sudah memiliki magang dengan status 1 dan belum memiliki entri dalam PembimbingDosenModel
        $mahasiswaWithMagang = MahasiswaModel::selectRaw("m_mahasiswa.mahasiswa_id, m_mahasiswa.nama_mahasiswa, t_magang.magang_id")
            ->join('t_magang', 't_magang.mahasiswa_id', '=', 'm_mahasiswa.mahasiswa_id')
            ->where('t_magang.status', 1)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('t_pembimbing_dosen')
                    ->whereRaw('t_magang.magang_id = t_pembimbing_dosen.magang_id');
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

        $dosen = DosenModel::selectRaw("dosen_id, dosen_name")->get();
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
            // dd($validator);
            if ($validator->fails()) {
                return response()->json([
                    'stat' => false,
                    'mc' => false,
                    'msg' => 'Terjadi kesalahan.',
                    'msgField' => $validator->errors()
                ]);
            }
            $mahasiswa_ids = $request->input('mahasiswa_id');
            // dd($mahasiswa_ids);
            $pembimbingDosen = null;
            $magang_ids = [];
            if (!empty($mahasiswa_ids)) {
                // Loop untuk setiap mahasiswa yang dipilih
                foreach ($mahasiswa_ids as $mahasiswa_id) {
                    // Pastikan mahasiswa_id tidak null sebelum menyimpan data
                    if ($mahasiswa_id) {
                        $magang_id = Magang::where('mahasiswa_id', $mahasiswa_id)->value('magang_id');
                        $magang_ids[] = $magang_id;
                        // Simpan data ke dalam InstrukturLapanganModel
                        $pembimbingDosen = PembimbingDosenModel::create([
                            'magang_id' => $magang_id,
                            'mahasiswa_id' => $mahasiswa_id,
                            'dosen_id' => $request->input('dosen_id') // Gunakan id instruktur yang baru saja dibuat
                            // Isi kolom-kolom lainnya sesuai kebutuhan
                        ]);
                    }
                }
                // dd($magang_ids);
            } else {
                // Jika tidak ada mahasiswa yang dipilih, berikan pesan kesalahan
                return response()->json([
                    'msg' => 'Tidak ada mahasiswa yang dipilih.'
                ]);
            }
            return response()->json([
                'stat' => $pembimbingDosen,
                'mc' => $pembimbingDosen,
                'msg' => $pembimbingDosen ? $this->getMessage('insert.success') : $this->getMessage('insert.failed')
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
        // dd($page);

        $mahasiswaWithMagang = MahasiswaModel::selectRaw("m_mahasiswa.mahasiswa_id, m_mahasiswa.nama_mahasiswa, t_magang.magang_id")
            ->join('t_magang', 't_magang.mahasiswa_id', '=', 'm_mahasiswa.mahasiswa_id')
            ->where('t_magang.status', 1)
            ->get();

        // Buat array untuk menyimpan mahasiswa beserta magang_id-nya
        $mahasiswa = [];
        foreach ($mahasiswaWithMagang as $data) {
            $mahasiswa[$data->mahasiswa_id] = [
                'nama_mahasiswa' => $data->nama_mahasiswa,
                'magang_id' => $data->magang_id
            ];
        }

        $dosen = DosenModel::selectRaw("dosen_id, dosen_name")->get();
        // $prodi = ProdiModel::selectRaw("prodi_id, prodi_name, prodi_code")->get();
        $data = PembimbingDosenModel::find($id);
        // dd($data);

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data)
            ->with('mahasiswa', $mahasiswa)
            ->with('dosen', $dosen);;
    }

    public function update(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
                'mahasiswa_id' => 'required|exists:m_mahasiswa,mahasiswa_id',
                'dosen_id' => 'required|exists:m_dosen,dosen_id',
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

            $res = PembimbingDosenModel::updateData($id, $request);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? $this->getMessage('update.success') : $this->getMessage('update.failed')
            ]);
        }

        return redirect('/');
    }
}

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
use App\Models\Transaction\LogModel;
use App\Models\Transaction\PembimbingDosenModel;
use App\Models\Transaction\PenilaianMahasiswaModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DosenPenilaianMahasiswaController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'DOSEN.PEMBIMBING.PENILAIAN.MAHASISWA.DOSEN';
        $this->menuUrl   = url('dosen-pembimbing/penilaian-mahasiswa-dosen');
        $this->menuTitle = 'Penilaian Mahasiswa Dosen';
        $this->viewPath  = 'transaction.penilaian-mahasiswa-dosen.';
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Dosen Pembimbing', 'Penilaian Mahasiswa Dosen']
        ];

        $activeMenu = [
            'l1' => 'dosen-pembimbing',
            'l2' => 'dosenpem-penmados',
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl,
            'title' => 'Daftar ' . $this->menuTitle
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

        // Dapatkan instruktur lapangan terkait dengan pengguna saat ini
        $dosen = DosenModel::where('user_id', auth()->user()->user_id)->first();
        if (!$dosen) {
            // Handle jika dosen tidak ditemukan
            return response()->json(['error' => 'dosen not found'], 404);
        }

        // Dapatkan ID periode yang aktif
        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');

        // Dapatkan ID mahasiswa yang terhubung dengan dosen
        $mahasiswa_ids = PembimbingDosenModel::where('dosen_id', $dosen->dosen_id)
            ->whereIn('magang_id', function ($query) use ($activePeriods) {
                $query->select('magang_id')
                    ->from('t_magang')
                    ->where('periode_id', $activePeriods);
            })
            ->pluck('mahasiswa_id')
            ->toArray();
        // dd($mahasiswa_ids);

        // Dapatkan nama mahasiswa berdasarkan ID dari MahasiswaModel
        $mahasiswa_names = MahasiswaModel::whereIn('mahasiswa_id', $mahasiswa_ids)->pluck('nama_mahasiswa', 'mahasiswa_id');

        // Dapatkan nilai dan komentar instruktur lapangan berdasarkan ID mahasiswa
        $penilaian_mahasiswa = PenilaianMahasiswaModel::whereIn('mahasiswa_id', $mahasiswa_ids)->where('periode_id', $activePeriods)->get();


        // Buat data untuk ditampilkan dalam DataTables
        $data = [];
        foreach ($mahasiswa_names as $mahasiswa_id => $nama_mahasiswa) {
            // Cari nilai dan komentar yang sesuai dengan mahasiswa ini
            $nilai_komentar = $penilaian_mahasiswa->where('mahasiswa_id', $mahasiswa_id)->first();

            // Tambahkan data mahasiswa beserta nilai dan komentar ke dalam array $data
            $data[] = [
                'no' => count($data) + 1,
                'mahasiswa' => [
                    'nama_mahasiswa' => $nama_mahasiswa,
                    'mahasiswa_id' => $mahasiswa_id
                ],
                'nilai_dosen_pembimbing' => $nilai_komentar ? $nilai_komentar->nilai_dosen_pembimbing : null,
                'komentar_dosen_pembimbing' => $nilai_komentar ? $nilai_komentar->komentar_dosen_pembimbing : null,
                'penilaian_mahasiswa_id' => $nilai_komentar ? $nilai_komentar->penilaian_mahasiswa_id : null
            ];
        }

        // $penilaian_mahasiswaall = PenilaianMahasiswaModel::all();
        // dd($penilaian_mahasiswaall);

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function updatedataPenilaianMahasiswa(Request $request)
    {
        // Validasi data yang diterima dari request
        $request->validate([
            'mahasiswa_id' => 'required', // Pastikan mahasiswa_id tidak boleh kosong
            'pembimbing_dosen_id' => 'required', // Pastikan instruktur_lapangan_id tidak boleh kosong
            'nilai_dosen_pembimbing' => 'required', // Pastikan nilai_instruktur_lapangan tidak boleh kosong
            'komentar_dosen_pembimbing' => 'required', // Komentar_instruktur_lapangan boleh kosong
        ]);
        $dosen = DosenModel::where('user_id', auth()->user()->user_id)->first();
        if (!$dosen) {
            // Handle jika dosen tidak ditemukan
            return response()->json(['error' => 'dosen not found'], 404);
        }

        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
        $existingData = PenilaianMahasiswaModel::where('mahasiswa_id', $request->mahasiswa_id)->where('periode_id', $activePeriods)->first();
        // Dapatkan ID mahasiswa yang terkait dengan dosen lapangan
        $dosen_id = PembimbingDosenModel::where('dosen_id', $dosen->dosen_id)->where('periode_id', $activePeriods)->pluck('pembimbing_dosen_id')->first();
        // dd($instruktur_id);
        $mahasiswa_id = $request->mahasiswa_id;

        if ($existingData) {
            // Jika entri sudah ada, gunakan nilai yang telah ada untuk update
            $existingData->update([
                'komentar_dosen_pembimbing' => $request->komentar_dosen_pembimbing,
                'nilai_dosen_pembimbing' => $request->nilai_dosen_pembimbing,
                'pembimbing_dosen_id' => $dosen_id,
                // Tambahkan kolom lainnya sesuai kebutuhan
            ]);
            LogModel::create([
                'user_id' => auth()->id(),
                'action' => 'update',
                'url' => $this->menuUrl,
                'data' => 'Komentar Dosen Pembimbing: ' . $existingData->komentar_dosen_pembimbing . ', Nilai Pembimbing Dosen: ' . $existingData->nilai_dosen_pembimbing,
                'created_by' => auth()->id(),
                'periode_id' => $activePeriods,
            ]);
        } else {
            // Jika entri belum ada, buat entri baru
            PenilaianMahasiswaModel::create([
                'mahasiswa_id' => $request->mahasiswa_id,
                'pembimbing_dosen_id' => $dosen_id,
                'komentar_dosen_pembimbing' => $request->komentar_dosen_pembimbing,
                'nilai_dosen_pembimbing' => $request->nilai_dosen_pembimbing,
                'periode_id' => $activePeriods,
                // Tambahkan kolom lainnya sesuai kebutuhan
            ]);
            LogModel::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'url' => $this->menuUrl,
                'data' => 'Komentar Dosen Pembimbing: ' . $existingData->komentar_dosen_pembimbing . ', Nilai Pembimbing Dosen: ' . $existingData->nilai_dosen_pembimbing,
                'created_by' => auth()->id(),
                'periode_id' => $activePeriods,
            ]);
        }

        // Kemudian kembalikan respons sukses
        return response()->json(['success' => true]);
    }
}

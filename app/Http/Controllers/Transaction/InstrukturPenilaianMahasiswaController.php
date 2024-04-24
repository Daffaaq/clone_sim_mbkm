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
use App\Models\Transaction\PenilaianMahasiswaModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InstrukturPenilaianMahasiswaController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'TRANSACTION.PENILAIAN.MAHASISWA.INSTRUKTUR';
        $this->menuUrl   = url('transaksi/penilaian-mahasiswa-instruktur');
        $this->menuTitle = 'Penilaian Mahasiswa Instruktur';
        $this->viewPath  = 'transaction.penilaian-mahasiswa-instruktur.';
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Transaksi', 'Penilaian Mahasiswa Instruktur']
        ];

        $activeMenu = [
            'l1' => 'transaction',
            'l2' => 'transaksi-penmainst',
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl,
            'title' => 'Daftar ' . $this->menuTitle
        ];
        // dd($page);

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
        $instruktur = InstrukturModel::where('user_id', auth()->user()->user_id)->first();
        if (!$instruktur) {
            // Handle jika instruktur tidak ditemukan
            return response()->json(['error' => 'Instruktur not found'], 404);
        }

        // Dapatkan ID periode yang aktif
        $activePeriods = PeriodeModel::where('is_active', 1)->pluck('periode_id');
        $mahasiswa_ids = InstrukturLapanganModel::where('instruktur_id', $instruktur->instruktur_id)
            ->whereIn('magang_id', function ($query) use ($activePeriods) {
                $query->select('magang_id')
                    ->from('t_magang')
                    ->where('periode_id', $activePeriods->toArray());
            })
            ->pluck('mahasiswa_id')
            ->toArray();
        // $mahasiswa_ids = InstrukturLapanganModel::where('instruktur_id', $instruktur->instruktur_id)->pluck('mahasiswa_id')->toArray();

        // Dapatkan nama mahasiswa berdasarkan ID dari MahasiswaModel
        $mahasiswa_names = MahasiswaModel::whereIn('mahasiswa_id', $mahasiswa_ids)->pluck('nama_mahasiswa', 'mahasiswa_id');

        // Dapatkan nilai dan komentar instruktur lapangan berdasarkan ID mahasiswa
        $penilaian_mahasiswa = PenilaianMahasiswaModel::whereIn('mahasiswa_id', $mahasiswa_ids)->get();


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
                'nilai_instruktur_lapangan' => $nilai_komentar ? $nilai_komentar->nilai_instruktur_lapangan : null,
                'komentar_instruktur_lapangan' => $nilai_komentar ? $nilai_komentar->komentar_instruktur_lapangan : null,
                'penilaian_mahasiswa_id' => $nilai_komentar ? $nilai_komentar->penilaian_mahasiswa_id : null
            ];
        }

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function updatedataPenilaianMahasiswa(Request $request)
    {
        // Validasi data yang diterima dari request
        $request->validate([
            'mahasiswa_id' => 'required', // Pastikan mahasiswa_id tidak boleh kosong
            'instruktur_lapangan_id' => 'required', // Pastikan instruktur_lapangan_id tidak boleh kosong
            'nilai_instruktur_lapangan' => 'required', // Pastikan nilai_instruktur_lapangan tidak boleh kosong
            'komentar_instruktur_lapangan' => 'required', // Komentar_instruktur_lapangan boleh kosong
        ]);
        $instruktur = InstrukturModel::where('user_id', auth()->user()->user_id)->first();
        if (!$instruktur) {
            // Handle jika instruktur tidak ditemukan
            return response()->json(['error' => 'Instruktur not found'], 404);
        }

        // Periksa apakah entri untuk mahasiswa dengan ID yang diberikan sudah ada
        $existingData = PenilaianMahasiswaModel::where('mahasiswa_id', $request->mahasiswa_id)->first();

        // Dapatkan ID mahasiswa yang terkait dengan instruktur lapangan
        $instruktur_id = InstrukturLapanganModel::where('instruktur_id', $instruktur->instruktur_id)->pluck('instruktur_lapangan_id')->first();
        // dd($instruktur_id);
        $mahasiswa_id = $request->mahasiswa_id;

        if ($existingData) {
            // Jika entri sudah ada, gunakan nilai yang telah ada untuk update
            $existingData->update([
                'komentar_instruktur_lapangan' => $request->komentar_instruktur_lapangan,
                'nilai_instruktur_lapangan' => $request->nilai_instruktur_lapangan,
                'instruktur_lapangan_id' => $instruktur_id,
                // Tambahkan kolom lainnya sesuai kebutuhan
            ]);
        } else {
            // Jika entri belum ada, buat entri baru
            PenilaianMahasiswaModel::create([
                'mahasiswa_id' => $request->mahasiswa_id,
                'instruktur_lapangan_id' => $instruktur_id,
                'komentar_instruktur_lapangan' => $request->komentar_instruktur_lapangan,
                'nilai_instruktur_lapangan' => $request->nilai_instruktur_lapangan,
                // Tambahkan kolom lainnya sesuai kebutuhan
            ]);
        }

        // Kemudian kembalikan respons sukses
        return response()->json(['success' => true]);
    }
}

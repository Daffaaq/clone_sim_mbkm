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

class PenilaianMahasiswaController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'TRANSACTION.PENILAIAN.MAHASISWA';
        $this->menuUrl   = url('transaksi/penilaian-mahasiswa');
        $this->menuTitle = 'Penilaian Mahasiswa';
        $this->viewPath  = 'transaction.penilaian-mahasiswa.';
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Transaksi', 'Penilaian Mahasiswa Dosen']
        ];

        $activeMenu = [
            'l1' => 'transaction',
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

        // Dapatkan ID periode yang aktif
        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');

        // Dapatkan data penilaian mahasiswa untuk periode aktif
        // $data = PenilaianMahasiswaModel::where('periode_id', $activePeriods)
        //     ->select('mahasiswa_id', 'pembimbing_dosen_id', 'instruktur_lapangan_id', 'komentar_dosen_pembimbing', 'komentar_instruktur_lapangan', 'nilai_dosen_pembimbing', 'nilai_instruktur_lapangan')
        //     ->with([
        //         'mahasiswa' => function ($query) {
        //             $query->select('mahasiswa_id', 'nama_mahasiswa','prodi_id');
        //         },
        //         'pembimbingDosen.dosen' => function ($query) {
        //             $query->select('dosen_id', 'dosen_name'); // Corrected column name
        //         },
        //         'instrukturLapangan.instruktur' => function ($query) {
        //             $query->select('instruktur_id', 'nama_instruktur');
        //         }
        //     ]);
        
        $data = PenilaianMahasiswaModel::where('periode_id', $activePeriods)
    ->select('mahasiswa_id', 'pembimbing_dosen_id', 'instruktur_lapangan_id', 'komentar_dosen_pembimbing', 'komentar_instruktur_lapangan', 'nilai_dosen_pembimbing', 'nilai_instruktur_lapangan')
    ->with([
        'mahasiswa' => function ($query) {
            $query->select('mahasiswa_id', 'nama_mahasiswa', 'prodi_id');
        },
        'pembimbingDosen.dosen' => function ($query) {
            $query->select('dosen_id', 'dosen_name'); // Adjusted column name
        },
        'instrukturLapangan.instruktur' => function ($query) {
            $query->select('instruktur_id', 'nama_instruktur');
        }
    ]);
    
    if (auth()->user()->group_id == 1) {
            $data = $data->get();
        } else {
            $prodi_id = auth()->user()->getProdiId();
            $data = $data->get()->filter(function($item) use ($prodi_id) {
                return $item->mahasiswa->prodi_id == $prodi_id;
            });
        }

    // Membuat pengisian default untuk kolom yang kosong
    $data->transform(function ($item) {
        // Handle komentar_dosen_pembimbing
        if (!$item->komentar_dosen_pembimbing) {
            $item->komentar_dosen_pembimbing = 'Tidak ada komentar';
        }

        // Handle nilai_dosen_pembimbing
        if (!$item->nilai_dosen_pembimbing) {
            $item->nilai_dosen_pembimbing = 'Belum dinilai';
        }
        
        if (!$item->komentar_instruktur_lapangan) {
            $item->komentar_instruktur_lapangan = 'Tidak ada komentar';
        }

        // Handle nilai_dosen_pembimbing
        if (!$item->nilai_instruktur_lapangan) {
            $item->nilai_instruktur_lapangan = 'Belum dinilai';
        }
        // Handle pembimbingDosen.dosen.dosen_name
        if ($item->pembimbingDosen && $item->pembimbingDosen->dosen) {
            $item->pembimbing_dosen_name = $item->pembimbingDosen->dosen->dosen_name;
        } else {
            $item->pembimbing_dosen_name = 'Dosen tidak tersedia';
        }

        // Handle instrukturLapangan.instruktur.nama_instruktur
        if ($item->instrukturLapangan && $item->instrukturLapangan->instruktur) {
            $item->instruktur_lapangan_name = $item->instrukturLapangan->instruktur->nama_instruktur;
        } else {
            $item->instruktur_lapangan_name = 'Instruktur tidak tersedia';
        }

        return $item;
    });
        
        
//         if (auth()->user()->group_id != 1) {
//     $prodi_id = auth()->user()->getProdiId();
//     $data = $data->filter(function ($item) use ($prodi_id) {
//         return $item->mahasiswa->prodi_id == $prodi_id;
//     });
// }

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }
}

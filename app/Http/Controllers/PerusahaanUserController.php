<?php

namespace App\Http\Controllers;

use App\Models\Master\KegiatanPerusahaanModel;
use App\Models\Master\MahasiswaModel;
use App\Models\Master\PendaftaranModel;
use App\Models\Master\PeriodeModel;
use App\Models\Master\PerusahaanModel;
use App\Models\Master\ProdiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;
use Yajra\DataTables\Facades\DataTables;

class PerusahaanUserController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'MAHASISWA.KEGIATAN';
        $this->menuUrl   = url('m/kegiatan');     // set URL untuk menu ini
        $this->menuTitle = 'Kegiatan';                       // set nama menu
        $this->viewPath  = 'mahasiswa.kegiatan.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Data Master', 'Perusahaan']
        ];

        $activeMenu = [
            'l1' => 'perusahaan',
            'l2' => null,
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
            ->with('allowAccess', $this->authAccessKey())
            ->with('koordinator', false);
    }

    public function list(Request $request)
    {
        $this->authAction('read', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $periode_active = PeriodeModel::where('is_current', 1)->first();

        $data = KegiatanPerusahaanModel::with('tipe_kegiatan')
            ->with('perusahaan')
            ->where('status', 1)
            ->get();

        //durasi = (akhir_kegiatan - mulai_kegiatan) / (60 * 60 * 24 * 30)
        $data = $data->map(function ($item) use ($periode_active) {
            $item->periode_kegiatan = (strtotime($item->akhir_kegiatan) - strtotime($item->mulai_kegiatan)) / (60 * 60 * 24 * 30);
            //change to 3.4 bulan example
            $item->periode_kegiatan = number_format($item->periode_kegiatan, 1) . ' bulan';
            //item = mulai_kegiatan - akhir_kegiatan (x bulan)
            $item->periode_kegiatan = $item->mulai_kegiatan . ' - ' . $item->akhir_kegiatan . ' (' . $item->periode_kegiatan . ')';

            //if is array prodi_id

            $pendaftaran = PendaftaranModel::where('periode_id', $periode_active->periode_id)
                ->where('kegiatan_perusahaan_id', $item->kegiatan_perusahaan_id)
                ->where('status', 1)
                ->count();
            $item->jumlah_pendaftar = $pendaftaran;
            $item->sisa_kuota = $item->kuota - $pendaftaran;
            $item->kuota = "$item->sisa_kuota/$item->kuota";

            $mahasiswa_id = MahasiswaModel::where('user_id', auth()->user()->user_id)->first()->mahasiswa_id;
            $item->is_daftar = PendaftaranModel::where('periode_id', $periode_active->periode_id)
                ->where('kegiatan_perusahaan_id', $item->kegiatan_perusahaan_id)
                ->where('mahasiswa_id', $mahasiswa_id)
                ->count();

            $item->is_undang = PendaftaranModel::where('periode_id', $periode_active->periode_id)
                ->where('kegiatan_perusahaan_id', $item->kegiatan_perusahaan_id)
                ->where('mahasiswa_id', $mahasiswa_id)
                ->where('tipe_pendaftar', 1)
                ->where('status', 0)
                ->count();
            $item->is_undang = ($item->is_undang > 0) ? true : false;
            $item->is_daftar = ($item->is_daftar > 0) ? true : false;


            return $item;
        });

        //filter periode_active only by periode_id in array example ["1","2","3", ...]
        $data = $data->filter(function ($item) use ($periode_active) {
            return in_array($periode_active->periode_id, json_decode($item->periode_id));
        });

        $id_user = auth()->user()->user_id;
        $mahasiswa_prodi_id = MahasiswaModel::where('user_id', $id_user)->first()->prodi_id;

        //filter prodi_id only by prodi_id in array example ["1","2","3", ...]
        $data = $data->filter(function ($item) use ($mahasiswa_prodi_id) {
            return in_array($mahasiswa_prodi_id, json_decode($item->prodi_id));
        });

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function show($id)
    {
        $this->authAction('read', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $kegiatan = KegiatanPerusahaanModel::where('kegiatan_perusahaan_id', $id)
            ->with('tipe_kegiatan')
            ->first();

        $page = [
            'title' => 'Detail ' . $this->menuTitle
        ];

        $datas = [];

        $kegiatan->durasi = (strtotime($kegiatan->akhir_kegiatan) - strtotime($kegiatan->mulai_kegiatan)) / (60 * 60 * 24 * 30);
        //change to 3.4 bulan example
        $kegiatan->durasi = number_format($kegiatan->durasi, 1) . ' bulan';

        //if is array prodi_id
        if (is_array(json_decode($kegiatan->prodi_id))) {
            $kegiatan->prodi = ProdiModel::whereIn('prodi_id', json_decode($kegiatan->prodi_id))
                ->pluck('prodi_name')
                ->implode(', ');
        } else {
            $kegiatan->prodi = '-';
        }

        //if is array periode_id
        if (is_array(json_decode($kegiatan->periode_id))) {
            $kegiatan->periode = PeriodeModel::whereIn('periode_id', json_decode($kegiatan->periode_id))
                ->get();

            $kegiatan->periode = $kegiatan->periode->map(function ($item) {
                return $item->semester . ' - ' . $item->tahun_ajar;
            })->implode(', ');
        } else {
            $kegiatan->periode = '-';
        }

        $periode_active = PeriodeModel::where('is_current', 1)->first();

        $pendaftaran = PendaftaranModel::where('periode_id', $periode_active->periode_id)
            ->where('kegiatan_perusahaan_id', $kegiatan->kegiatan_perusahaan_id)
            ->where('status', 1)
            ->count();
        $kegiatan->jumlah_pendaftar = $pendaftaran;
        $kegiatan->sisa_kuota = $kegiatan->kuota - $pendaftaran;
        $kegiatan->kuota = "$kegiatan->sisa_kuota/$kegiatan->kuota";

        $datas = [
            [
                "title" => "Kode Kegiatan",
                "value" => $kegiatan->kode_kegiatan,
                "bold" => true
            ],
            [
                "title" => "Tipe Kegiatan",
                "value" => $kegiatan->tipe_kegiatan->nama_kegiatan,
                "bold" => false
            ],
            [
                "title" => "Jenis Magang",
                "value" => $kegiatan->jenis_magang->nama_magang ?? '-',
                "bold" => false
            ],
            [
                "title" => "Prodi",
                "value" => $kegiatan->prodi,
                "bold" => false
            ],
            [
                "title" => "Periode",
                "value" => $kegiatan->periode,
                "bold" => false
            ],
            [
                "title" => "Posisi Lowongan",
                "value" => $kegiatan->posisi_lowongan,
                "bold" => true
            ],
            [
                "title" => "Deskripsi",
                "value" => $kegiatan->deskripsi,
                "bold" => false
            ],
            [
                "title" => "Kualifikasi",
                "value" => $kegiatan->kualifikasi ?? '-',
                "bold" => false
            ],
            [
                "title" => "Fasilitas/Benefit",
                "value" => $kegiatan->fasilitas ?? '-',
                "bold" => false
            ],
            [
                "title" => "Kuota",
                "value" => $kegiatan->kuota,
                "bold" => false
            ],
            [
                "title" => "Batas Pendaftaran",
                "value" => $kegiatan->batas_pendaftaran,
                "bold" => false
            ],
            [
                "title" => "Mulai Kegiatan",
                "value" => $kegiatan->mulai_kegiatan,
                "bold" => false
            ],
            [
                "title" => "Akhir Kegiatan",
                "value" => $kegiatan->akhir_kegiatan,
                "bold" => false
            ],
            [
                "title" => "Durasi",
                "value" => $kegiatan->durasi,
                "bold" => true
            ],
            [
                "title" => "Flyer",
                "value" => $kegiatan->flyer ? '<a href="' . url('assets/flyer/' . $kegiatan->flyer) . '" target="_blank">Download</a>' : '-',
                "bold" => true
            ],
            [
                "title" => "Keterangan Ditolak",
                "value" => $kegiatan->keterangan ?? '-',
                "bold" => false
            ]
        ];

        // if status != 2 then remove last index
        if ($kegiatan->status != 2) {
            array_pop($datas);
        }

        //change to stdClass loop
        $datas = array_map(function ($item) {
            $obj = new stdClass;
            $obj->title = $item['title'];
            $obj->value = $item['value'];
            $obj->bold = $item['bold'];
            $obj->color = $item['color'] ?? null;
            return $obj;
        }, $datas);

        // dd($datas);


        return (!$datas) ? $this->showModalError() :
            view($this->viewPath . 'detail')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('datas', $datas);
    }
}

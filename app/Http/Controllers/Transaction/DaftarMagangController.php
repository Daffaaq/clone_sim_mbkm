<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\KabupatenModel;
use App\Models\Master\KegiatanModel;
use App\Models\Master\MahasiswaModel;
use App\Models\Master\PeriodeModel;
use App\Models\Master\ProdiModel;
use App\Models\MitraKuotaModel;
use App\Models\MitraModel;
use App\Models\ProvinsiModel;
use App\Models\Transaction\Magang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use stdClass;
use Yajra\DataTables\Facades\DataTables;

class DaftarMagangController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'TRANSACTION.DAFTAR.MAGANG';
        $this->menuUrl   = url('transaksi/daftar-magang');     // set URL untuk menu ini
        $this->menuTitle = 'Daftar Magang';                       // set nama menu
        $this->viewPath  = 'transaction.daftar-magang.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Transaksi', 'Daftar Magang']
        ];

        $activeMenu = [
            'l1' => 'transaction',
            'l2' => 'transaksi-daftar-magang',
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

        $data  = MitraModel::with('kegiatan')
            ->with('periode')
            ->where('status', 1);
        // ->get();
        if (auth()->user()->group_id != 1) {
            //data in mitra with column mitra_prodi is [1,2,3,etc]
            //how to get with getProdiId() include with mitra_prodi
            $prodi_id = auth()->user()->getProdiId();

            // $data = $data->filter(function ($item) use ($prodi_id) {
            //     dd($prodi_id, json_decode($item->mitra_prodi));
            //     return in_array($prodi_id, json_decode($item->mitra_prodi));
            // });
            $data->whereRaw('find_in_set(?, mitra_prodi)', $prodi_id);
        }

        $data = $data->get();

        $data = $data->map(function ($item) {
            //TODO: get jumlah pendaftar
            $item['mitra_jumlah_pendaftar'] = Magang::where('mitra_id', $item->mitra_id)
                ->where('periode_id', PeriodeModel::where('is_current', 1)->first()->periode_id)
                ->where('status', 1)
                ->count();
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

        $data = MitraModel::find($id);
        $data['skema'] = explode(',', $data->mitra_skema);

        $page = [
            'title' => 'Detail ' . $this->menuTitle
        ];

        $mitra = MitraModel::where('mitra_id', $id)
            ->with('kegiatan')
            ->with('periode')
            ->first();

        $prodi_id = MahasiswaModel::where('user_id', Auth::user()->user_id)->first()->prodi_id;

        $kuota = MitraKuotaModel::where('mitra_id', $id)
            ->where('prodi_id', $prodi_id)
            ->first();

        $kuota = ($kuota) ? $kuota->kuota : 0;

        $datas = [
            [
                "title" => "Nama Kegiatan",
                "value" => $mitra->kegiatan->kegiatan_nama,
                "bold" => false
            ],
            [
                "title" => "Nama Mitra",
                "value" => $mitra->mitra_nama,
                "bold" => true
            ],
            [
                "title" => "Periode",
                "value" => $mitra->periode->periode_nama,
                "bold" => false
            ],
            [
                "title" => "Deskripsi",
                "value" => $mitra->mitra_deskripsi,
                "bold" => false
            ],
            [
                "title" => "Durasi",
                "value" => $mitra->mitra_durasi . ' bulan',
                "bold" => true
            ], [
                "title" => "Kuota",
                "value" => $kuota,
                "bold" => false
            ],
        ];

        if ($mitra->kegiatan->is_kuota == 0) {
            //remove kuota
            unset($datas[5]);
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

        $mahasiswa_id = MahasiswaModel::where('user_id', Auth::user()->user_id)->first()->mahasiswa_id;
        $mahasiswas = MahasiswaModel::where('prodi_id', $prodi_id)
            ->get();

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'detail')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data)
            ->with('datas', $datas)
            ->with('mitra', $data)
            ->with('url', $this->menuUrl . '/' . $id . '/daftar')
            ->with('mahasiswa_id', $mahasiswa_id)
            ->with('mahasiswas', $mahasiswas)
            ->with('action', 'POST');
    }

    public function daftar(Request $request, $id_mitra)
    {
        $id_periode = PeriodeModel::where('is_current', 1)->first()->periode_id;
        $tipe_pendaftar = $request->tipe_pendaftar;
        $mahasiswa = $request->mahasiswa;

        if ($tipe_pendaftar == 2) {
            $id_mahasiswa = $mahasiswa[0];
            $prodi_id = MahasiswaModel::where('mahasiswa_id', $id_mahasiswa)->first()->prodi_id;

            //cek in Magang id_mahasiswa and id_periode
            //if exist, return error
            $cek = Magang::where('mahasiswa_id', $id_mahasiswa)
                ->where('periode_id', $id_periode)
                ->where('status', '!=', 2)
                ->first();

            if ($cek) {
                return response()->json([
                    'stat' => false,
                    'mc' => false, // close modal
                    'msg' => 'Anda sudah mendaftar magang'
                ]);
            }

            //cek kegiatan model if is_kuota 1 then check kuota
            $kegiatan = MitraModel::with('kegiatan')
                ->where('mitra_id', $id_mitra)
                ->first();

            if ($kegiatan->kegiatan->is_kuota == 1) {
                $kuota = MitraKuotaModel::where('mitra_id', $id_mitra)
                    ->where('prodi_id', $prodi_id)
                    ->first();

                $kuota = ($kuota) ? $kuota->kuota : 0;

                $pendaftar = Magang::where('mitra_id', $id_mitra)
                    ->where('periode_id', $id_periode)
                    ->where('prodi_id', $prodi_id)
                    ->count();

                if ($pendaftar >= $kuota) {
                    return response()->json([
                        'stat' => false,
                        'mc' => false, // close modal
                        'msg' => 'Kuota sudah penuh'
                    ]);
                }
            }

            $count = Magang::selectRaw('magang_kode, count(*) as count')
                ->groupBy('magang_kode')
                ->get();
            $count = count($count);

            $kode = 'P-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

            $request['mahasiswa_id'] = $id_mahasiswa;
            $request['mitra_id'] = $id_mitra;
            $request['periode_id'] = $id_periode;
            $request['prodi_id'] = $prodi_id;
            $request['magang_kode'] = $kode;
            $request['magang_tipe'] = $tipe_pendaftar;
            $request['status'] = 0;

            unset($request['mahasiswa']);
            unset($request['tipe_pendaftar']);
            // dd($request->all());
            $res = Magang::insertData($request);
        } else {
            $cek = Magang::whereIn('mahasiswa_id', $mahasiswa)
                ->where('periode_id', $id_periode)
                ->where('status', '!=', 2)
                ->first();

            if ($cek) {
                return response()->json([
                    'stat' => false,
                    'mc' => false, // close modal
                    'msg' => 'Salah satu mahasiswa sudah mendaftar magang'
                ]);
            }

            $count = Magang::selectRaw('magang_kode, count(*) as count')
                ->groupBy('magang_kode')
                ->get();
            $count = count($count);

            $kode = 'P-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

            foreach ($mahasiswa as $index => $m) {
                $idx = $index;
                $id_mahasiswa = $m;
                $prodi_id = MahasiswaModel::where('mahasiswa_id', $id_mahasiswa)->first()->prodi_id;

                //cek in Magang id_mahasiswa and id_periode
                //if exist, return error

                if ($idx == 0) {
                    $request['magang_tipe'] = 0;
                } else {
                    $request['magang_tipe'] = 1;
                    $request['is_accept'] = 0;
                }
                $request['mahasiswa_id'] = $id_mahasiswa;
                $request['mitra_id'] = $id_mitra;
                $request['periode_id'] = $id_periode;
                $request['prodi_id'] = $prodi_id;
                $request['magang_kode'] = $kode;
                $request['status'] = 0;

                unset($request['mahasiswa']);
                unset($request['tipe_pendaftar']);
                // dd($request->all());
                $res = Magang::insertData($request);
            }
        }

        return response()->json([
            'stat' => $res,
            'mc' => $res, // close modal
            'msg' => $res ? 'Berhasil mendaftar' : 'Gagal mendaftar'
        ]);

        // dd($request->all(), $id_mitra, $id_periode, $tipe_pendaftar, $id_mahasiswa);
    }

    public function ajukan()
    {
        $this->authAction('create', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $page = [
            'url' => $this->menuUrl . '/ajukan',
            'title' => 'Tambah ' . $this->menuTitle
        ];


        $provinsis = ProvinsiModel::all();
        $kegiatans = KegiatanModel::where('is_mandiri', 1)->get();
        $kabupatens = [];

        return view($this->viewPath . 'ajukan')
            ->with('page', (object) $page)
            ->with('kegiatans', $kegiatans)
            ->with('provinsis', $provinsis)
            ->with('kabupatens', $kabupatens);
    }

    public function ajukan_action(Request $request)
    {
        $this->authAction('create', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
                'kegiatan_id' => 'required',
                'mitra_nama' => 'required|string',
                'mitra_website' => 'required',
                'mitra_deskripsi' => 'required',
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


            $mahasiswa = MahasiswaModel::where('user_id', Auth::user()->user_id)->first();
            $request['mitra_prodi'] = $mahasiswa->prodi_id;
            $request['periode_id'] = PeriodeModel::where('is_current', 1)->first()->periode_id;

            $kota = KabupatenModel::find($request['kota_id']);
            $request['mitra_alamat'] = $kota->nama_kab_kota;
            $request['status'] = 0;

            $res = MitraModel::insertData($request);



            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? $this->getMessage('insert.success') : $this->getMessage('insert.failed')
            ]);
        }

        return redirect('/');
    }
}

<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\DokumenMagangModel;
use App\Models\KabupatenModel;
use App\Models\Master\KegiatanModel;
use App\Models\Master\MahasiswaModel;
use App\Models\Master\PeriodeModel;
use App\Models\Master\ProdiModel;
use App\Models\Master\ProgramModel;
use App\Models\MitraKuotaModel;
use App\Models\MitraModel;
use App\Models\ProvinsiModel;
use App\Models\Transaction\Magang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
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

        $programs = ProgramModel::all();

        $id_mahasiswa = MahasiswaModel::where('user_id', Auth::user()->user_id)->first()->mahasiswa_id;

        $id_periode = PeriodeModel::where('is_current', 1)->first()->periode_id;

        $cek = Magang::where('mahasiswa_id', $id_mahasiswa)
            ->where('periode_id', $id_periode)
            //where status == 1 or 3
            ->whereIn('status', [1, 3])
            ->first();

        if ($cek) {
            if ($cek->magang_tipe == 1) {
                if ($cek->is_accept == 2) {
                    $can_daftar = true;
                } else {
                    $can_daftar = false;
                }
            } else {
                $can_daftar = ($cek) ? false : true;
            }
        } else {
            $can_daftar = ($cek) ? false : true;
        }


        return view($this->viewPath . 'index')
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $activeMenu)
            ->with('page', (object) $page)
            ->with('programs', $programs)
            ->with('can_daftar', $can_daftar)
            ->with('allowAccess', $this->authAccessKey());
    }

    public function list(Request $request)
    {
        $this->authAction('read', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $prodi_id = MahasiswaModel::where('user_id', Auth::user()->user_id)->first()->prodi_id;

        $data  = MitraModel::with('kegiatan')
            ->with('kegiatan.program')
            ->with('periode')
            ->with('kota')
            ->where('status', 1);

        $programId = $request->program;
        if ($programId) {
            $data = $data->whereHas('kegiatan.program', function ($query) use ($programId) {
                $query->where('program_id', $programId);
            });
        }

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

        $data = $data->map(function ($item) use ($prodi_id) {
            //TODO: get jumlah pendaftar
            $item['mitra_jumlah_pendaftar'] = Magang::where('mitra_id', $item->mitra_id)
                ->where('periode_id', PeriodeModel::where('is_current', 1)->first()->periode_id)
                ->whereIn('status', [1, 3])->get();
            //if magang_tipe == 1 and is_accept == 2 then remove
            $item['mitra_jumlah_pendaftar'] = $item['mitra_jumlah_pendaftar']->filter(function ($item) {
                return $item->magang_tipe != 1 || $item->is_accept != 2;
            })->count();

            $item['mitra_kuota'] = MitraKuotaModel::where('mitra_id', $item->mitra_id)
                ->where('prodi_id', $prodi_id)
                ->first();

            $item['mitra_kuota'] = ($item['mitra_kuota']) ? $item['mitra_kuota']->kuota : 0;

            $item['encrypt_mitra_id'] = Crypt::encrypt($item->mitra_id);

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

        $id = Crypt::decrypt($id);

        $data = MitraModel::find($id);
        $data['skema'] = explode(',', $data->mitra_skema);

        $dateString = $data->mitra_batas_pendaftaran;
        $currentDate = date('Y-m-d');
        $disabled = strtotime($dateString) < strtotime($currentDate);

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
                "textarea" => false
            ],
            [
                "title" => "Nama Mitra",
                "value" => $mitra->mitra_nama,
                "textarea" => false
            ],
            [
                "title" => "Periode",
                "value" => $mitra->periode->periode_nama,
                "textarea" => false
            ],
            [
                "title" => "Deskripsi",
                "value" => $mitra->mitra_deskripsi,
                "textarea" => true
            ],
            [
                "title" => "Durasi",
                "value" => $mitra->mitra_durasi . ' bulan',
                "textarea" => false
            ], [
                "title" => "Kuota",
                "value" => $kuota,
                "textarea" => false
            ], [
                "title" => "Batas Pendaftaran",
                "value" => Carbon::parse($mitra->mitra_batas_pendaftaran)->format('d M Y'),
                "textarea" => false
            ]
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
            $obj->textarea = $item['textarea'];
            $obj->color = $item['color'] ?? null;
            return $obj;
        }, $datas);

        $mahasiswa_id = MahasiswaModel::where('user_id', Auth::user()->user_id)->first()->mahasiswa_id;
        $saya = MahasiswaModel::where('mahasiswa_id', $mahasiswa_id)->first();
        $mahasiswas = MahasiswaModel::where('prodi_id', $prodi_id)
            ->whereNotIn('mahasiswa_id', function ($query) {
                $query->select('mahasiswa_id')
                    ->from('t_magang');
            })
            ->get();

        return view($this->viewPath . 'daftar')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data)
            ->with('datas', $datas)
            ->with('mitra', $data)
            ->with('url', $this->menuUrl . '/' . $id . '/daftar')
            ->with('mahasiswa_id', $mahasiswa_id)
            ->with('mahasiswas', $mahasiswas)
            ->with('saya', $saya)
            ->with('disabled', $disabled)
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $activeMenu)
            ->with('page', (object) $page)
            ->with('action', 'POST');
    }

    public function daftar(Request $request, $id_mitra)
    {
        // dd($request->all());
        // dd($file);
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

            if (!$request->magang_skema) {
                return response()->json([
                    'stat' => false,
                    'mc' => false, // close modal
                    'msg' => 'Pilih skema terlebih dahulu'
                ]);
            }

            // if ($kegiatan->kegiatan->is_submit_proposal == 1) {
            //     $file = $request->file('proposal');
            //     if (!$file) {
            //         return response()->json([
            //             'stat' => false,
            //             'mc' => false, // close modal
            //             'msg' => 'Proposal belum diisi'
            //         ]);
            //     }
            // }

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
            $res = Magang::insertData($request, ['proposal', 'files']);

            //cek if $kegiatan is_proposal 1 then
            // if ($kegiatan->kegiatan->is_submit_proposal == 1) {
            //     $file = $request->file('proposal');
            //     if ($file) {
            //         $fileName = 'proposal_' . time() . '.' . $file->getClientOriginalExtension();
            //         //move to public/assets/
            //         $file->move(public_path('assets/proposal'), $fileName);
            //         // $request['dokumen_magang_file'] = $fileName;

            //         $magang_id = Magang::where('magang_kode', $kode)->first()->magang_id;

            //         DokumenMagangModel::create([
            //             'mahasiswa_id' => $id_mahasiswa,
            //             'magang_id' => $magang_id,
            //             'dokumen_magang_nama' => 'PROPOSAL',
            //             'dokumen_magang_file' => $fileName
            //         ]);
            //     }
            // }
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

            if (!$request->magang_skema) {
                return response()->json([
                    'stat' => false,
                    'mc' => false, // close modal
                    'msg' => 'Pilih skema terlebih dahulu'
                ]);
            }

            $kegiatan = MitraModel::with('kegiatan')
                ->where('mitra_id', $id_mitra)
                ->first();

            // if ($kegiatan->kegiatan->is_submit_proposal == 1) {
            //     $file = $request->file('proposal');
            //     if (!$file) {
            //         return response()->json([
            //             'stat' => false,
            //             'mc' => false, // close modal
            //             'msg' => 'Proposal belum diisi'
            //         ]);
            //     }
            // }

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
                $res = Magang::insertData($request, ['proposal', 'files']);
            }

            //cek if $kegiatan is_proposal 1 then
            // if ($kegiatan->kegiatan->is_submit_proposal == 1) {
            //     $file = $request->file('proposal');
            //     if ($file) {
            //         $fileName = 'proposal_' . time() . '.' . $file->getClientOriginalExtension();
            //         //move to public/assets/
            //         $file->move(public_path('assets/proposal'), $fileName);
            //         // $request['dokumen_magang_file'] = $fileName;

            //         $magang_id = Magang::where('magang_kode', $kode)->first()->magang_id;

            //         DokumenMagangModel::create([
            //             'mahasiswa_id' => $id_mahasiswa,
            //             'magang_id' => $magang_id,
            //             'dokumen_magang_nama' => 'PROPOSAL',
            //             'dokumen_magang_file' => $fileName
            //         ]);
            //     }
            // }
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
                'mitra_deskripsi' => 'required',
                'mitra_alamat' => 'required'
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
            // $request['mitra_alamat'] = $kota->nama_kab_kota;
            $request['status'] = 0;

            $request['mitra_skema'] = implode(',', $request->skema_arr);

            unset($request['skema_arr']);

            $file = $request->file('flyer');
            if (!$file) {
                $request['mitra_flyer'] = NULL;
            } else {
                $fileName = 'flyer_' . time() . '.' . $file->getClientOriginalExtension();
                //move to public/assets/
                $file->move(public_path('assets/flyer'), $fileName);
                $request['mitra_flyer'] = $fileName;
            }

            // dd($request);

            $res = MitraModel::insertData($request, ['flyer']);



            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? $this->getMessage('insert.success') : $this->getMessage('insert.failed')
            ]);
        }

        return redirect('/');
    }
}

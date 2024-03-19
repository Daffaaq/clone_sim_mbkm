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
use App\Models\Setting\UserModel;
use App\Models\Transaction\Magang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use stdClass;
use Symfony\Component\VarDumper\Cloner\Data;
use Yajra\DataTables\Facades\DataTables;

class MitraController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'TRANSACTION.MITRA';
        $this->menuUrl   = url('transaksi/mitra');     // set URL untuk menu ini
        $this->menuTitle = 'Mitra';                       // set nama menu
        $this->viewPath  = 'transaction.mitra.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Transaksi', 'Mitra MBKM']
        ];

        $activeMenu = [
            'l1' => 'transaction',
            'l2' => 'transaksi-mitra',
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

        $data  = MitraModel::with('kegiatan')
            ->with('periode');
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
                ->whereIn('status', [1, 3])
                ->count();

            $item['encrypt_mitra_id'] = Crypt::encrypt($item->mitra_id);

            return $item;
        });

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


        $provinsis = ProvinsiModel::all();
        $periodes = PeriodeModel::where('is_active', 1)->get();
        $kegiatans = KegiatanModel::all();
        $kabupatens = [];
        $prodis = ProdiModel::where('is_active', 1)->get();

        return view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('periodes', $periodes)
            ->with('kegiatans', $kegiatans)
            ->with('provinsis', $provinsis)
            ->with('kabupatens', $kabupatens)
            ->with('prodis', $prodis);
    }


    public function store(Request $request)
    {
        $this->authAction('create', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
                'kegiatan_id' => 'required',
                'periode_id' => 'required',
                'mitra_nama' => 'required|string',
                'mitra_deskripsi' => 'required',
                'mitra_batas_pendaftaran' => 'required',
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

            // $random_username = 'per_' . rand(100000, 999999);
            // $user = [
            //     'username' => $random_username,
            //     'name' => $request->nama_perusahaan,
            //     'password' => Hash::make($random_username),
            //     'group_id' => 5,
            //     'is_active' => 1,
            //     'email' => $request->email,
            // ];
            // $insert = UserModel::create($user);

            // $request['user_id'] = $insert->user_id;

            if (auth()->user()->group_id == 1) {
                $request['mitra_prodi'] = implode(',', $request->prodi_arr);
                // unset($request['periode_arr']);
                unset($request['prodi_arr']);
            } else {
                $request['mitra_prodi'] = auth()->user()->getProdiId();
            }

            $request['mitra_skema'] = implode(',', $request->skema_arr);

            unset($request['skema_arr']);

            $kota = KabupatenModel::find($request['kota_id']);
            $request['mitra_alamat'] = $kota->nama_kab_kota;
            $request['status'] = 1;

            $file = $request->file('flyer');
            if (!$file) {
                $request['mitra_flyer'] = NULL;
            } else {
                $fileName = 'flyer_' . time() . '.' . $file->getClientOriginalExtension();
                //move to public/assets/
                $file->move(public_path('assets/flyer'), $fileName);
                $request['mitra_flyer'] = $fileName;
            }

            $res = MitraModel::insertData($request, ['flyer']);



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

        $data = MitraModel::find($id);
        $data['skema'] = explode(',', $data->mitra_skema);

        $provinsis = ProvinsiModel::all();
        $periodes = PeriodeModel::where('is_active', 1)->get();
        $kegiatans = KegiatanModel::all();
        $kabupatens = KabupatenModel::where('d_provinsi_id', $data->provinsi_id)->get();
        $prodis = ProdiModel::where('is_active', 1)->get();

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'action')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('periodes', $periodes)
            ->with('kegiatans', $kegiatans)
            ->with('provinsis', $provinsis)
            ->with('kabupatens', $kabupatens)
            ->with('data', $data)
            ->with('prodis', $prodis);
    }


    public function update(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
                'kegiatan_id' => 'required',
                'periode_id' => 'required',
                'mitra_nama' => 'required|string',
                'mitra_deskripsi' => 'required',
                'mitra_batas_pendaftaran' => 'required'
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

            $kota = KabupatenModel::find($request['kota_id']);
            $request['mitra_alamat'] = $kota->nama_kab_kota;

            if (auth()->user()->group_id == 1) {
                $request['mitra_prodi'] = implode(',', $request->prodi_arr);
                // unset($request['periode_arr']);
                unset($request['prodi_arr']);
            }

            $request['mitra_skema'] = implode(',', $request->skema_arr);

            unset($request['skema_arr']);

            $file = $request->file('flyer');
            if (!$file) {
                $request['mitra_flyer'] = MitraModel::where('mitra_id', $id)->first()->mitra_flyer;
            } else {
                $fileName = 'flyer_' . time() . '.' . $file->getClientOriginalExtension();
                //move to public/assets/
                $file->move(public_path('assets/flyer'), $fileName);
                $request['mitra_flyer'] = $fileName;
            }

            $res = MitraModel::updateData($id, $request, ['flyer']);

            // $id_perusahaan = MitraModel::where('perusahaan_id', $id)->first();

            // $res_user = UserModel::where('user_id', $id_perusahaan->user_id)->update([
            //     'name' => $request->nama_perusahaan,
            //     'email' => $request->email,
            // ]);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? $this->getMessage('update.success') : $this->getMessage('update.failed')
            ]);
        }

        return redirect('/');
    }

    public function show($id)
    {
        $this->authAction('read', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $id = Crypt::decrypt($id);

        $data = MitraModel::find($id);
        // $data['skema'] = explode(',', $data->mitra_skema);
        // $data->mitra_skema = $data['skema'];

        $dateString = $data->mitra_batas_pendaftaran;
        $currentDate = date('Y-m-d');
        $disabled = strtotime($dateString) < strtotime($currentDate);

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Transaksi', 'Mitra MBKM', 'Detail ' . $this->menuTitle]
        ];

        $activeMenu = [
            'l1' => 'transaction',
            'l2' => 'transaksi-mitra',
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl,
            'title' => 'Detail ' . $this->menuTitle
        ];

        $mitra = MitraModel::where('mitra_id', $id)
            ->with('kegiatan')
            ->with('periode')
            ->first();

        if (auth()->user()->group_id != 1) {
            //data in mitra with column mitra_prodi is [1,2,3,etc]
            //how to get with getProdiId() include with mitra_prodi
            $prodi_id = auth()->user()->getProdiId();
            // $data = $data->filter(function ($item) use ($prodi_id) {
            //     return in_array($prodi_id, json_decode($item->mitra_prodi));
            // });
            $prodis = ProdiModel::where('prodi_id', $prodi_id)->get();
        } else {
            $prodi_arr = explode(',', $mitra->mitra_prodi);
            //get prodi by $prodi_arr
            $prodis = ProdiModel::whereIn('prodi_id', $prodi_arr)->get();
        }
        $prodis = $prodis->map(function ($item) use ($id) {
            $item['kuota'] = MitraKuotaModel::where('mitra_id', $id)
                ->where('prodi_id', $item->prodi_id)
                ->first();
            return $item;
        });

        return view($this->viewPath . 'detail')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data)
            ->with('mitra', $data)
            ->with('url', $this->menuUrl . '/' . $id . '/kuota')
            ->with('disabled', $disabled)
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $activeMenu)
            ->with('page', (object) $page)
            ->with('prodis', $prodis)
            ->with('action', 'PUT');
    }

    public function show2($id)
    {
        $this->authAction('read', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $id = Crypt::decrypt($id);

        $data = MitraModel::find($id);
        $page = [
            'title' => 'Detail ' . $this->menuTitle
        ];

        $mitra = MitraModel::where('mitra_id', $id)
            ->with('kegiatan')
            ->with('periode')
            ->first();

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
            ],
            [
                "title" => "Status",
                "value" => $mitra->status == 0 ? 'Menunggu' : ($mitra->status == 1 ? 'Diterima' : 'Ditolak'),
                "bold" => false,
                "color" => $mitra->status == 0 ? 'info' : ($mitra->status == 1 ? 'success' : 'danger')
            ],
            [
                "title" => "Keterangan Ditolak",
                "value" => $mitra->mitra_keterangan_ditolak ?? '-',
                "bold" => false
            ]
        ];


        // if status != 2 then remove last index
        if ($mitra->status != 2) {
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

        if (auth()->user()->group_id != 1) {
            //data in mitra with column mitra_prodi is [1,2,3,etc]
            //how to get with getProdiId() include with mitra_prodi
            $prodi_id = auth()->user()->getProdiId();
            // $data = $data->filter(function ($item) use ($prodi_id) {
            //     return in_array($prodi_id, json_decode($item->mitra_prodi));
            // });
            $prodis = ProdiModel::where('prodi_id', $prodi_id)->get();
        } else {
            $prodi_arr = explode(',', $mitra->mitra_prodi);
            //get prodi by $prodi_arr
            $prodis = ProdiModel::whereIn('prodi_id', $prodi_arr)->get();
        }
        $prodis = $prodis->map(function ($item) use ($id) {
            $item['kuota'] = MitraKuotaModel::where('mitra_id', $id)
                ->where('prodi_id', $item->prodi_id)
                ->first();
            return $item;
        });

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'detail')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data)
            ->with('datas', $datas)
            ->with('url', $this->menuUrl . '/' . $id . '/kuota')
            ->with('action', 'PUT')
            ->with('prodis', $prodis)
            ->with('mitra', $mitra);
    }


    public function confirm($id)
    {
        $this->authAction('delete', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = MitraModel::find($id);

        return (!$data) ? $this->showModalError() :
            $this->showModalConfirm($this->menuUrl . '/' . $id, [
                'Nama' => $data->mitra_nama,
            ]);
    }

    public function destroy(Request $request, $id)
    {
        $this->authAction('delete', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $res = MitraModel::deleteData($id);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => MitraModel::getDeleteMessage()
            ]);
        }

        return redirect('/');
    }

    public function update_status(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $request['status'] = $request->status; // [0: pending, 1: approved, 2: rejected]
            $res = MitraModel::updateData($id, $request);

            return response()->json([
                'stat' => $request->status == 1 ? TRUE : FALSE,
                'mc' => $res, // close modal
                'msg' => ($res) ? 'Perusahaan berhasil ' . ($request->status == 1 ? 'diapprove.' : 'direject.') : 'Perusahaan gagal ' . ($request->status == 1 ? 'diapprove.' : 'direject.')
            ]);
        }

        return redirect('/');
    }

    public function confirm_approve($id)
    {
        $this->authAction('update', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = MitraModel::find($id);

        return (!$data) ? $this->showModalError() :
            $this->showModalConfirm($this->menuUrl . '/' . $id . '/approve', [
                'Nama' => "$data->mitra_nama",
            ], 'Konfirmasi Approve Perusahaan', 'Apakah anda yakin ingin approve perusahaan berikut:', 'Ya, Approve', 'PUT');
    }

    public function confirm_reject($id)
    {
        $this->authAction('update', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = MitraModel::find($id);

        return (!$data) ? $this->showModalError() :
            $this->showModalReject($this->menuUrl . '/' . $id . '/reject', [
                'Nama' => "$data->mitra_nama",
            ], 'Konfirmasi Reject Perusahaan', 'Apakah anda yakin ingin reject perusahaan berikut:', 'Ya, Reject', 'PUT');
    }

    public function approve(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $request['status'] = 1; // [0: pending, 1: approved, 2: rejected]
            $res = MitraModel::updateData($id, $request);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? 'Perusahaan berhasil diapprove.' : 'Perusahaan gagal diapprove.'
            ]);
        }

        return redirect('/');
    }

    public function reject(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $request['status'] = 2; // [0: pending, 1: approved, 2: rejected]
            $request['mitra_keterangan_ditolak'] = $request->reason;
            unset($request['reason']);
            $res = MitraModel::updateData($id, $request);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? 'Perusahaan berhasil direject.' : 'Perusahaan gagal direject.'
            ]);
        }

        return redirect('/');
    }

    public function set_kuota(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $prodi_id = $request->prodi_id;
            $cek = MitraKuotaModel::where('mitra_id', $id)
                ->where('prodi_id', $prodi_id)
                ->first();

            $request['mitra_id'] = $id;
            $request['prodi_id'] = $prodi_id;
            // dd($request);
            if ($cek) {
                unset($request['mitra_id']);
                unset($request['prodi_id']);
                $res = MitraKuotaModel::where('mitra_id', $id)
                    ->where('prodi_id', $prodi_id)
                    ->update([
                        'kuota' => $request->kuota
                    ]);
            } else {
                $res = MitraKuotaModel::insertData($request);
            }


            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? 'Berhasil set kuota.' : 'Gagal set kuota.'
            ]);
        }

        return redirect('/');
    }

    public function alasan($id)
    {
        $this->authAction('read', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = MitraModel::find($id);
        $page = [
            'title' => 'Detail ' . $this->menuTitle
        ];

        $mitra = MitraModel::where('mitra_id', $id)
            ->with('kegiatan')
            ->with('periode')
            ->first();

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
                "title" => "Status",
                "value" => $mitra->status == 0 ? 'Menunggu' : ($mitra->status == 1 ? 'Diterima' : 'Ditolak'),
                "bold" => false,
                "color" => $mitra->status == 0 ? 'info' : ($mitra->status == 1 ? 'success' : 'danger')
            ],
            [
                "title" => "Keterangan Ditolak",
                "value" => $mitra->mitra_keterangan_ditolak ?? '-',
                "bold" => false
            ]
        ];



        //change to stdClass loop
        $datas = array_map(function ($item) {
            $obj = new stdClass;
            $obj->title = $item['title'];
            $obj->value = $item['value'];
            $obj->bold = $item['bold'];
            $obj->color = $item['color'] ?? null;
            return $obj;
        }, $datas);

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'alasan')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data)
            ->with('datas', $datas)
            ->with('url', $this->menuUrl . '/' . $id . '/kuota')
            ->with('action', 'PUT')
            ->with('mitra', $mitra);
    }
}

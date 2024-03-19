<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\DokumenMagangModel;
use App\Models\Master\MahasiswaModel;
use App\Models\MitraModel;
use App\Models\SuratPengantarModel;
use App\Models\Transaction\Magang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use stdClass;
use Yajra\DataTables\Facades\DataTables;

class LihatStatusPendaftaranController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'TRANSACTION.LIHAT.STATUS.PENDAFTARAN';
        $this->menuUrl   = url('transaksi/lihat-status-pendaftaran');     // set URL untuk menu ini
        $this->menuTitle = 'Lihat Status Pendaftaran';                       // set nama menu
        $this->viewPath  = 'transaction.lihat-status-pendaftaran.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Transaksi', 'Lihat Status Pendafatran']
        ];

        $activeMenu = [
            'l1' => 'transaction',
            'l2' => 'transaksi-lihat-status-pendaftaran',
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


        $data  = Magang::with('mahasiswa')
            ->with('mitra')
            ->with('periode')
            ->with('prodi')
            ->with('mitra.kegiatan');

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

        $data = $data->map(function ($item) {
            $item['encrypt_magang_id'] = Crypt::encrypt($item->magang_id);
            $item['proposal'] = DokumenMagangModel::where('magang_id', $item->magang_id)->where('dokumen_magang_nama', 'PROPOSAL')->first();
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

        $mitra = MitraModel::where('mitra_id', $data->mitra_id)
            ->with('kegiatan')
            ->with('periode')
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

        $data = Magang::find($id);

        $kode_magang = $data->magang_kode;

        //find Magang with same kode_magang then show mahaasiswa

        $anggotas = Magang::where('magang_kode', $kode_magang)
            ->with('mahasiswa')
            ->get();

        $dateString = $data->mitra_batas_pendaftaran;
        $currentDate = date('Y-m-d');
        $disabled = strtotime($dateString) < strtotime($currentDate);

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Transaksi', 'Lihat Status Pendaftaran']
        ];

        $activeMenu = [
            'l1' => 'transaction',
            'l2' => 'transaksi-lihat-status-pendaftaran',
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
            ->first();
        $mag = Magang::where('magang_kode', $data->magang_kode)->where('magang_tipe', 1)->where('is_accept', 0)->count();
        // dd($aksi_proposal);
        //jika lebih dari 0 maka return false
        if ($mag > 0) {
            $magang->can_upload_proposal = FALSE;
        } else {
            $magang->can_upload_proposal = TRUE;
        }
        //check if me is magang_tipe is not 0 and 2 then visible to upload proposal
        $me = Magang::where('magang_kode', $data->magang_kode)->where('mahasiswa_id', $id_mahasiswa)->first();
        if ($me->magang_tipe == 0 || $me->magang_tipe == 2) {
            $magang->ketua = TRUE;
        } else {
            $magang->ketua = FALSE;
        }
        $check = Magang::where('magang_kode', $kode_magang)->get();
        $id_joined = $check->pluck('magang_id');
        $proposal = DokumenMagangModel::whereIn('magang_id', $id_joined)->where('dokumen_magang_nama', 'PROPOSAL')->first();
        $surat_pengantar = SuratPengantarModel::where('magang_kode', $kode_magang)->first();
        $surat_balasan = DokumenMagangModel::whereIn('magang_id', $id_joined)->where('dokumen_magang_nama', 'SURAT_BALASAN')->first();
        if ($proposal) {
            $magang->proposal_exist = TRUE;
            $magang->proposal = $proposal;
            if ($surat_pengantar) {
                $magang->surat_pengantar_exist = TRUE;
                $magang->surat_pengantar = $surat_pengantar;
            } else {
                $magang->surat_pengantar_exist = FALSE;
                $magang->surat_pengantar = NULL;
            }
        } else {
            $magang->proposal_exist = FALSE;
            $magang->proposal = NULL;
            $magang->surat_pengantar_exist = FALSE;
            $magang->surat_pengantar = NULL;
        }
        $magang->surat_balasan = $surat_balasan;
        $magang->surat_balasan_exist = $surat_balasan ? TRUE : FALSE;


        $bulans = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        return view($this->viewPath . 'update')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data)
            ->with('magang', $magang)
            ->with('url', $this->menuUrl . '/' . $id . '/suratbalasan')
            ->with('disabled', $disabled)
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $activeMenu)
            ->with('anggotas', $anggotas)
            ->with('bulans', $bulans)
            ->with('page', (object) $page)
            ->with('action', 'POST');
    }

    public function suratbalasan(Request $request, $id_magang)
    {

        $file = $request->file('surat_balasan');
        if ($file) {
            $fileName = 'suratbalasan' . time() . '.' . $file->getClientOriginalExtension();
            //move to public/assets/
            $file->move(public_path('assets/suratbalasan'), $fileName);
            // $request['dokumen_magang_file'] = $fileName;

            $id_mahasiswa = $request->mahasiswa_id;

            $res = DokumenMagangModel::create([
                'mahasiswa_id' => $id_mahasiswa,
                'magang_id' => $id_magang,
                'dokumen_magang_tipe' => $request->dokumen_magang_tipe,
                'dokumen_magang_nama' => 'SURAT_BALASAN',
                'dokumen_magang_file' => $fileName
            ]);
            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => $res ? 'Berhasil upload surat balasan' : 'Gagal upload surat balasan'
            ]);
        } else {
            return response()->json([
                'stat' => false,
                'mc' => false,
                'msg' => 'Pilih file terlebih dahulu'
            ]);
        }



        // dd($request->all(), $id_mitra, $id_periode, $tipe_pendaftar, $id_mahasiswa);
    }
}

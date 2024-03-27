<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\DokumenMagangModel;
use App\Models\Master\PeriodeModel;
use App\Models\MitraModel;
use App\Models\SuratPengantarModel;
use App\Models\Transaction\Magang;
use Illuminate\Http\Request;
use stdClass;
use Yajra\DataTables\Facades\DataTables;

class PendaftaranController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'TRANSACTION.PENDAFTARAN';
        $this->menuUrl   = url('transaksi/pendaftaran');     // set URL untuk menu ini
        $this->menuTitle = 'Pendaftaran MBKM';                       // set nama menu
        $this->viewPath  = 'transaction.pendaftaran.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Transaksi', 'Pendaftaran MBKM']
        ];

        $activeMenu = [
            'l1' => 'transaction',
            'l2' => 'transaksi-pendaftaran',
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

        $active_periode = PeriodeModel::where('is_current', 1)->first();

        $data  = Magang::with('mahasiswa')
            ->with('mitra')
            ->with('periode')
            ->with('prodi')
            ->with('mitra.kegiatan')
            ->where('periode_id', $active_periode->periode_id)
            ->where('magang_tipe', '!=', 1);

        if (auth()->user()->group_id == 1) {
            $data = $data->get();
        } else if (auth()->user()->group_id == 4) {
            $data = $data->where('mahasiswa_id', auth()->user()->getUserMahasiswa->mahasiswa_id)->get();
        } else {
            $prodi_id = auth()->user()->getProdiId();
            $data = $data->where('prodi_id', $prodi_id)->get();
        }

        //cek proposal ex ist if$data->mitra->kegiatan->is submit proposal == 0 then false
        $data = $data->map(function ($item) {
            $item->proposal = DokumenMagangModel::where('magang_id', $item->magang_id)->where('dokumen_magang_nama', 'PROPOSAL')->latest()->first();
            $item->surat_balasan = DokumenMagangModel::where('magang_id', $item->magang_id)->where('dokumen_magang_nama', 'SURAT_BALASAN')->first();
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
            'title' => 'Dokumen ' . $this->menuTitle
        ];

        $data = Magang::find($id);

        $mitra = MitraModel::where('mitra_id', $data->mitra_id)
            ->with('kegiatan')
            ->with('periode')
            ->first();

        if ($data->magang_tipe == 2) {
            $id = $id;
            $dokumen = DokumenMagangModel::where('magang_id', $id);
            $proposal = DokumenMagangModel::where('magang_id', $id);
            $surat_balasan = DokumenMagangModel::where('magang_id', $id);
            $aksi_proposal = true;
        } else {
            $magang = Magang::where('magang_kode', $data->magang_kode)->get();
            $id = $magang->pluck('magang_id');
            $dokumen = DokumenMagangModel::whereIn('magang_id', $id);
            $proposal = DokumenMagangModel::whereIn('magang_id', $id);
            $surat_balasan = DokumenMagangModel::whereIn('magang_id', $id);
            //search if any is_accept = 0 then throw false
            $mag = Magang::where('magang_kode', $data->magang_kode)->where('magang_tipe', 1)->where('is_accept', 0)->count();
            // dd($aksi_proposal);
            //jika lebih dari 0 maka return false
            if ($mag > 0) {
                $aksi_proposal = FALSE;
            } else {
                $aksi_proposal = TRUE;
            }
        }

        // dd($dokumen->get());

        $datas = [
            [
                "title" => "Proposal",
                "value" => $proposal->where('dokumen_magang_nama', 'PROPOSAL')->first() ? $proposal->where('dokumen_magang_nama', 'PROPOSAL')->first()->dokumen_magang_file : "Belum Ada File",
                "bold" => false,
                'aksi' => $aksi_proposal,
                'dokumen' => $proposal->where('dokumen_magang_nama', 'PROPOSAL')->first(),
                'type' => 'p'
            ],
            [
                "title" => "Surat Balasan",
                "value" => $surat_balasan->where('dokumen_magang_nama', 'SURAT_BALASAN')->first() ? $surat_balasan->where('dokumen_magang_nama', 'SURAT_BALASAN')->first()->dokumen_magang_file : "Belum Ada File",
                "bold" => false,
                'aksi' => true,
                'dokumen' => $surat_balasan->where('dokumen_magang_nama', 'SURAT_BALASAN')->first(),
                'type' => 'sb'
            ],
        ];

        if ($mitra->kegiatan->is_submit_proposal == 0) {
            //remove kuota
            unset($datas[0]);
        }

        //change to stdClass loop
        $datas = array_map(function ($item) {
            $obj = new stdClass;
            $obj->title = $item['title'];
            $obj->value = $item['value'];
            $obj->bold = $item['bold'];
            $obj->aksi = $item['aksi'];
            $obj->dokumen = $item['dokumen'];
            $obj->type = $item['type'];
            $obj->color = $item['color'] ?? null;
            return $obj;
        }, $datas);

        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'detail')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data)
            ->with('datas', $datas)
            ->with('surat_balasan', $surat_balasan->where('dokumen_magang_nama', 'SURAT_BALASAN')->first())
            ->with('mitra', $data);
    }

    public function anggota($id)
    {
        $this->authAction('read', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $page = [
            'title' => 'Anggota ' . $this->menuTitle
        ];

        $data = Magang::find($id);


        if ($data->magang_tipe == 2) {
            $anggota = NULL;
            $ketua = NULL;
        } else {
            $magang = Magang::where('magang_kode', $data->magang_kode)->get();
            $id = $magang->pluck('magang_id');
            $ketua = ($data->magang_tipe == 0) ? Magang::whereIn('magang_id', $id)->with('mahasiswa')->where('magang_tipe', '=', 0)->get() : NULL;
            $anggota = ($data->magang_tipe == 0) ? Magang::whereIn('magang_id', $id)->with('mahasiswa')->where('magang_tipe', '=', 1)->get() : NULL;
        }


        return (!$data) ? $this->showModalError() :
            view($this->viewPath . 'anggota')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data)
            ->with('ketua', $ketua)
            ->with('anggota', $anggota)
            ->with('mitra', $data);
    }

    public function confirm_approve($id)
    {
        $this->authAction('update', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = Magang::with('mahasiswa')
            ->with('mitra')
            ->with('periode')
            ->with('prodi')
            ->with('mitra.kegiatan')
            ->where('magang_id', $id)
            ->first();

        return (!$data) ? $this->showModalError() :
            $this->showModalConfirm($this->menuUrl . '/' . $id . '/approve', [
                'NIM' => $data->mahasiswa->nim,
                'Nama' => $data->mahasiswa->nama_mahasiswa,
                'Mitra' => $data->mitra->mitra_nama,
                'Kegiatan' => $data->mitra->kegiatan->kegiatan_nama,
            ], 'Konfirmasi Terima', 'Apakah anda yakin ingin menerima mahasiswa berikut:', 'Ya, Approve', 'PUT');
    }

    public function confirm_reject($id)
    {
        $this->authAction('update', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = Magang::with('mahasiswa')
            ->with('mitra')
            ->with('periode')
            ->with('prodi')
            ->with('mitra.kegiatan')
            ->where('magang_id', $id)
            ->first();

        return (!$data) ? $this->showModalError() :
            $this->showModalConfirm($this->menuUrl . '/' . $id . '/reject', [
                'NIM' => $data->mahasiswa->nim,
                'Nama' => $data->mahasiswa->nama_mahasiswa,
                'Mitra' => $data->mitra->mitra_nama,
                'Kegiatan' => $data->mitra->kegiatan->kegiatan_nama,
            ], 'Konfirmasi Tolak', 'Apakah anda yakin ingin menolak mahasiswa berikut:', 'Ya, Reject', 'PUT');
    }

    public function confirm($id)
    {
        $this->authAction('update', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = Magang::with('mahasiswa')
            ->with('mitra')
            ->with('periode')
            ->with('prodi')
            ->with('mitra.kegiatan')
            ->where('magang_id', $id)
            ->first();

        return (!$data) ? $this->showModalError() :
            $this->showModalConfirmCustom('master.pendaftaran.confirm', $this->menuUrl . '/' . $id . '/confirm', [
                'NIM' => $data->mahasiswa->nim,
                'Nama' => $data->mahasiswa->nama_mahasiswa,
                'Mitra' => $data->mitra->mitra_nama,
                'Kegiatan' => $data->mitra->kegiatan->kegiatan_nama,
            ], 'Konfirmasi Pendaftaran', 'Data pendaftar:', 'Ya, Approve', 'PUT');
    }

    public function confirm_delete($id)
    {
        $this->authAction('delete', 'modal');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = Magang::with('mahasiswa')
            ->with('mitra')
            ->with('periode')
            ->with('prodi')
            ->with('mitra.kegiatan')
            ->where('magang_id', $id)
            ->first();

        $kode_magang = $data->magang_kode;

        //find Magang with same kode_magang then show mahaasiswa

        $data->anggotas = Magang::where('magang_kode', $kode_magang)
            ->with('mahasiswa')
            ->get();

        return (!$data) ? $this->showModalError() :
            $this->showModalConfirmCustom(
                'transaction.pendaftaran.confirm',
                $this->menuUrl . '/' . $id,
                [
                    'Magang ID' => $data->magang_kode,
                    'Skema' => $data->magang_skema,
                    'Mitra' => $data->mitra->mitra_nama,
                    'Kegiatan' => $data->mitra->kegiatan->kegiatan_nama,
                ],
                'Konfirmasi Hapus Data',
                'Apakah anda yakin ingin menghapus data berikut:',
                'Ya, Hapus',
                'DELETE',
                $data
            );
    }

    public function destroy(Request $request, $id)
    {
        $this->authAction('delete', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {
            $magang_kode = Magang::find($id)->magang_kode;

            $res = [];
            foreach (Magang::where('magang_kode', $magang_kode)->get() as $m) {
                $res[] = Magang::deleteData($m->magang_id);
            }

            return response()->json([
                'stat' => $res[0],
                'mc' => $res[0], // close modal
                'msg' => Magang::getDeleteMessage()
            ]);
        }

        return redirect('/');
    }

    public function confirm_action(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $act = $request['act'];

            $request['status'] = $act == "1" ? 1 : 2; // [0: pending, 1: approved, 2: rejected]
            unset($request['act']);
            $res = Magang::updateData($id, $request);
            $stat = $act == "1" ? "diapprove" : "ditolak";


            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? "Magang berhasil $stat" : "Magang gagal $stat."
            ]);
        }

        return redirect('/');
    }

    public function approve(Request $request, $id)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $request['status'] = 1; // [0: pending, 1: approved, 2: rejected]
            $res = Magang::updateData($id, $request);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? 'Magang berhasil diapprove.' : 'Magang gagal diapprove.'
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
            // $request['mitra_keterangan_ditolak'] = $request->reason;
            // unset($request['reason']);
            $res = Magang::updateData($id, $request);

            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => ($res) ? 'Magang berhasil direject.' : 'Magang gagal direject.'
            ]);
        }

        return redirect('/');
    }

    public function validasi_proposal($id)
    {

        $page = [
            'url' => $this->menuUrl,
            'title' => 'Validasi Proposal ' . $this->menuTitle
        ];

        $data = Magang::find($id);

        $kode_magang = $data->magang_kode;

        //find Magang with same kode_magang then show mahaasiswa

        $anggotas = Magang::where('magang_kode', $kode_magang)
            ->with('mahasiswa')
            ->get();

        $magang = Magang::where('magang_id', $id)
            ->with('mitra')
            ->with('mitra.kegiatan')
            ->with('periode')
            ->first();
        $check = Magang::where('magang_kode', $kode_magang)->get();
        $id_joined = $check->pluck('magang_id');
        $proposal = DokumenMagangModel::whereIn('magang_id', $id_joined)->where('dokumen_magang_nama', 'PROPOSAL')->latest()->first();
        $magang->proposal = $proposal;
        $magang->proposals = DokumenMagangModel::whereIn('magang_id', $id_joined)->where('dokumen_magang_nama', 'PROPOSAL')->get();

        return view($this->viewPath . 'validasi_proposal')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data)
            ->with('url', $this->menuUrl . '/' . $id . '/suratbalasan')
            ->with('anggotas', $anggotas)
            ->with('magang', $magang)
            ->with('page', (object) $page)
            ->with('action', 'POST');
    }

    public function validasi_surat_balasan($id)
    {

        $page = [
            'url' => $this->menuUrl,
            'title' => 'Validasi Surat Balasan ' . $this->menuTitle
        ];

        $data = Magang::find($id);

        $kode_magang = $data->magang_kode;

        //find Magang with same kode_magang then show mahaasiswa

        $anggotas = Magang::where('magang_kode', $kode_magang)
            ->with('mahasiswa')
            ->get();

        $magang = Magang::where('magang_id', $id)
            ->with('mitra')
            ->with('mitra.kegiatan')
            ->with('periode')
            ->first();
        $check = Magang::where('magang_kode', $kode_magang)->get();
        $id_joined = $check->pluck('magang_id');
        $surat_balasan = DokumenMagangModel::whereIn('magang_id', $id_joined)->where('dokumen_magang_nama', 'SURAT_BALASAN')->first();
        $magang->surat_balasan = $surat_balasan;
        $proposal = DokumenMagangModel::whereIn('magang_id', $id_joined)->where('dokumen_magang_nama', 'PROPOSAL')->first();
        $magang->proposal = $proposal;
        $magang->proposals = DokumenMagangModel::whereIn('magang_id', $id_joined)->where('dokumen_magang_nama', 'PROPOSAL')->get();

        return view($this->viewPath . 'validasi_surat_balasan')
            ->with('page', (object) $page)
            ->with('id', $id)
            ->with('data', $data)
            ->with('url', $this->menuUrl . '/' . $id . '/suratbalasan')
            ->with('anggotas', $anggotas)
            ->with('magang', $magang)
            ->with('page', (object) $page)
            ->with('action', 'POST');
    }

    public function confirm_proposal(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {

            $id = $request->id;
            $status = $request->status;
            $status_text = $status == 3 ? "diterima" : "ditolak";

            $request['status'] = $status; // [0: pending, 1: approved, 2: rejected, 3:terdaftar]

            $kode_magang = DokumenMagangModel::with('magang')->where('dokumen_magang_id', $id)->first()->magang->magang_kode;
            // dd($kode_magang);
            //update status Magang where magang_kode = $kode_magang
            $res = Magang::where('magang_kode', $kode_magang)->update(['status' => $status == 3 ? 3 : 0]);
            $res = DokumenMagangModel::where('dokumen_magang_id', $id)->where('dokumen_magang_nama', 'PROPOSAL')->update(['dokumen_magang_status' => $status == 3 ? 1 : 0, 'dokumen_magang_keterangan' => $request->keterangan]);

            $request['surat_pengantar_no'] = '/PL.2.1/PM/' . date('Y');
            $request['magang_kode'] = $kode_magang;

            if ($status == 3) {
                SuratPengantarModel::insertData($request, ['status', 'id', 'keterangan']);
            }

            return response()->json([
                'stat' => $status == 3 ? 1 : 0,
                'mc' => $res, // close modal
                'msg' => ($res) ? "Proposal berhasil $status_text." : "Proposal gagal $status_text."
            ]);
        }

        return redirect('/');
    }

    public function confirm_sb(Request $request)
    {
        // dd($request->all());
        if ($request->ajax() || $request->wantsJson()) {

            $id = $request->id;
            $status = $request->status;
            $status_text = $status == 1 ? "diterima" : "ditolak";

            $request['status'] = $status; // [0: pending, 1: approved, 2: rejected, 3:terdaftar]

            $kode_magang = DokumenMagangModel::with('magang')->where('dokumen_magang_id', $id)->first()->magang->magang_kode;
            // dd($kode_magang);
            //update status Magang where magang_kode = $kode_magang
            $res = Magang::where('magang_kode', $kode_magang)->update(['status' => $status == 1 ? 1 : 2]);
            $res = DokumenMagangModel::where('dokumen_magang_id', $id)->where('dokumen_magang_nama', 'SURAT_BALASAN')->update(['dokumen_magang_status' => $status == 1 ? 1 : 0, 'dokumen_magang_keterangan' => $request->keterangan]);

            return response()->json([
                'stat' => $status == 1 ? 1 : 0,
                'mc' => $res, // close modal
                'msg' => ($res) ? "Surat balasan berhasil $status_text." : "Surat balasan gagal $status_text."
            ]);
        }

        return redirect('/');
    }
}

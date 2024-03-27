<?php

namespace App\Http\Controllers;

use App\Models\SuratPengantarModel;
use App\Models\Transaction\Magang;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratPengantarController extends Controller
{
    public function index($kode)
    {
        $sp = SuratPengantarModel::where('magang_kode', $kode)->first();
        $anggotas = Magang::with('mahasiswa')->with('periode')->where('magang_kode', $sp->magang_kode)->get();
        $anggotas = $anggotas->filter(function ($item) {
            return $item->magang_tipe != 1 || $item->is_accept != 2;
        });
        //rearrange $key from 0
        $anggotas = array_values($anggotas->toArray());
        //change to stdclass
        $anggotas = json_decode(json_encode($anggotas), FALSE);
        $mitra = Magang::with('mitra')->with('prodi')->with('mitra.kegiatan.program')
            ->with('mitra.provinsi')
            ->with('mitra.kota')
            ->where('magang_kode', $sp->magang_kode)->first();

        // return view('template_surat.surat_pengantar', compact('sp', 'anggotas', 'mitra'));
        $pdf = Pdf::loadView('template_surat.surat_pengantar', compact('sp', 'anggotas', 'mitra'));
        return $pdf->stream();
    }

    public function generate(Request $request)
    {
        $bulans = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        // dd($request->all());
        $request['surat_pengantar_no'] = '/PL.2.1/PM/' . date('Y');
        // $request['surat_pengantar_awal_pelaksanaan'] = $request->surat_pengantar_awal_pelaksanaan;
        // $request['surat_pengantar_akhir_pelaksanaan'] = $request->surat_pengantar_akhir_pelaksanaan;

        // dd($request->all());

        $res = SuratPengantarModel::insertData($request);

        return response()->json([
            'stat' => $res,
            'mc' => $res, // close modal
            'msg' => ($res) ? "Surat Pengantar berhasil dibuat" : "Surat Pengantar gagal dibuat"
        ]);
    }
}

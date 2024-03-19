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
        $mitra = Magang::with('mitra')->with('prodi')->where('magang_kode', $sp->magang_kode)->first();

        // return view('template_surat.surat_pengantar', compact('sp', 'anggotas', 'mitra'));
        $pdf = Pdf::loadView('template_surat.surat_pengantar', compact('sp', 'anggotas', 'mitra'));
        return $pdf->stream();
    }

    public function generate(Request $request)
    {
        $bulans = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        // dd($request->all());
        $request['surat_pengantar_no'] = '/PL.2.1/PM/' . date('Y');
        $request['surat_pengantar_awal_pelaksanaan'] = $bulans[$request->surat_pengantar_awal_pelaksanaan - 1];
        $request['surat_pengantar_akhir_pelaksanaan'] = $bulans[$request->surat_pengantar_akhir_pelaksanaan - 1];

        // dd($request->all());

        $res = SuratPengantarModel::insertData($request);

        return response()->json([
            'stat' => $res,
            'mc' => $res, // close modal
            'msg' => ($res) ? "Surat Pengantar berhasil dibuat" : "Surat Pengantar gagal dibuat"
        ]);
    }
}

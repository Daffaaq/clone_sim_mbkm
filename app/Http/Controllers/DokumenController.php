<?php

namespace App\Http\Controllers;

use App\Models\DokumenMagangModel;
use App\Models\Master\MahasiswaModel;
use Illuminate\Http\Request;

class DokumenController extends Controller
{
    public function upload_proposal(Request $request)
    {
        $file = $request->file('proposal');
        if ($file) {
            $fileName = 'proposal_' . time() . '.' . $file->getClientOriginalExtension();
            //move to public/assets/
            $file->move(public_path('assets/proposal'), $fileName);
            // $request['dokumen_magang_file'] = $fileName;

            $id_mahasiswa = MahasiswaModel::where('user_id', auth()->user()->user_id)->first()->mahasiswa_id;

            $res = DokumenMagangModel::create([
                'mahasiswa_id' => $id_mahasiswa,
                'magang_id' => $request->magang_id,
                'dokumen_magang_nama' => 'PROPOSAL',
                'dokumen_magang_file' => $fileName
            ]);
            return response()->json([
                'stat' => $res,
                'mc' => $res, // close modal
                'msg' => $res ? 'Berhasil upload proposal' : 'Gagal upload proposal'
            ]);
        } else {
            return response()->json([
                'stat' => false,
                'mc' => false,
                'msg' => 'Pilih file terlebih dahulu'
            ]);
        }
    }
    public function upload_surat_balasan(Request $request)
    {
        // dd($request->all());
        $file = $request->file('surat_balasan');
        if ($file) {
            $fileName = 'surat_balasan_' . time() . '.' . $file->getClientOriginalExtension();
            //move to public/assets/
            $file->move(public_path('assets/suratbalasan'), $fileName);
            // $request['dokumen_magang_file'] = $fileName;

            $id_mahasiswa = MahasiswaModel::where('user_id', auth()->user()->user_id)->first()->mahasiswa_id;

            $res = DokumenMagangModel::create([
                'mahasiswa_id' => $id_mahasiswa,
                'magang_id' => $request->magang_id,
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
    }
}

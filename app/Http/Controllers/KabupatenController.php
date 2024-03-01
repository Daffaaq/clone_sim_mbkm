<?php

namespace App\Http\Controllers;

use App\Models\KabupatenModel;
use Illuminate\Http\Request;

class KabupatenController extends Controller
{
    public function getKota(Request $request)
    {
        $provinsi_id = $request->provinsi_id;

        $kabupaten = KabupatenModel::select('id', 'd_provinsi_id', 'nama_kab_kota')->with('provinsi');
        if (isset($request->provinsi_id)) {
            $kabupaten = $kabupaten->where('d_provinsi_id', $provinsi_id)->get();
        } else {
            $kabupaten = $kabupaten->get();
        }


        return response()->json($kabupaten);
    }
}

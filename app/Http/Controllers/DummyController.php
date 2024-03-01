<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DummyController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'MASTER.JURUSAN';
        $this->menuUrl   = url('dummy');     // set URL untuk menu ini
        $this->menuTitle = 'Jurusan';                       // set nama menu
        $this->viewPath  = 'dummy';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        // $this->authAction('read');
        // $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Data Master', 'Jurusan']
        ];

        $activeMenu = [
            'l1' => 'master',
            'l2' => 'master-jurusan',
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl,
            'title' => 'Daftar ' . $this->menuTitle
        ];

        return view($this->viewPath)
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $activeMenu)
            ->with('page', (object) $page)
            ->with('allowAccess', $this->authAccessKey());
    }

    public function list(Request $request)
    {
        // $this->authAction('read', 'json');
        // if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data  = [
            [
                'id' => 1,
                'name' => 'Jurusan 1',
                'code' => 'JRS1',
                'created_at' => '2021-01-01 00:00:00',
                'updated_at' => '2021-01-01 00:00:00',
            ],
            [
                'id' => 2,
                'name' => 'Jurusan 2',
                'code' => 'JRS2',
                'created_at' => '2021-01-01 00:00:00',
                'updated_at' => '2021-01-01 00:00:00',
            ],
            [
                'id' => 3,
                'name' => 'Jurusan 3',
                'code' => 'JRS3',
                'created_at' => '2021-01-01 00:00:00',
                'updated_at' => '2021-01-01 00:00:00',
            ],
        ];

        //make collection
        $data = collect($data);

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }
}

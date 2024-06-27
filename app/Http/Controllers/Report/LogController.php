<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Master\DosenModel;
use App\Models\Setting\UserModel;
use App\Models\Master\ProdiModel;
use App\Imports\MahasiswaImport;
use App\Models\Master\PeriodeModel;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Transaction\KuotaDosenModel;
use App\Models\Transaction\LogModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LogController extends Controller
{
    public function __construct()
    {
        $this->menuCode  = 'LAPORAN.LOG.SISTEM';
        $this->menuUrl   = url('laporan/log-sistem');
        $this->menuTitle = 'Log Sistem';
        $this->viewPath  = 'report.log-sistem.';
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Report', 'Log Sistem']
        ];

        $activeMenu = [
            'l1' => 'report',
            'l2' => 'laporan-logis',
            'l3' => null
        ];

        $page = [
            'url' => $this->menuUrl,
            'title' => 'Daftar ' . $this->menuTitle
        ];
        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
        $data = LogModel::select('t_log.user_id', 't_log.action', 't_log.url', 't_log.data', 's_user.name')
            ->leftJoin('s_user', 't_log.user_id', '=', 's_user.user_id')
            ->whereNull('t_log.deleted_at')
            ->where('t_log.periode_id', $activePeriods)
            ->get();
        $actions = LogModel::select('action')->groupBy('action')->get();
        return view($this->viewPath . 'index')
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $activeMenu)
            ->with('page', (object) $page)
            ->with('actions', $actions)
            ->with('data', $data)
            ->with('allowAccess', $this->authAccessKey());
    }

    public function list(Request $request)
    {
        $this->authAction('read', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $activePeriods = PeriodeModel::where('is_current', 1)->value('periode_id');
        $data = LogModel::select('t_log.user_id', 't_log.action', 't_log.url', 't_log.data', 's_user.name')
            ->leftJoin('s_user', 't_log.user_id', '=', 's_user.user_id')
            ->whereNull('t_log.deleted_at')
            ->where('t_log.periode_id', $activePeriods)
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }
}

<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

/**
 * App\Models\DashboardModel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|DashboardModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DashboardModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DashboardModel query()
 * @mixin \Eloquent
 */
class DashboardModel extends AppModel
{
    /* Untuk default log user activity */
    protected static $_log_scope = 'DASHBOARD';
    protected static $_log_table = '*';
    protected static $_log_action = 'ACCESS';
    protected static $_log_id = 0;

    public function getTotalDepositFailed($merchant_id = 0, int $days_ago = 29){
        $res = DB::select('CALL web_dashboard_total_deposit_failed(?, ?,?)', [
            $merchant_id,
            getDateAgoFromToday($days_ago),
            date('Y-m-d')
        ])[0];

        return $res->deposit_amount;
    }

    public function getTotalDepositPending($merchant_id = 0, int $days_ago = 29){
        $res = DB::select('CALL web_dashboard_total_deposit_pending(?,?,?)', [
            $merchant_id,
            getDateAgoFromToday($days_ago),
            date('Y-m-d')
        ])[0];

        return $res->deposit_amount;
    }

    public function getTotalPotensiDeposit($merchant_id = 0, int $days_ago = 29){
        $res = DB::select('CALL web_dashboard_total_potensi_deposit(?,?,?)', [
            $merchant_id,
            getDateAgoFromToday($days_ago),
            date('Y-m-d')
        ])[0];

        return $res->deposit_amount;
    }

    public function getTotalWithdraw($merchant_id = 0, int $days_ago = 29){
        $res = DB::select('CALL web_dashboard_total_withdraw(?,?,?)', [
            $merchant_id,
            getDateAgoFromToday($days_ago),
            date('Y-m-d')
        ])[0];

        return $res->withdraw_amount;
    }

    public function getRekapitulasiDepositWithdraw($merchant_id = 0, int $days_ago = 29){
        return DB::select('CALL web_dashboard_rekapitulasi_dp_wd(?,?,?)', [
            $merchant_id,
            getDateAgoFromToday($days_ago),
            date('Y-m-d')
        ]);
    }

    public function getTotalDepositSuccess($merchant_id = 0, int $days_ago = 29){
        $res = DB::select('CALL web_dashboard_total_deposit_success(?,?,?)', [
            $merchant_id,
            getDateAgoFromToday($days_ago),
            date('Y-m-d'),
        ])[0];

        return $res->deposit_amount;
    }

    public function getTotalMerchant(){
        $res = DB::select('CALL web_dashboard_total_merchant()')[0];

        return $res->total_merchant;
    }
}

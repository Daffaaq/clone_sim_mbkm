<?php

namespace App\Models\Setting;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupMenuModel extends AppModel
{
    use SoftDeletes;

    protected $table = 's_group_menu';
    protected $primaryKey = 'groupmenu_id';
    protected $uniqueKey = '';

    protected static $_table = 's_group_menu';
    protected static $_primaryKey = 'groupmenu_id';
    protected static $_uniqueKey = '';

    protected $fillable = [
        'group_id',
        'menu_id',
        'c',
        'r',
        'u',
        'd'
    ];


    public static function getMenuMap($group_id, $tag = '', $parent_id = null)
    {
        $map = DB::table('s_menu AS m')
            ->selectRaw('m.menu_id, m.menu_scope, m.menu_url, m.menu_name, m.menu_level, m.order_no, (SELECT COUNT(*) FROM s_menu mm WHERE mm.parent_id = m.menu_id) as sub, gm.c, gm.r, gm.u, gm.d')
            ->leftJoin('s_group_menu AS gm', function ($join) use ($group_id) {
                $join->on('m.menu_id', '=', 'gm.menu_id')
                    ->where('gm.group_id', '=', $group_id)
                    ->whereNull('gm.deleted_at');
            })
            ->where('m.is_active', '=', 1)
            ->orderBy('m.order_no');

        if (empty($parent_id)) {
            $map->where(function ($query) {
                $query->whereNull('m.parent_id')
                    ->orWhere('m.menu_level', '=', 1);
            });
        } else {
            $map->where(function ($query) use ($parent_id) {
                $query->where('m.parent_id', '=', $parent_id)
                    ->where('m.menu_level', '>', 1);
            });
        }

        $data = $map->get();

        // dd($data);

        foreach ($data as $d) {
            if (empty($d->menu_url)) {
                $tag .= '<tr><td><span class="tree-ml-' . $d->menu_level . ' font-weight-bold">' . $d->menu_name . '</span></td>' .
                    '<td class="text-center pr-2">' . strtolower($d->menu_scope) . '</td>' .
                    '<td class="text-center pr-2"><div class="icheck-success d-inline"><input class="r_act" name="' . $d->menu_id . '[r]" value="1" type="checkbox" id="r_' . $d->menu_id . '"  ' . (($d->r) ? 'checked' : '') . '><label for="r_' . $d->menu_id . '"></label></div></td>' .
                    '<td class="text-center pr-2">-</td>' .
                    '<td class="text-center pr-2">-</td>' .
                    '<td class="text-center pr-2">-</td>' .
                    '<td class="text-center pr-2">-</td>' .
                    '</tr>';
            } else {
                $tag .= '<tr><td><span class="tree-ml-' . $d->menu_level . '">' . $d->menu_name . '</span></td>' .
                    '<td class="text-center pr-2">' . strtolower($d->menu_scope) . '</td>' .
                    '<td class="text-center pr-2"><div class="icheck-success d-inline"><input class="r_act" name="' . $d->menu_id . '[r]" value="1" type="checkbox" id="r_' . $d->menu_id . '"  ' . (($d->r) ? 'checked' : '') . '><label for="r_' . $d->menu_id . '"></label></div></td>' .
                    '<td class="text-center pr-2"><div class="icheck-success d-inline"><input class="c_act" name="' . $d->menu_id . '[c]" value="1" type="checkbox" id="c_' . $d->menu_id . '"  ' . (($d->c) ? 'checked' : '') . '><label for="c_' . $d->menu_id . '"></label></div></td>' .
                    '<td class="text-center pr-2"><div class="icheck-success d-inline"><input class="u_act" name="' . $d->menu_id . '[u]" value="1" type="checkbox" id="u_' . $d->menu_id . '"  ' . (($d->u) ? 'checked' : '') . '><label for="u_' . $d->menu_id . '"></label></div></td>' .
                    '<td class="text-center pr-2"><div class="icheck-success d-inline"><input class="d_act" name="' . $d->menu_id . '[d]" value="1" type="checkbox" id="d_' . $d->menu_id . '"  ' . (($d->d) ? 'checked' : '') . '><label for="d_' . $d->menu_id . '"></label></div></td>' .
                    '<td class="text-center pr-2"><div class="icheck-success d-inline"><input class="all_line" value="' . $d->menu_id . '" type="checkbox" id="line_' . $d->menu_id . '"><label for="line_' . $d->menu_id . '"></label></div></td>' .
                    '</tr>';
            }

            $tag = self::getMenuMap($group_id, $tag, $d->menu_id);
        }
        return $tag;
    }

    public static function setGroupMenu($group_id, $request)
    {
        $menu = $request->except(['_token', '_method', 'all_c', 'all_r', 'all_u', 'all_d']);

        $data['deleted_by'] = Auth::user()->user_id;
        $data['deleted_at'] = date('Y-m-d H:i:s');

        self::where('group_id', $group_id)->update($data);

        if (!empty($menu) && is_array($menu)) {
            foreach ($menu as $menu_id => $act) {
                self::updateOrInsert(
                    ['group_id' => $group_id, 'menu_id' => $menu_id],
                    [
                        'c' => isset($act['c']) ? 1 : 0,
                        'r' => isset($act['r']) ? 1 : 0,
                        'u' => isset($act['u']) ? 1 : 0,
                        'd' => isset($act['d']) ? 1 : 0,
                        'deleted_by' => null,
                        'deleted_at' => null,
                        'created_by' => Auth::user()->user_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::user()->user_id,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                );
            }
        }
        return true;
    }
}

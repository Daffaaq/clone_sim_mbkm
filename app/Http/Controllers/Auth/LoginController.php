<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Google;
use App\Models\History\LogActivityModel;
use App\Models\Master\PeriodeModel;
use App\Models\Master\PerusahaanModel;
use App\Models\Proposal\ProposalSemproModel;
use App\Models\Setting\UserModel;
use App\Models\View\MahasiswaProposalView;
use App\Models\View\ProposalSemproView;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // set akun untuk bisa masuk (pilih username/email)
    public function username()
    {
        return 'username';
    }

    //fungsi untuk handle saat tombol login di klik
    public function login(Request $request)
    {
        // validasi username dan password saat akan login
        $validate = $this->validateLogin($request);
        if ($validate->fails()) {
            return response()->json([
                'stat' => false,
                'msg' => 'Terjadi kesalahan',
                'captcha_img' => captcha_img('math'),
                'msgField' => $validate->errors()
            ]);
        }

        // cek login attemp. Jika tidak wajar/melebihi kuota, login di blok beberapa detik
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // validasi login
        $check = $this->attemptLogin($request);
        if ($check === true) {
            return $this->sendLoginResponse($request);
        } else {
            // Tambah hitungan login attemp jika kombinasi username/password salah
            $this->incrementLoginAttempts($request);

            return response()->json([
                'stat' => false,
                'msg' => $check
            ]);
        }
    }

    // fungsi menghendle percobaan login user
    protected function attemptLogin(Request $request)
    {
        $db = UserModel::where('username', '=', $request->input('username'))->first();
        if ($db) {

            //cek akun perusahaan, jika status 2 (ditolak), maka tampilkan keterangan
            // $perusahaan = PerusahaanModel::where('user_id', $db->user_id)->first();
            // if ($perusahaan) {
            //     if ($perusahaan->status == 2) {
            //         return "Perusahaan Anda ditolak dikarenakan $perusahaan->keterangan. Silahkan hubungi admin.";
            //     }
            // }

            if (!$db->is_active) {
                return 'Akun Anda tidak aktif.';
            }


            if (!$this->guard()->attempt(
                $this->credentials($request),
                $request->filled('remember')
            )) {
                return 'Username atau password salah.';
            }

            unset($db->password);
            session()->regenerate();

            // LogActivityModel::setLog($db->user_id, 'login', 'Login ke sistem');

            $this->_getUserMenu($db->group_id);
            $periode = PeriodeModel::where('is_current', 1)->selectRaw('periode_id, periode_nama')->first();

            $this->redirectTo = url('/');

            if ($db->group_id == 3) {         // jika yg login Dosen
                $dosen = $db->getUserDosen;
                session()->put('dosen', $dosen);
            }

            if ($db->group_id == 4) {         // jika yg login Mahasiswa
                $mhs = $db->getUserMahasiswa;
                session()->put('mahasiswa', $mhs);
            }

            if ($db->group_id == 5) {         // jika yg login Mahasiswa
                $mhs = $db->getUserPerusahaan;
                session()->put('perusahaan', $mhs);
            }

            session()->put('periode_active', $periode);
            session()->put('userAccess', $this->userAccess);
            session()->put('userMenu', $this->userMenu);
            session()->put('theme', env('appTheme', 'dark'));
            session()->put('access_token', null);
            return true;
        }
        return 'Kombinasi username dan password salah.';
    }

    // validasi inputan username dan password
    protected function validateLogin(Request $request)
    {
        return Validator::make(
            $request->all(),
            [
                $this->username() => 'required|min:3|string',
                'password' => 'required|string|min:5|max:30',
                'captcha' => 'required|captcha'
            ],
            [
                'validation.captcha' => 'Hasil perhitungan salah',
                'captcha' => 'Hasil perhitungan salah',
            ]
        );
    }

    // kirim respon ke client untuk status yang berhasil login
    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        $this->authenticated($request, $this->guard()->user());

        return response()->json([
            'stat' => true,
            'msg' => 'Login Berhasil. Silahkan tunggu',
            'url' => $this->redirectTo
        ]);
    }

    // kirim respon lock untuk attemp yg gagal berkali-kali
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        return response()->json([
            'stat' => false,
            'waiting_time' => $seconds,
            'msg' => 'Akun ' . $this->username() . ' diblok sementara. Silahkan tunggu ' . $seconds . ' detik untuk login kembali.',
        ]);
    }

    // get menu dinamis
    private function _getUserMenu($group_id, $parent_id = null)
    {
        $menu = DB::table('s_group_menu AS gm')
            ->join('s_menu AS m', 'gm.menu_id', '=', 'm.menu_id')
            ->where('gm.group_id', '=', $group_id)
            ->where('m.is_active', '=', 1)
            ->whereNull('gm.deleted_at')
            ->orderBy('m.order_no');

        if (empty($parent_id)) {
            $menu->where(function ($query) {
                $query->whereNull('m.parent_id')->orWhere('m.menu_level', '=', 1);
            });
        } else {
            $menu->where(function ($query) use ($parent_id) {
                $query->where('m.parent_id', '=', $parent_id)->where('m.menu_level', '>', 1);
            });
        }

        $res = $menu->selectRaw('m.menu_id, m.menu_code, m.menu_name, m.menu_url, m.icon, m.class_tag, m.menu_level, (SELECT COUNT(*) FROM s_menu mm WHERE mm.parent_id = m.menu_id) as sub, gm.c, gm.r, gm.u, gm.d')->get();

        if ($res) {
            foreach ($res as $d) {
                $this->userAccess[strtoupper($d->menu_code)] = ['c' => $d->c, 'r' => $d->r, 'u' => $d->u, 'd' => $d->d];
                if ($d->sub == 0) {
                    $this->userMenu .=  '<li class="nav-item">' .
                        '<a href="' . (empty($d->menu_url) ? '#' : url($d->menu_url)) . '" class="nav-link ' . $d->class_tag . ' l' . $d->menu_level . '">' .
                        '<i class="nav-icon fas ' . $d->icon . ' ' . (($d->menu_level > 1) ? 'text-xs' : '') . '"></i><p>' . $d->menu_name . '</p></a></li>';
                } else {
                    $this->userMenu .=     '<li class="nav-item has-treeview ">' .
                        '<a href="#" class="nav-link ' . $d->class_tag . ' l' . $d->menu_level . '">' .
                        '<i class="nav-icon fas ' . $d->icon . '"></i>' .
                        '<p>' . $d->menu_name . '<i class="fas fa-angle-left right"></i></p></a>' .
                        '<ul class="nav nav-treeview">';

                    $this->_getUserMenu($group_id, $d->menu_id);
                    $this->userMenu .= '</ul>';
                }
            }
        }
    }

    public static function getMenu($group_id, $parent_id = null)
    {
        $menus = '';
        $menu = DB::table('s_group_menu AS gm')
            ->join('s_menu AS m', 'gm.menu_id', '=', 'm.menu_id')
            ->where('gm.group_id', '=', $group_id)
            ->where('m.is_active', '=', 1)
            ->whereNull('gm.deleted_at')
            ->orderBy('m.order_no');

        if (empty($parent_id)) {
            $menu->where(function ($query) {
                $query->whereNull('m.parent_id')->orWhere('m.menu_level', '=', 1);
            });
        } else {
            $menu->where(function ($query) use ($parent_id) {
                $query->where('m.parent_id', '=', $parent_id)->where('m.menu_level', '>', 1);
            });
        }

        $res = $menu->selectRaw('m.menu_id, m.menu_code, m.menu_name, m.menu_url, m.icon, m.class_tag, m.menu_level, (SELECT COUNT(*) FROM s_menu mm WHERE mm.parent_id = m.menu_id) as sub, gm.c, gm.r, gm.u, gm.d')->get();

        if ($res) {
            foreach ($res as $d) {
                // $this->userAccess[strtoupper($d->menu_code)] = ['c' => $d->c, 'r' => $d->r, 'u' => $d->u, 'd' => $d->d];
                if ($d->sub == 0) {
                    $menus .=  '<li class="nav-item">' .
                        '<a href="' . (empty($d->menu_url) ? '#' : url($d->menu_url)) . '" class="nav-link ' . $d->class_tag . ' l' . $d->menu_level . '">' .
                        '<i class="nav-icon fas ' . $d->icon . ' ' . (($d->menu_level > 1) ? 'text-xs' : '') . '"></i><p>' . $d->menu_name . '</p></a></li>';
                } else {
                    $menus .=     '<li class="nav-item has-treeview ">' .
                        '<a href="#" class="nav-link ' . $d->class_tag . ' l' . $d->menu_level . '">' .
                        '<i class="nav-icon fas ' . $d->icon . '"></i>' .
                        '<p>' . $d->menu_name . '<i class="fas fa-angle-left right"></i></p></a>' .
                        '<ul class="nav nav-treeview">';

                    self::getMenu($group_id, $d->menu_id);
                    $menus .= '</ul>';
                }
            }
        }

        return $menus;
    }

    public function logout(Request $request)
    {
        /*if(Auth::check()){
            LogActivityModel::setLog(Auth::user()->user_id, 'logout', 'Logout dari sistem');
        }*/
        $this->guard()->logout();

        Auth::logout();
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson() ? new JsonResponse([], 204) : redirect('/');
    }
}

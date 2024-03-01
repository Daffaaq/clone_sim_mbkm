<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Master\PeriodeModel;
use App\Models\Master\PerusahaanModel;
use App\Models\Setting\UserModel;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [

            // 'username' => ['required', 'string', 'max:100', 'unique:users'],
            // 'name' => ['required', 'string', 'max:100'],
            // 'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
            // 'password' => ['required', 'string', 'min:8', 'confirmed'],

            'username' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'captcha' => 'required|captcha'
        ], [
            'captcha.captcha' => 'Captcha tidak sesuai',
            'captcha.required' => 'Captcha tidak boleh kosong',
            'username.required' => 'Username tidak boleh kosong',
            'username.max' => 'Username maksimal 100 karakter',
            'name.required' => 'Nama tidak boleh kosong',
            'name.max' => 'Nama maksimal 100 karakter',
            'email.required' => 'Email tidak boleh kosong',
            'email.max' => 'Email maksimal 100 karakter',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Password tidak sama dengan konfirmasi password',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return UserModel::create([
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function register_perusahaan()
    {
        return view('auth.register_perusahaan');
    }

    public function register_perusahaan_store(Request $request)
    {
        $validate = $this->validateLogin($request);
        if ($validate->fails()) {
            return response()->json([
                'stat' => false,
                'msg' => 'Terjadi kesalahan',
                'captcha_img' => captcha_img('math'),
                'msgField' => $validate->errors()
            ]);
        }

        $user = UserModel::create([
            'username' => $request->input('username'),
            'name' => $request->input('name'),
            'group_id' => 5,
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'is_active' => 1,
        ]);

        $user = [
            'nama_perusahaan' => $request->input('name'),
            'email' => $request->input('email'),
            'user_id' => $user->user_id,
            'status' => 0,
            'kategori' => '',
            'tipe_industri' => '',
            'alamat' => '',
            'provinsi_id' => 0,
            'kota_id' => 0,
            'profil_perusahaan' => '',
            'website' => '',
        ];

        $res = PerusahaanModel::insert($user);

        if ($user && $res) {
            $check = $this->attemptLogin($request);
            if ($check === true) {
                return $this->sendLoginResponse($request);
            } else {
                return response()->json([
                    'stat' => false,
                    'msg' => $check
                ]);
            }
        } else {
            return response()->json([
                'stat' => false,
                'msg' => 'Register Gagal. Silahkan coba lagi'
            ]);
        }
    }

    protected function validateLogin(Request $request)
    {
        return Validator::make(
            $request->all(),
            [
                'username' => 'required|string|max:100|unique:s_user',
                'name' => 'required|string|max:100',
                'email' => 'required|string|email|max:100|unique:s_user',
                'password' => 'required|string|min:8',
                'captcha' => 'required|captcha'
            ],
            [
                'validation.captcha' => 'Hasil perhitungan salah',
                'captcha' => 'Hasil perhitungan salah',
                'username.required' => 'Username tidak boleh kosong',
                'username.max' => 'Username maksimal 100 karakter',
                'name.required' => 'Nama tidak boleh kosong',
                'name.max' => 'Nama maksimal 100 karakter',
                'email.required' => 'Email tidak boleh kosong',
                'email.max' => 'Email maksimal 100 karakter',
                'password.required' => 'Password tidak boleh kosong',
                'password.min' => 'Password minimal 8 karakter',
                'password.confirmed' => 'Password tidak sama dengan konfirmasi password',
            ]
        );
    }

    protected function sendLoginResponse(Request $request)
    {
        //sent authenticated event
        $this->authenticated($request, $this->guard()->user());

        return response()->json([
            'stat' => true,
            'msg' => 'Register Berhasil. Silahkan tunggu',
            'url' => $this->redirectTo
        ]);
    }

    // fungsi menghendle percobaan login user
    protected function attemptLogin(Request $request)
    {
        $db = UserModel::where('username', '=', $request->username)->first();
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
            $periode = PeriodeModel::where('is_active', 1)->selectRaw('periode_id, semester, tahun_ajar')->first();

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
}

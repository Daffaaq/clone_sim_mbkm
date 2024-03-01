<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\History\LogActivityModel;
use App\Models\KategoriUsaha;
use App\Models\Master\DosenModel;
use App\Models\Master\MahasiswaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{

    public function __construct()
    {

        $this->menuUrl   = url('setting/account');     // set URL untuk menu ini
        $this->menuTitle = 'Akun Pengguna';                       // set nama menu
        $this->viewPath  = 'setting.account.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $user = auth()->user();
        $this->menuCode  = $user->group_id != 1  ? 'SETTING.ACCOUNT.2' :  'SETTING.ACCOUNT';
        $this->authAction('read');
        $this->authCheckDetailAccess();

        // untuk set breadcrumb pada halaman web
        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Akun Pengguna']
        ];

        // untuk set aktif menu pada sidebar
        $activeMenu = [
            'l1' => 'setting-account',              // menu aktif untuk level 1, berdasarkan class yang ada di sidebar
            'l2' => null,              // menu aktif untuk level 2, berdasarkan class yang ada di sidebar
            'l3' => null               // menu aktif untuk level 3, berdasarkan class yang ada di sidebar
        ];

        // untuk set konten halaman web
        $page = [
            'url' => $this->menuUrl,
            'title' => $this->menuTitle
        ];

        return view($this->viewPath . 'index')
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $activeMenu)
            ->with('page', (object) $page)
            ->with('allowAccess', $this->authAccessKey())
            ->with('user', Auth::user());
    }

    public function update_password(Request $request)
    {
        $user = auth()->user();
        $this->menuCode  = $user->group_id == 4  ? 'SETTING.ACCOUNT.2' :  'SETTING.ACCOUNT';
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $user = Auth::user();

            $rules = [
                'password_old' => ['required', function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password))
                        $fail('Password lama yang di-inputkan salah.');
                }],
                'password' => ['required', 'confirmed', 'min:6', 'different:password_old'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'stat'     => false,
                    'mc'       => false, // close modal
                    'msg'      => 'terjadi kesalahan',
                    'msgField' => $validator->errors()
                ]);
            }

            if ($user) {
                try {
                    $user->password = Hash::make($request->password);
                    $user->updated_by = $user->user_id;
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    // LogActivityModel::setLog($user->user_id, 'account.update-password', 'Update Password');

                    return response()->json([
                        'stat'     => true,
                        'mc'       => true, // close modal
                        'msg'      => $this->getMessage('update.success')
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'stat'     => false,
                        'mc'       => false, // close modal
                        'msg'      => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'stat'     => false,
                'mc'       => false, // close modal
                'msg'      => $this->getMessage('data.notfound')
            ]);
        }

        return redirect('/');
    }


    public function update_avatar(Request $request)
    {
        $user = auth()->user();
        $this->menuCode  = $user->group_id == 4  ? 'SETTING.ACCOUNT.2' :  'SETTING.ACCOUNT';
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'image' => 'required|image|mimes:jpeg,png,jpg|max:125',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'stat'     => false,
                    'mc'       => false, // close modal
                    'msg'      => 'terjadi kesalahan',
                    'msgField' => $validator->errors()
                ]);
            }

            $user = Auth::user();
            if ($user) {
                try {

                    if (!empty($user->avatar_dir)) {
                        Storage::disk('public')->delete($user->avatar_dir);
                    }

                    $imgName = time() . '-' . uniqid() . '.' . $request->image->extension();
                    Storage::disk('public')->put('avatar/' . $imgName, $request->file('image')->get());

                    $user->avatar_url = Storage::url('avatar/' . $imgName);
                    $user->avatar_dir = 'avatar/' . $imgName;
                    $user->updated_by = $user->user_id;
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    if ($user->group_id == 3) {
                        DosenModel::where('dosen_id', getDosenID())->update([
                            'updated_by' => $user->user_id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'photo_dir' => $user->avatar_dir,
                            'photo_url' => $user->avatar_url
                        ]);
                    }

                    if ($user->group_id == 4) {
                        MahasiswaModel::where('mahasiswa_id', getMahasiswaID())->update([
                            'updated_by' => $user->user_id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'photo_dir' => $user->avatar_dir,
                            'photo_url' => $user->avatar_url
                        ]);
                    }

                    // LogActivityModel::setLog($user->user_id, 'account.update-avatar', 'Update Avatar');

                    return response()->json([
                        'stat'     => true,
                        'mc'       => true, // close modal
                        'msg'      => $this->getMessage('update.success'),
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'stat'     => false,
                        'mc'       => false, // close modal
                        'msg'      => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'stat'     => false,
                'mc'       => false, // close modal
                'msg'      => $this->getMessage('data.notfound')
            ]);
        }

        return redirect('/');
    }
}

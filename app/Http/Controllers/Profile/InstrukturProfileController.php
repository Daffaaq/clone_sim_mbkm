<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\KategoriUsaha;
use App\Models\Master\BidangModel;
use App\Models\Master\DosenBidangModel;
use App\Models\Master\DosenModel;
use App\Models\Master\InstrukturModel;
use App\Models\Master\JabatanModel;
use App\Models\Master\PangkatModel;
use App\Models\View\DosenView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InstrukturProfileController extends Controller
{

    public function __construct()
    {
        $this->menuCode  = 'INSTRUKTUR.PROFILE';
        $this->menuUrl   = url('instruktur/profile');     // set URL untuk menu ini
        $this->menuTitle = 'Profil Instruktur';                       // set nama menu
        $this->viewPath  = 'profile.instruktur.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        // untuk set breadcrumb pada halaman web
        $breadcrumb = [
            'title' => $this->menuTitle,
            'list'  => ['Data Profile']
        ];

        // untuk set aktif menu pada sidebar
        $activeMenu = [
            'l1' => 'profile',              // menu aktif untuk level 1, berdasarkan class yang ada di sidebar
            'l2' => null,              // menu aktif untuk level 2, berdasarkan class yang ada di sidebar
            'l3' => null               // menu aktif untuk level 3, berdasarkan class yang ada di sidebar
        ];

        // untuk set konten halaman web
        $page = [
            'url' => $this->menuUrl,
            'title' => $this->menuTitle
        ];
        $instruktur = InstrukturModel::where('instruktur_id', getinstrukturID())->first();
        // dd($instruktur);

        return (!$instruktur) ? $this->showPageNotFound() :
            view($this->viewPath . 'index')
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $activeMenu)
            ->with('page', (object) $page)
            ->with('allowAccess', $this->authAccessKey())
            ->with('instruktur', $instruktur);
    }


    public function update(Request $request)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $user = Auth::user();
            $rules = [
                'nama_instruktur' => 'required',
                'instruktur_email' => 'required|email',
                'instruktur_phone' => 'required',
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

                    $user->name = $request->nama_instruktur;
                    $user->email = $request->instruktur_email;
                    $user->updated_by = $user->user_id;
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    InstrukturModel::updateData(getinstrukturID(), $request);

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

    public function update_password(Request $request)
    {
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {

            $user = Auth::user();

            $rules = [
                'password_old' => ['required', function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password))
                        $fail('The ' . $attribute . ' is invalid.');
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
        $this->authAction('update', 'json');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'image' => 'required|image',
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

                    $user->avatar_url = asset(Storage::url('avatar/' . $imgName));
                    $user->avatar_dir = 'avatar/' . $imgName;
                    $user->updated_by = $user->user_id;
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

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

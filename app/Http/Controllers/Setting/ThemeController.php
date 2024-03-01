<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\KategoriUsaha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ThemeController extends Controller
{

    public function __construct()
    {
        $this->menuUrl   = url('setting/theme');     // set URL untuk menu ini
        $this->menuTitle = 'User Profile';                       // set nama menu
        $this->viewPath  = 'setting.profile.';         // untuk menunjukkan direktori view. Diakhiri dengan tanda titik
    }

    public function index($theme = '')
    {
        switch (strtolower($theme)) {
            case 'dark':
                $theme = 'dark';
                break;
            case 'light':
                $theme = 'light';
                break;
            default:
                $theme = 'dark';
                break;
        }
        session()->put('theme', $theme);

        return redirect()->back();
    }
}

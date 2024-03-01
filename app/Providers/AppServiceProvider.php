<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::share('breadcrumb', (object) ['title' => 'Default Title Breadcrumb', 'list' => ['App','Home']]);
        View::share('activeMenu', (object) ['l1' => 'dashboard', 'l2' => null, 'l3' => null]);

        view()->composer('*', function ($view) {
            $theme = new \StdClass();
            if(session()->get('theme') == 'light'){
                $theme->mode = 'light-mode';
                $theme->sidebar = 'sidebar-light-success';
                $theme->sidebar_navbar = 'navbar-success';
                $theme->navbar = 'navbar-success';
                $theme->card_outline = 'success';
                $theme->button = 'success';
                $theme->color = 'success';
            }else{
                $theme->mode = 'dark-mode';
                $theme->sidebar = 'sidebar-dark-primary';
                $theme->sidebar_navbar = 'navbar-dark';
                $theme->navbar = 'navbar-dark';
                $theme->card_outline = 'dark';
                $theme->button = 'primary';
                $theme->color = 'primary';
            }
            $view->with('theme', $theme);
        });
    }
}

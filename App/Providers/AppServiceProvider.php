<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Sondage;
use Carbon\Carbon;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /*Bootstrap any application services.*/
    public function boot(): void
    {
      Schema::defaultStringLength(191);
        
       View::composer('*', function ($view) {
        $sondagesActifsCount = Sondage::where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', Carbon::now());
        })->count();

        // 🔹 Partage avec toutes les vues (layouts, dashboard, etc.)
        $view->with('sondagesActifsCount', $sondagesActifsCount);
    });
    }
}

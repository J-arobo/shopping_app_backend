<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env('PASSPORT_PRIVATE_KEY') && env('PASSPORT_PUBLIC_KEY')) {
            file_put_contents(storage_path('oauth-private.key'), env('PASSPORT_PRIVATE_KEY'));
            file_put_contents(storage_path('oauth-public.key'), env('PASSPORT_PUBLIC_KEY'));
        }
    }
}

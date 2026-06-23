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
        if (env('PASSPORT_PRIVATE_KEY_B64') && env('PASSPORT_PUBLIC_KEY_B64')) {
            file_put_contents(storage_path('oauth-private.key'), str_replace("\r\n", "\n", base64_decode(env('PASSPORT_PRIVATE_KEY_B64'))));
            file_put_contents(storage_path('oauth-public.key'), str_replace("\r\n", "\n", base64_decode(env('PASSPORT_PUBLIC_KEY_B64'))));
        }
    }
}

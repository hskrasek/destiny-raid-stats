<?php

namespace App\Providers;

use App\Destiny1\Client as Destiny1Client;
use App\Destiny2\Client as Destiny2Client;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(Destiny1Client::class)
            ->needs(Client::class)
            ->give(function () {
                return new Client([
                    'base_uri' => 'https://www.bungie.net/d1/platform/Destiny/',
                    'headers'  => ['X-API-Key' => config('services.destiny.key')],
                ]);
            });

        $this->app->when(Destiny2Client::class)
            ->needs(Client::class)
            ->give(function () {
                return new Client([
                    'base_uri' => 'https://www.bungie.net/Platform/Destiny2/',
                    'headers'  => ['X-API-Key' => config('services.destiny.key')],
                ]);
            });
    }
}

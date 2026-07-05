<?php

namespace App\Providers;

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\ServiceProvider;
use Tymon\JWTAuth\Http\Parser\AuthHeaders;
use Tymon\JWTAuth\Http\Parser\Cookies;
use Tymon\JWTAuth\Http\Parser\InputSource;
use Tymon\JWTAuth\Http\Parser\QueryString;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // El JWT viaja como cookie HttpOnly; se mantiene el header como
        // fallback para clientes que ya posean un token (ej. tests).
        $this->app['tymon.jwt.parser']->setChain([
            (new Cookies(false))->setKey(AuthController::TOKEN_COOKIE),
            new AuthHeaders,
            new QueryString,
            new InputSource,
        ]);
    }
}

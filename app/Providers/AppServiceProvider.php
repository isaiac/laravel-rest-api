<?php

namespace App\Providers;

use Dingo\Api\Exception\Handler as ExceptionHandler;
use Dingo\Api\Http\Response\Factory as Response;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Laravel\Sanctum\Exceptions\MissingScopeException;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Sanctum::ignoreMigrations();

        $this->registerExceptionHandlers();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (in_array(config('app.env'), ['production', 'testing'])) {
            URL::forceScheme('https');
        }

        JsonResource::withoutWrapping();
    }

    /**
     * Register all the application exception handlers.
     *
     * @return void
     */
    protected function registerExceptionHandlers()
    {
        app(ExceptionHandler::class)->register(function (Exception $e) {
            if ($e instanceof AuthenticationException) {
                return app(Response::class)->errorUnauthorized();
            }

            if ($e instanceof MissingAbilityException
                || $e instanceof MissingScopeException
            ) {
                return app(Response::class)->errorForbidden($e->getMessage());
            }
        });
    }
}

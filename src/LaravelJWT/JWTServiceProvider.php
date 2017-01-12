<?php
namespace LaravelJWT;

use Illuminate\Support\ServiceProvider;
use LaravelJWT\Http\Middleware\JWTMiddleware;
use JWT;

class JWTServiceProvider extends ServiceProvider
{
  public function boot() {
    $this->app['router']->middleware('JWT', JWTMiddleware::class);

    $this->publishes([__DIR__ . '/config/jwt.php' => config_path('jwt.php')], 'config');
  }

  public function register() {
    $this->app->singleton('JWT', function () {
      return new JWT(config('jwt'));
    });
  }
}
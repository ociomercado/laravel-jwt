<?php
/**
 * Laravel-JWT - A simple Laravel package to work with JWT
 * Author: Miguel Ángel Villagrá
 * Organization: OcioMercado
 */

namespace OcioMercado\LaravelJWT;

use Illuminate\Support\ServiceProvider;
use OcioMercado\LaravelJWT\Http\Middleware\JWTMiddleware;
use OcioMercado\LaravelJWT\JWT;

class JWTServiceProvider extends ServiceProvider
{
  public function boot() {
    if (method_exists($this->app['router'], 'middleware')) {
      $this->app['router']->middleware('JWT', JWTMiddleware::class);
    } elseif (method_exists($this->app['router'], 'aliasMiddleware')) {
      $this->app['router']->aliasMiddleware('JWT', JWTMiddleware::class);
    }

    $this->publishes([__DIR__ . '/config/jwt.php' => config_path('jwt.php')], 'config');
  }

  public function register() {
    $this->app->singleton('JWT', function () {
      return new JWT($this->app['request']);
    });
  }
}
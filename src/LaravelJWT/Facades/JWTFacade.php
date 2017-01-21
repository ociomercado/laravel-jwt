<?php
/**
 * Laravel-JWT - A simple Laravel package to work with JWT
 * Author: Miguel Ángel Villagrá
 * Organization: OcioMercado
 */

namespace OcioMercado\LaravelJWT\Facades;

use Illuminate\Support\Facades\Facade;

class JWTFacade extends Facade
{
  protected static function getFacadeAccessor() {
    return 'JWT';
  }
}
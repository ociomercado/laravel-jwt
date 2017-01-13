<?php
namespace OcioMercado\LaravelJWT\Facades;

use Illuminate\Support\Facades\Facade;

class JWTFacade extends Facade
{
  protected static function getFacadeAccessor() {
    return 'JWT';
  }
}
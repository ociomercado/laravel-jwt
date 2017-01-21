<?php
/**
 * Laravel-JWT - A simple Laravel package to work with JWT
 * Author: Miguel Ángel Villagrá
 * Organization: OcioMercado
 */

namespace OcioMercado\LaravelJWT\Exceptions;

class TokenExpiredException extends JWTException
{
  public function __construct($refreshableToken = null) {
    parent::__construct('Token has expired.', 401);
  }
}
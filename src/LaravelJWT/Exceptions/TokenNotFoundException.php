<?php
/**
 * Laravel-JWT - A simple Laravel package to work with JWT
 * Author: Miguel Ángel Villagrá
 * Organization: OcioMercado
 */

namespace OcioMercado\LaravelJWT\Exceptions;

class TokenNotFoundException extends JWTException
{
  public function __construct() {
    parent::__construct('Token not found.', 403);
  }
}
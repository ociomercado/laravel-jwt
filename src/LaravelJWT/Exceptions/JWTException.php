<?php
/**
 * Laravel-JWT - A simple Laravel package to work with JWT
 * Author: Miguel Ángel Villagrá
 * Organization: OcioMercado
 */

namespace OcioMercado\LaravelJWT\Exceptions;

use Exception;

class JWTException extends Exception
{
  private $status;

  public function __construct($message = 'An error ocurred.', $status = null) {
    parent::__construct($message);

    if (!is_null($status)) {
      self::setStatus($status);
    }
  }

  private function setStatus($status) {
    $this->status = $status;
  }

  public function getStatus() {
    return $this->status;
  }
}
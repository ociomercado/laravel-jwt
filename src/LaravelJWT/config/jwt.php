<?php
/**
 * Laravel-JWT - A simple Laravel package to work with JWT
 * Author: Miguel Ángel Villagrá
 * Organization: OcioMercado
 */

return [
  /**
    * Identifies the principal that issued the JWT.
    * Use of this claim is OPTIONAL.
    */
  'iss' => env('APP_URL', 'http://localhost'),

  /**
    * Identifies the principal that is the subject of the JWT.
    * Use of this claim is OPTIONAL.
    */
  'sub' => null,

  /**
    * Identifies the principal that is the subject of the JWT.
    * Use of this claim is OPTIONAL.
    */
  'aud' => null,

  /**
    * Identifies the expiration time on or after which the JWT MUST NOT be accepted for processing.
    * Use of this claim is OPTIONAL.
    */
  'exp' => 3600,

  /**
    * Time before which the JWT MUST NOT be accepted for processing.
    * Use of this claim is OPTIONAL.
    */
  'nbf' => 0,

  /**
    * Key used to sign the JWT Claims Set with the specified algorithm ('alg' claim).
    * Used when 'privateKeyPath' is null.
    */
  'key' => env('APP_KEY'),

  /**
    * Private keychain used to sign the JWT Claims Set with RSA or ECDSA.
    * When null, 'key' is used instead.
    * E.g. 'file://path/to/public/key'
    */
  'privateKeyPath' => null,

  /**
    * Public keychain used to verify the JWT Claims Set with RSA or ECDSA.
    * Not used if 'privateKeyPath' is null.
    * E.g. 'file://path/to/public/key'
    */
  'publicKeyPath' => null,

  /**
    * Name of the header key in the request, that has the JWT.
    */
  'headerKey' => 'Authorization',

  /**
    * Name of the request key in the request, that has the JWT.
    * A fallback in case that the token isn't found in the headers.
    */
  'requestKey' => 'token',

  /**
    * Redirect route to send failed checks in the middleware that doesn't want JSON as response.
    */
  'redirect' => '/'
];
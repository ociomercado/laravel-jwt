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
    * Value in milliseconds.
    * Use of this claim is OPTIONAL.
    */
  'exp' => 3600,

  /**
    * Time before which the JWT MUST NOT be accepted for processing.
    * Value in milliseconds.
    * Use of this claim is OPTIONAL.
    */
  'nbf' => 0,

  /**
    * The time limit in milliseconds that allows a token to be refreshed.
    * Defaults to 2 weeks.
    */
  'refreshTTL' => 3600 * 24 * 15,

  /**
    * Key used to sign the JWT Claims Set with the specified algorithm ('alg' claim).
    * Used when 'privateKeyPath' is null.
    */
  'key' => env('APP_KEY', 'app-key'),

  /**
    * Private keychain used to sign the JWT Claims Set with RSA or ECDSA.
    * When null, 'key' is used instead.
    * E.g. 'file://path/to/public/key'
    */
  'privateKeyPath' => null,

  /**
    * Public keychain used to verify the JWT Claims Set with RSA or ECDSA.
    * Not used if 'privateKeyPath' is null or 'firebasePublicKeys' is true.
    * E.g. 'file://path/to/public/key'
    */
  'publicKeyPath' => null,

  /**
    * If set to true, it will use Firebase public keys to verify the token.
    * Not used if 'privateKeyPath' is null.
    */
  'useFirebasePublicKeys' => false,

  /**
    * Required for Firebase token verification.
    * Must be your Firebase project ID, the unique identifier for your Firebase project, which can be found in the URL of that project's console.
    * Not used if 'useFirebasePublicKeys' is false.
    */
  'firebaseProject' => null,

  /**
    * URL where the Firebase public keys are located.
    * Not used if 'privateKeyPath' is null or 'useFirebasePublicKeys' is false.
    */
  'firebasePublicKeysURL' => 'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com',

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
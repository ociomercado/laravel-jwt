# laravel-jwt
A simple Laravel package that implements a `Provider`, `Middleware` and `Facade` for `JWT` using [lcobucci/jwt](https://github.com/lcobucci/jwt) library to generate and check the tokens.

## Dependencies
This library requires:
* PHP 5.5+
* OpenSSL Extension

## Installation
Using composer:
```
composer require ociomercado/laravel-jwt
```

## Configuration

### Provider
You need to update your `config/app.php` file and add the following code in the `providers` section:
```php
  'providers' => [
    // Other providers

      OcioMercado\LaravelJWT\JWTServiceProvider::class,

    // Other providers
  ]
```

### Alias
Also, you need to add the following in the `aliases` section:
```php
  'aliases' => [
    // Other aliases

      'JWT' => OcioMercado\LaravelJWT\Facades\JWTFacade::class,

    // Other aliases
  ]
```

### Config file
Then you need to publish the configuration file so you customize the options:
```
php artisan vendor:publish
```

This will create the config file `jwt.php` in the `/config` folder. Don't forget to check it out and change the options as you need.

## Using the library

### Protecting routes
Now you can use the `JWT` middleware to protect your routes:
```php
  Route::get('/user', function (Request $request) {
    return 'Route secured!';
  })->middleware('JWT');
```
The `middleware` checks if the `request` has a `Authorization` header or the parameter `token` sent via `GET` or `POST`.

### The `JWT` class
```php
  /**
   * Creates and signs a new JWT.
   *
   * It signs the token with the configured type of key in the jwt.php file.
   *
   * @param string $jti A unique identifier for the token.
   * @param mixed[] $customClaims Optional data to append to the token.
   *
   * @return Lcobucci\JWT\Token
   */
  public function createToken($jti = null, $customClaims = null)
```

```php
  /**
   * Validates and verifies a JWT.
   *
   * It verfies the token with the configured type of key in the jwt.php file.
   *
   * @param string $token The token.
   *
   * @return array Returns an array with the results.
   */
  public function verifyToken($token)
```
<?php
/**
 * Laravel-JWT - A simple Laravel package to work with JWT
 * Author: Miguel Ángel Villagrá
 * Organization: OcioMercado
 */

namespace OcioMercado\LaravelJWT\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use OcioMercado\LaravelJWT\Exceptions\TokenNotFoundException;
use OcioMercado\LaravelJWT\Exceptions\TokenExpiredException;
use OcioMercado\LaravelJWT\Exceptions\InvalidTokenException;
use OcioMercado\LaravelJWT\Exceptions\InvalidTokenSignException;

class JWTMiddleware
{
  /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @param  string|null  $guard
    * @return mixed
    */
  public function handle(Request $request, Closure $next, $guard = null) {
    $config = config('jwt');
    $response = $next($request);

    try {
      $token = app('JWT')->verifyToken();
    } catch (TokenNotFoundException $e) {
      if ($request->ajax() || $request->wantsJson()) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], $e->getStatus());
      } else {
        return redirect($config['redirect']);
      }
    } catch (InvalidTokenException $e) {
      if ($request->ajax() || $request->wantsJson()) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], $e->getStatus());
      } else {
        return redirect($config['redirect']);
      }
    } catch (InvalidTokenSignException $e) {
      if ($request->ajax() || $request->wantsJson()) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], $e->getStatus());
      } else {
        return redirect($config['redirect']);
      }
    }

    try {
      app('JWT')->tokenExpired();
    } catch (TokenExpiredException $e) {
      if (!app('JWT')->isRefreshableToken()) {
        if ($request->ajax() || $request->wantsJson()) {
          return response()->json(['success' => false, 'error' => $e->getMessage()], $e->getStatus());
        } else {
          return redirect($config['redirect']);
        }
      } else {
        $jti = null;

        if ($token->hasClaim('jti')) {
          $jti = $token->getClaim('jti');
        }

        $ccKeys = null;
        $customClaims = [];

        if ($token->hasClaim('customClaims')) {
          $ccKeys = explode(',', $token->getClaim('customClaims'));

          foreach ($ccKeys as $cck) {
            if ($token->hasClaim($cck)) {
              $customClaims[$cck] = $token->getClaim($cck);
            }
          }
        }

        $token = app('JWT')->createToken($jti, $customClaims);
      }
    }

    return $response->header('Token', $token->__toString());
  }
}
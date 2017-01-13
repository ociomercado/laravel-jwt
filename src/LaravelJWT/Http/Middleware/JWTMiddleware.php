<?php
namespace OcioMercado\LaravelJWT\Http\Middleware;

use Closure;

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
  public function handle($request, Closure $next, $guard = null) {
    $config = config('jwt');
    
    $headerToken = $request->header($config['headerKey']);
    $requestToken = $request->get($config['requestKey']);

    if (!isset($headerToken) && !isset($requestToken)) {
      if ($request->ajax() || $request->wantsJson()) {
        return response()->json(['success' => false, 'error' => 'Unauthorized.'], 403);
      } else {
        return redirect($config['redirect']);
      }
    }

    $token = isset($headerToken) ? $headerToken : $requestToken;
    $token = explode('Bearer ', $token)[1];

    $result = app('JWT')->verifyToken($token);

    if ($result['success'] === false) {
      $code = $result['code'];
      unset($result['code']);

      if ($request->ajax() || $request->wantsJson()) {
        return response()->json($result, $code);
      } else {
        return redirect($config['redirect']);
      }
    }

    $response = $next($request);
    $token = $result['token'];
    $iat = $token->getClaim('iat');
    $time = time();

    if ($time < ($iat + $config['exp'])) {
      if ($request->ajax() || $request->wantsJson()) {
        return $response->header('Token', $token->__toString());
      } else {
        return redirect($config['redirect']);
      }
    }

    $jti = null;

    if ($token->hasClaim('jti')) {
      $jti = $token->getClaim('jti');
    }

    $newToken = app('JWT')->createToken($jti);

    return $response->header('Token', $newToken->__toString());
  }
}
<?php
namespace LaravelJWT\Middleware;

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

    $result = app('JWT')->verifyToken($token);

    if ($result['success'] === false) {
      $code = $result['code'];
      unset($result['code']);

      if ($request->ajax() || $request->wantsJson()) {
        return $next($request)->json($result, $code);
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
        return $response->json(['token', $token->__toString()]);
      } else {
        return redirect($config['redirect']);
      }
    }

    $jti = null;

    if ($token->hasClaim('jti')) {
      $jti = $token->getClaim('jti');
    }

    $newToken = app('JWT')->createToken($jti);

    if ($request->ajax() || $request->wantsJson()) {
      return $response->json(['token', $newToken->__toString()]);
    } else {
      $response->cookie("token", $newToken->__toString());
    }
  }
}
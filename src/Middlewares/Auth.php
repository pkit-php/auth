<?php

namespace Pkit\Middlewares;

use Pkit\Auth\Session;
use Pkit\Auth\Jwt;
use DateTime;
use ReflectionMethod;

class Auth
{
  public function __invoke($request, $next, $params)
  {
    if (!is_array($params))
      if (is_null($params))
        $params = [];
      else
        $params = is_array($params) ? $params : [$params];
    $isGeneric = count($params) > 1;

    if (empty($params)) {
      $params = ["Session", "JWT"];
      $isGeneric = true;
    }

    $lastTh = null;
    foreach ($params as $auth) {
      $return = $this->tryAuth(
        fn() => (new ReflectionMethod($this, "authBy" . $auth))
          ->invoke($this, $request, $next, $isGeneric),
        $err
      );
      if (is_null($err))
        return $return;
      else
        $lastTh = $err;
    }
    throw $lastTh;
  }

  private function tryAuth($auth, &$err)
  {
    try {
      return $auth();
    } catch (\Throwable $th) {
      if (
        $th->getFile() != __FILE__
        || $th->getTrace()[0]["function"] == "__construct"
      ) {
        throw $th;
      }
      $err = $th;
    }
  }

  private static function throwUserForbidden(bool $expired, string|null $authType = null)
  {
    if ($expired)
      throw new \Exception(($authType ? $authType : "Auth") . " Expired", 403);
    else
      throw new \Exception("User Unauthenticated", 403);
  }

  private function authBySession($request, $next, $isGeneric)
  {
    $authType = $isGeneric ? null : "Session";
    if (!Session::logged()) {
      Session::logout();
      throw new \Exception("User Unauthorized", 401);
    }

    if (Session::expired()) {
      self::throwUserForbidden(true, $authType);
    }

    return $next($request);
  }



  private function authByJWT($request, $next, $isGeneric)
  {
    $token = Jwt::parseBearer($request->headers["Authorization"]);
    $authType = $isGeneric ? null : "JWT";
    if (!$token || !Jwt::validate($token))
      throw new \Exception("User Unauthorized", 401);

    if (Jwt::isExpired($token)) {
      self::throwUserForbidden(true, $authType);
    }
    return $next($request);
  }
}
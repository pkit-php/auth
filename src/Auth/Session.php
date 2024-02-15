<?php

namespace Pkit\Auth;

use DateTime;
use Pkit\Auth\Session\SessionEnv;

class Session extends SessionEnv
{
  private static function start()
  {
    if (session_status() != PHP_SESSION_ACTIVE) {
      session_save_path(self::getPath());
      session_start();
      if (self::getTime() && !key_exists('created', $_SESSION)) {
        $_SESSION['created'] = (new DateTime())->format('Y-m-d H:i:s');
        setcookie(session_name(), session_id(), [
          'expires' => (time() + self::getTime()),
          'path' => '/',
          'httponly' => true, // or false
        ]);
      }
    }
  }

  public static function logged(): bool
  {
    self::start();
    return !is_null(@$_SESSION['payload']);
  }

  public static function expired() {
    Session::start();
    $expire = Session::getTime();
    if ($expire > 0) {
      $created = Session::getCreated();
      $interval =
        (new DateTime('now'))->getTimestamp() -
        (new DateTime($created))->getTimestamp();
      if ($interval > $expire) {
        Session::logout();
        return false;
      }
    }
    return true;
  }

  public static function login(mixed $payload)
  {
    self::start();
    $_SESSION['payload'] = $payload;
  }

  public static function logout()
  {
    self::start();
    setcookie(session_name());
    session_destroy();
  }

  public static function getSession(): mixed
  {
    self::start();
    return key_exists("payload", $_SESSION) ? $_SESSION['payload'] : null;
  }

  public static function getCreated(): string
  {
    self::start();
    return $_SESSION['created'];
  }
}
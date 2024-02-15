<?php

namespace Pkit\Auth\Session;

class SessionEnv
{
    public static function config(int $time = 0, ?string $path = null)
    {
        putenv("SESSION_TIME=$time");
        if ($path !== null) {
            putenv("SESSION_PATH=$path");
        }
    }

    public static function getTime(): int
    {
        return (int) getenv("SESSION_TIME") ?: 0;
    }

    public static function getPath(): string
    {
        return getenv("SESSION_PATH") ?: session_save_path();
    }
}
